<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Murid;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Surat;
use App\Models\TargetHafalan;
use App\Models\SetoranHafalan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MuridController extends Controller
{
    /**
     * Helper: Dapatkan Tahun Akademik aktif (dari session atau DB).
     */
    private function getActiveTahunAkademik(): ?\App\Models\TahunAkademik
    {
        $activeYearId = session('active_tahun_akademik_id');
        if ($activeYearId) {
            $tahun = \App\Models\TahunAkademik::find($activeYearId);
            if ($tahun) {
                return $tahun;
            }
        }

        return \App\Models\TahunAkademik::where('is_aktif', true)->first() ?? \App\Models\TahunAkademik::orderBy('tanggal_mulai', 'desc')->first();
    }

    /**
     * Daftar Murid (Figma Node 393:4622)
     */
    public function index(Request $request)
    {
        $perPage  = (int) $request->get('per_page', 10);
        $search   = $request->get('search', '');
        $searchNisn = $request->get('nisn', '');
        $searchKelas = $request->get('kelas', '');

        $activeTahun = $this->getActiveTahunAkademik();
        $activeYearId = $activeTahun ? $activeTahun->id_tahun_akademik : null;

        // Pastikan setiap user dengan role Murid memiliki record di tabel murid
        $orphanMuridUsers = User::where('role', 'Murid')->whereDoesntHave('murid')->get();
        foreach ($orphanMuridUsers as $orphan) {
            Murid::create([
                'id_user' => $orphan->id_user,
                'status'  => 'Aktif',
            ]);
        }

        // Ambil kelas hanya untuk tahun akademik aktif
        $kelasList = Kelas::when($activeYearId, fn($q) => $q->where('id_tahun_akademik', $activeYearId))
            ->orderBy('nama_kelas')
            ->get();
        $kelasIds = $kelasList->pluck('id_kelas')->toArray();

        $query = Murid::with(['user', 'kelas'])
            ->join('users', 'murid.id_user', '=', 'users.id_user')
            ->select('murid.*');

        // Filter murid: kelas tahun akademik aktif atau yang belum memiliki kelas (dibuat admin)
        if ($searchKelas) {
            if ($searchKelas === 'belum') {
                $query->whereNull('murid.id_kelas');
            } else {
                $query->where('murid.id_kelas', $searchKelas);
            }
        } elseif ($activeYearId) {
            $query->whereIn('murid.id_kelas', $kelasIds);
        }

        if ($search) {
            $query->where('users.nama', 'like', '%' . $search . '%');
        }
        if ($searchNisn) {
            $query->where('murid.nisn', 'like', '%' . $searchNisn . '%');
        }

        $muridList = $query->orderBy('users.nama', 'asc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Ambil semua pengguna murid (yang dibuat oleh admin) untuk pencarian live / autocomplete
        $availableMuridList = User::where('role', 'Murid')
            ->with(['murid.kelas'])
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($u) {
                return [
                    'id_user'    => $u->id_user,
                    'id_murid'   => $u->murid->id_murid ?? null,
                    'nama'       => $u->nama,
                    'email'      => $u->email,
                    'nisn'       => $u->murid->nisn ?? '',
                    'no_hp'      => $u->murid->no_hp ?? '',
                    'id_kelas'   => $u->murid->id_kelas ?? null,
                    'nama_kelas' => $u->murid->kelas->nama_kelas ?? null,
                ];
            });

        return view('siswa.index', compact('muridList', 'kelasList', 'perPage', 'availableMuridList'));
    }

    /**
     * Simpan murid baru (bisa pilih dari pengguna admin atau buat baru).
     */
    public function store(Request $request)
    {
        $idUser = $request->input('id_user');

        $rules = [
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'\-]+$/'],
            'email'    => 'required|email|max:100|unique:users,email' . ($idUser ? (',' . $idUser . ',id_user') : ''),
            'password' => $idUser ? 'nullable|string|min:6' : 'required|string|min:6',
            'nisn'     => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'no_hp'    => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'foto'     => 'nullable|image|max:2048',
        ];

        $request->validate($rules, [
            'nama.regex'        => 'Nama hanya boleh berisi huruf dan spasi.',
            'nisn.regex'        => 'NISN hanya boleh berisi angka.',
            'no_hp.regex'       => 'No. HP hanya boleh berisi angka.',
            'email.unique'      => 'Email sudah terdaftar.',
            'id_kelas.required' => 'Pilih kelas terlebih dahulu.',
        ]);

        $tahunAktif = $this->getActiveTahunAkademik();
        if (!$tahunAktif || !$tahunAktif->is_aktif) {
            return redirect()->route('siswa.gagal')
                ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        $kelas = Kelas::with('tahunAkademik')->find($request->id_kelas);
        if ($kelas && $kelas->tahunAkademik && !$kelas->tahunAkademik->is_aktif) {
            return redirect()->route('siswa.gagal')
                ->with('error', 'Tahun akademik kelas ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        try {
            if ($request->filled('nisn')) {
                $nisnExists = Murid::where('nisn', $request->nisn)
                    ->when($idUser, fn($q) => $q->where('id_user', '!=', $idUser))
                    ->exists();
                if ($nisnExists) {
                    return redirect()->route('siswa.gagal')->with('error', 'NISN ' . $request->nisn . ' sudah terdaftar pada murid lain.');
                }
            }

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('murid', 'public');
            }

            if ($idUser) {
                // User sudah ada dari Pengguna yang dibuat Admin
                $user = User::findOrFail($idUser);
                $userData = [
                    'nama'  => $request->nama,
                    'email' => $request->email,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);

                $murid = Murid::where('id_user', $idUser)->first();
                if ($murid) {
                    $muridData = [
                        'id_kelas' => $request->id_kelas,
                        'status'   => 'Aktif',
                    ];
                    if ($request->filled('nisn')) {
                        $muridData['nisn'] = $request->nisn;
                        $muridData['qr_code'] = $request->nisn;
                    }
                    if ($request->filled('no_hp')) {
                        $muridData['no_hp'] = $request->no_hp;
                    }
                    if ($fotoPath) {
                        if ($murid->foto) {
                            Storage::disk('public')->delete($murid->foto);
                        }
                        $muridData['foto'] = $fotoPath;
                    }
                    $murid->update($muridData);
                    \App\Services\QRCodeService::getOrGenerateUserQR($user, $murid);
                } else {
                    $newMurid = Murid::create([
                        'id_user'  => $idUser,
                        'id_kelas' => $request->id_kelas,
                        'nisn'     => $request->nisn ?: null,
                        'qr_code'  => $request->nisn ?: null,
                        'no_hp'    => $request->no_hp ?: null,
                        'foto'     => $fotoPath,
                        'status'   => 'Aktif',
                    ]);
                    \App\Services\QRCodeService::getOrGenerateUserQR($user, $newMurid);
                }
            } else {
                // User murid baru sepenuhnya
                $user = User::create([
                    'nama'     => $request->nama,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => 'Murid',
                    'status'   => 'Aktif',
                ]);

                $newMurid = Murid::create([
                    'id_user'  => $user->id_user,
                    'id_kelas' => $request->id_kelas,
                    'nisn'     => $request->nisn ?: null,
                    'qr_code'  => $request->nisn ?: null,
                    'no_hp'    => $request->no_hp ?: null,
                    'foto'     => $fotoPath,
                    'status'   => 'Aktif',
                ]);
                \App\Services\QRCodeService::getOrGenerateUserQR($user, $newMurid);
            }

            return redirect()->route('siswa.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('siswa.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Perbarui data murid.
     */
    public function update(Request $request, $id)
    {
        $tahunAktif = $this->getActiveTahunAkademik();
        if (!$tahunAktif || !$tahunAktif->is_aktif) {
            return redirect()->route('siswa.gagal')
                ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        if ($request->filled('id_kelas')) {
            $kelas = Kelas::with('tahunAkademik')->find($request->id_kelas);
            if ($kelas && $kelas->tahunAkademik && !$kelas->tahunAkademik->is_aktif) {
                return redirect()->route('siswa.gagal')
                    ->with('error', 'Tahun akademik kelas ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }
        }

        $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'\-]+$/'],
            'email'    => 'required|email|max:100|unique:users,email,' . $murid->id_user . ',id_user',
            'nisn'     => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'no_hp'    => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
            'password' => 'nullable|string|min:6',
            'foto'     => 'nullable|image|max:2048',
        ], [
            'nama.regex'  => 'Nama hanya boleh berisi huruf dan spasi.',
            'nisn.regex'  => 'NISN hanya boleh berisi angka.',
            'no_hp.regex' => 'No. HP hanya boleh berisi angka.',
        ]);

        try {
            $userData = [
                'nama'  => $request->nama,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $murid->user->update($userData);

            $muridData = [
                'id_kelas' => $request->id_kelas ?: null,
                'nisn'     => $request->nisn ?: null,
                'qr_code'  => $request->nisn ?: null,
                'no_hp'    => $request->no_hp ?: null,
            ];

            if ($request->hasFile('foto')) {
                if ($murid->foto) {
                    Storage::disk('public')->delete($murid->foto);
                }
                $muridData['foto'] = $request->file('foto')->store('murid', 'public');
            }

            $murid->update($muridData);
            \App\Services\QRCodeService::getOrGenerateUserQR($murid->user, $murid);

            return redirect()->route('siswa.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('siswa.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus murid.
     */
    public function destroy($id)
    {
        try {
            $tahunAktif = $this->getActiveTahunAkademik();
            if (!$tahunAktif || !$tahunAktif->is_aktif) {
                return redirect()->route('siswa.gagal')
                    ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }

            $murid = Murid::with('user')->findOrFail($id);
            if ($murid->foto) {
                Storage::disk('public')->delete($murid->foto);
            }
            $murid->user->delete(); // cascade ke murid
            return redirect()->route('siswa.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('siswa.gagal')->with('error', 'Gagal menghapus data murid.');
        }
    }

    /**
     * Progress Setoran Murid (Figma Node 412:4268)
     */
    public function show($id)
    {
        $murid = Murid::with(['user', 'kelas.guru.user'])->findOrFail($id);

        // Target hafalan dari kelas murid
        $targetList = $murid->id_kelas
            ? TargetHafalan::with('surat')
                ->where('id_kelas', $murid->id_kelas)
                ->orderBy('id_surat', 'asc')
                ->get()
            : collect();

        // Setoran hafalan murid ini
        $setoranList = SetoranHafalan::with(['surat', 'murid.user'])
            ->where('id_murid', $murid->id_murid)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_setoran', 'desc')
            ->get();

        // Hitung progress per surat
        $suratProgress = [];
        foreach ($targetList as $target) {
            $setoranSurat = $setoranList->where('id_surat', $target->id_surat);
            $totalAyat = $target->surat->jumlah_ayat ?? 0;

            // Ayat yang lulus (status Lancar/Lulus/Selesai)
            $lulusSetoran = $setoranSurat->filter(function($s) {
                return in_array(strtolower(trim($s->status ?? '')), ['lancar', 'lulus', 'selesai', 'tuntas']);
            });

            $totalAyatSetor = $lulusSetoran->sum(function($s) {
                return max(0, $s->ayat_selesai - $s->ayat_mulai + 1);
            });
            if ($totalAyat > 0) {
                $totalAyatSetor = min($totalAyat, $totalAyatSetor);
            }

            $setoranTerakhir = $setoranSurat->first();

            $statusSurat = 'Belum Mulai';
            if ($totalAyat > 0 && $totalAyatSetor >= $totalAyat) {
                $statusSurat = 'Selesai';
            } elseif ($setoranSurat->count() > 0 || $totalAyatSetor > 0) {
                $statusSurat = 'Progress';
            }

            $persen = $totalAyat > 0 ? min(100, round(($totalAyatSetor / $totalAyat) * 100)) : 0;

            $suratProgress[] = [
                'target'        => $target,
                'totalSetor'    => $totalAyatSetor,
                'totalAyat'     => $totalAyat,
                'persen'        => $persen,
                'status'        => $statusSurat,
                'terakhir'      => $setoranTerakhir,
            ];
        }

        $totalSurat   = count($suratProgress);
        $selesaiCount = collect($suratProgress)->where('status', 'Selesai')->count();
        $persen       = $totalSurat > 0 ? round(($selesaiCount / $totalSurat) * 100) : 0;

        return view('siswa.show', compact('murid', 'targetList', 'setoranList', 'suratProgress', 'totalSurat', 'selesaiCount', 'persen'));
    }

    /**
     * Cetak Setoran Murid (per satu murid) - Figma Node 439:3678
     */
    public function cetak($id)
    {
        $murid = Murid::with(['user', 'kelas.guru.user', 'kelas.tahunAkademik'])->findOrFail($id);
        $kelas = $murid->kelas;

        // Target hafalan kelas murid jika ada
        $targetIds = $murid->id_kelas
            ? TargetHafalan::where('id_kelas', $murid->id_kelas)->pluck('id_surat')->toArray()
            : [];

        // Cocokkan surat dengan yang menjadi target kelas (jika ada target, tampilkan target saja; jika belum ada target, tampilkan semua)
        if (!empty($targetIds)) {
            $suratList = Surat::whereIn('id_surat', $targetIds)->orderBy('id_surat', 'asc')->get();
        } else {
            $suratList = Surat::orderBy('id_surat', 'asc')->get();
        }

        // Setoran murid ini
        $setoranList = SetoranHafalan::where('id_murid', $murid->id_murid)->get();

        // Format data rekap untuk murid ini
        $suratStatus = [];
        $totalSuratSelesai = 0;
        foreach ($suratList as $s) {
            $setoranSurat = $setoranList->where('id_surat', $s->id_surat);
            
            $lulusSetoran = $setoranSurat->filter(function($row) {
                return in_array(strtolower(trim($row->status ?? '')), ['lancar', 'lulus', 'selesai', 'tuntas']);
            });
            $totalAyatSetor = $lulusSetoran->sum(fn($x) => max(0, $x->ayat_selesai - $x->ayat_mulai + 1));
            $isLulus = ($s->jumlah_ayat > 0 && $totalAyatSetor >= $s->jumlah_ayat);

            $hasMengulang = $setoranSurat->filter(function($row) {
                return in_array(strtolower(trim($row->status ?? '')), ['mengulang', 'perlu perbaikan']);
            })->count() > 0;

            if ($isLulus) {
                $suratStatus[$s->id_surat] = ['symbol' => '✓', 'status' => 'lulus'];
                $totalSuratSelesai++;
            } elseif ($hasMengulang && $totalAyatSetor == 0) {
                $suratStatus[$s->id_surat] = ['symbol' => 'M', 'status' => 'mengulang'];
            } elseif ($totalAyatSetor > 0) {
                $suratStatus[$s->id_surat] = ['symbol' => (string)min($s->jumlah_ayat, $totalAyatSetor), 'status' => 'progress'];
            } else {
                $suratStatus[$s->id_surat] = ['symbol' => '—', 'status' => 'belum'];
            }
        }

        // Single murid row
        $rows = [
            [
                'murid' => $murid,
                'suratStatus' => $suratStatus,
                'totalSuratSelesai' => $totalSuratSelesai,
            ]
        ];

        // Total per surat
        $totalPerSurat = [];
        foreach ($suratList as $s) {
            $totalPerSurat[$s->id_surat] = ($suratStatus[$s->id_surat]['symbol'] === '✓') ? 1 : 0;
        }
        $grandTotalSelesai = $totalSuratSelesai;

        // Jumlah murid mencapai target di kelasnya
        $jumlahTargetTuntas = 0;
        if ($murid->id_kelas) {
            $kelasMurid = Murid::where('id_kelas', $murid->id_kelas)->get();
            foreach ($kelasMurid as $km) {
                $kmSetoran = SetoranHafalan::where('id_murid', $km->id_murid)->get();
                $allTargetPassed = !empty($targetIds);
                foreach ($targetIds as $tId) {
                    $targetSurat = $suratList->firstWhere('id_surat', $tId);
                    $passedAyat = $kmSetoran->where('id_surat', $tId)->filter(function($row) {
                        return in_array(strtolower(trim($row->status ?? '')), ['lancar', 'lulus', 'selesai', 'tuntas']);
                    })->sum(fn($x) => max(0, $x->ayat_selesai - $x->ayat_mulai + 1));
                    if (!$targetSurat || $passedAyat < $targetSurat->jumlah_ayat) {
                        $allTargetPassed = false;
                        break;
                    }
                }
                if ($allTargetPassed) {
                    $jumlahTargetTuntas++;
                }
            }
        }

        return view('siswa.cetak', compact(
            'kelas',
            'murid',
            'suratList',
            'rows',
            'totalPerSurat',
            'grandTotalSelesai',
            'jumlahTargetTuntas'
        ));
    }

    /**
     * Cetak Setoran Per Kelas - Figma Node 439:3678
     */
    public function cetakKelas($kelasId)
    {
        $kelas = Kelas::with(['guru.user', 'tahunAkademik'])->findOrFail($kelasId);
        $muridList = Murid::with('user')
            ->where('id_kelas', $kelasId)
            ->join('users', 'murid.id_user', '=', 'users.id_user')
            ->select('murid.*')
            ->orderBy('users.nama', 'asc')
            ->get();

        $suratList = Surat::orderBy('id_surat', 'asc')->get();
        $targetIds = TargetHafalan::where('id_kelas', $kelasId)->pluck('id_surat')->toArray();

        $rows = [];
        $totalPerSurat = [];
        foreach ($suratList as $s) {
            $totalPerSurat[$s->id_surat] = 0;
        }
        $grandTotalSelesai = 0;
        $jumlahTargetTuntas = 0;

        foreach ($muridList as $m) {
            $setoranMurid = SetoranHafalan::where('id_murid', $m->id_murid)->get();
            $suratStatus = [];
            $totalSuratSelesai = 0;
            $allTargetPassed = !empty($targetIds);

            foreach ($suratList as $s) {
                $setoranSurat = $setoranMurid->where('id_surat', $s->id_surat);
                $lulusSetoran = $setoranSurat->filter(function($row) {
                    return in_array(strtolower(trim($row->status ?? '')), ['lancar', 'lulus', 'selesai', 'tuntas']);
                });
                $totalAyatSetor = $lulusSetoran->sum(fn($x) => max(0, $x->ayat_selesai - $x->ayat_mulai + 1));
                $isLulus = ($s->jumlah_ayat > 0 && $totalAyatSetor >= $s->jumlah_ayat);

                $hasMengulang = $setoranSurat->filter(function($row) {
                    return in_array(strtolower(trim($row->status ?? '')), ['mengulang', 'perlu perbaikan']);
                })->count() > 0;

                if ($isLulus) {
                    $suratStatus[$s->id_surat] = ['symbol' => '✓', 'status' => 'lulus'];
                    $totalSuratSelesai++;
                    $totalPerSurat[$s->id_surat]++;
                } elseif ($hasMengulang && $totalAyatSetor == 0) {
                    $suratStatus[$s->id_surat] = ['symbol' => 'M', 'status' => 'mengulang'];
                } elseif ($totalAyatSetor > 0) {
                    $suratStatus[$s->id_surat] = ['symbol' => (string)min($s->jumlah_ayat, $totalAyatSetor), 'status' => 'progress'];
                } else {
                    $suratStatus[$s->id_surat] = ['symbol' => '—', 'status' => 'belum'];
                }

                if (in_array($s->id_surat, $targetIds) && !$isLulus) {
                    $allTargetPassed = false;
                }
            }

            if ($allTargetPassed) {
                $jumlahTargetTuntas++;
            }

            $grandTotalSelesai += $totalSuratSelesai;
            $rows[] = [
                'murid' => $m,
                'suratStatus' => $suratStatus,
                'totalSuratSelesai' => $totalSuratSelesai,
            ];
        }

        $murid = $muridList->first();

        return view('siswa.cetak', compact(
            'kelas',
            'murid',
            'suratList',
            'rows',
            'totalPerSurat',
            'grandTotalSelesai',
            'jumlahTargetTuntas'
        ));
    }

    /**
     * Alert berhasil.
     */
    public function berhasil(Request $request)
    {
        $isDelete = $request->query('action') === 'delete';
        return view('siswa.berhasil', compact('isDelete'));
    }

    /**
     * Alert gagal.
     */
    public function gagal()
    {
        return view('siswa.gagal');
    }
}
