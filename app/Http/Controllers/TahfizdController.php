<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\TahunAkademik;
use App\Models\Murid;
use App\Models\Surat;
use App\Models\TargetHafalan;
use App\Models\SetoranHafalan;
use Illuminate\Support\Facades\Auth;

class TahfizdController extends Controller
{
    // ambil tahun akademik aktif
    private function getActiveTahunAkademik(): ?TahunAkademik
    {
        $activeYearId = session('active_tahun_akademik_id');
        if ($activeYearId) {
            $tahun = TahunAkademik::find($activeYearId);
            if ($tahun) {
                return $tahun;
            }
        }

        return TahunAkademik::where('is_aktif', true)->first() ?? TahunAkademik::orderBy('tanggal_mulai', 'desc')->first();
    }
    // akhir ambil tahun akademik aktif

    // tampilkan daftar kelas tahfizd
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $tahunAktif = $this->getActiveTahunAkademik();
        $activeYearId = $tahunAktif ? $tahunAktif->id_tahun_akademik : null;

        // query data kelas
        $query = Kelas::with(['guru.user', 'tahunAkademik']);
        if ($activeYearId) {
            $query->where('id_tahun_akademik', $activeYearId);
        }

        $kelasList = $query->orderBy('id_kelas', 'asc')
            ->paginate($perPage)
            ->appends($request->except('page'));
        // akhir query data kelas

        $guruList = Guru::with('user')->get();
        $tahunAkademikList = TahunAkademik::orderBy('id_tahun_akademik', 'desc')->get();

        return view('tahfizd.index', compact('kelasList', 'guruList', 'tahunAkademikList', 'tahunAktif', 'perPage'));
    }
    // akhir tampilkan daftar kelas tahfizd

    // simpan kelas tahfizd baru
    public function store(Request $request)
    {
        // validasi data kelas
        $request->validate([
            'nama_kelas'         => 'required|string|max:100',
            'id_guru'            => 'nullable|exists:guru,id_guru',
            'id_tahun_akademik'  => 'nullable|exists:tahun_akademik,id_tahun_akademik',
            'hari'               => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'          => 'required',
            'jam_selesai'        => 'required',
        ], [
            'nama_kelas.required'  => 'Nama kelas wajib diisi.',
            'hari.required'        => 'Hari wajib dipilih.',
            'jam_mulai.required'   => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
        ]);
        // akhir validasi data kelas

        try {
            // periksa tahun akademik aktif
            $tahunAktif = $this->getActiveTahunAkademik();
            $idTahun = $request->id_tahun_akademik ?: ($tahunAktif ? $tahunAktif->id_tahun_akademik : null);

            $targetTahun = $idTahun ? TahunAkademik::find($idTahun) : $tahunAktif;
            if (!$targetTahun || !$targetTahun->is_aktif) {
                return redirect()->route('tahfizd.gagal')
                    ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }
            // akhir periksa tahun akademik aktif

            // tentukan guru pengajar
            $idGuru = $request->id_guru;
            if (!$idGuru && Auth::check() && Auth::user()->guru) {
                $idGuru = Auth::user()->guru->id_guru;
            }
            if (!$idGuru) {
                $firstGuru = Guru::first();
                $idGuru = $firstGuru ? $firstGuru->id_guru : 1;
            }
            // akhir tentukan guru pengajar

            // format penamaan kelas
            $namaKelas = $request->nama_kelas;
            if ($request->filled('kelas_level')) {
                if (trim($namaKelas) === 'Tahfizd' || empty($namaKelas)) {
                    $namaKelas = $request->kelas_level;
                } else {
                    $namaKelas = trim($namaKelas . ' ' . $request->kelas_level);
                }
            }
            // akhir format penamaan kelas

            // buat record kelas baru
            Kelas::create([
                'nama_kelas'         => $namaKelas,
                'id_guru'            => $idGuru,
                'id_tahun_akademik'  => $idTahun,
                'hari'               => $request->hari,
                'jam_mulai'          => $request->jam_mulai,
                'jam_selesai'        => $request->jam_selesai,
                'status'             => 'Aktif',
            ]);
            // akhir buat record kelas baru

            return redirect()->route('tahfizd.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahfizd.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir simpan kelas tahfizd baru

    // perbarui data kelas tahfizd
    public function update(Request $request, $id)
    {
        $kelas = Kelas::with('tahunAkademik')->findOrFail($id);
        if ($kelas->tahunAkademik && !$kelas->tahunAkademik->is_aktif) {
            return redirect()->route('tahfizd.gagal')
                ->with('error', 'Tahun akademik untuk kelas ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        // validasi data pembaruan kelas
        $request->validate([
            'nama_kelas'  => 'required|string|max:100',
            'id_guru'     => 'required|exists:guru,id_guru',
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);
        // akhir validasi data pembaruan kelas

        try {
            // perbarui record kelas
            $kelas->update([
                'nama_kelas'  => $request->nama_kelas,
                'id_guru'     => $request->id_guru,
                'hari'        => $request->hari,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ]);
            // akhir perbarui record kelas

            return redirect()->route('tahfizd.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahfizd.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir perbarui data kelas tahfizd

    // hapus kelas tahfizd
    public function destroy($id)
    {
        try {
            // periksa tahun akademik aktif
            $kelas = Kelas::with('tahunAkademik')->findOrFail($id);
            if ($kelas->tahunAkademik && !$kelas->tahunAkademik->is_aktif) {
                return redirect()->route('tahfizd.gagal')
                    ->with('error', 'Tahun akademik untuk kelas ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }
            // akhir periksa tahun akademik aktif

            $kelas->delete();

            return redirect()->route('tahfizd.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('tahfizd.gagal')->with('error', 'Gagal menghapus kelas tahfizd.');
        }
    }
    // akhir hapus kelas tahfizd

    // tampilkan alert berhasil
    public function berhasil(Request $request)
    {
        $isDelete = $request->query('action') === 'delete';
        return view('tahfizd.berhasil', compact('isDelete'));
    }
    // akhir tampilkan alert berhasil

    // tampilkan alert gagal
    public function gagal()
    {
        return view('tahfizd.gagal');
    }
    // akhir tampilkan alert gagal

    // detail progres hafalan kelas
    public function show($id)
    {
        $kelas = Kelas::with(['guru.user', 'tahunAkademik', 'murid.user'])->findOrFail($id);

        // ambil data murid di kelas
        $muridList = Murid::with('user')
            ->where('id_kelas', $id)
            ->join('users', 'murid.id_user', '=', 'users.id_user')
            ->select('murid.*')
            ->orderBy('users.nama', 'asc')
            ->get();

        $totalMurid = $muridList->count();
        // akhir ambil data murid di kelas

        // ambil target hafalan kelas
        $targetList = TargetHafalan::with('surat')
            ->where('id_kelas', $id)
            ->orderBy('id_surat', 'asc')
            ->get();
        $targetSuratCount = $targetList->count();
        // akhir ambil target hafalan kelas

        // ambil riwayat setoran seluruh murid
        $riwayatSetoran = SetoranHafalan::with(['murid.user', 'surat'])
            ->where('id_kelas', $id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_setoran', 'desc')
            ->get();
        $totalSetoranCount = $riwayatSetoran->count();
        // akhir ambil riwayat setoran seluruh murid

        // hitung kemajuan kelulusan per surat target
        $suratCards = [];
        $muridTuntasClass = 0;

        foreach ($targetList as $target) {
            $totalAyatSurat = $target->surat->jumlah_ayat ?? 0;
            $setoranSurat = $riwayatSetoran->where('id_surat', $target->id_surat);

            $lulusCount = 0;
            foreach ($muridList as $murid) {
                $setoranLancarMurid = $setoranSurat->where('id_murid', $murid->id_murid)
                    ->whereIn('status', ['Lancar', 'Lulus', 'Selesai']);

                $ayatLulusMap = [];
                foreach ($setoranLancarMurid as $s) {
                    for ($a = $s->ayat_mulai; $a <= $s->ayat_selesai; $a++) {
                        $ayatLulusMap[$a] = true;
                    }
                }

                $isLulus = ($totalAyatSurat > 0 && count($ayatLulusMap) >= $totalAyatSurat);

                if ($isLulus) {
                    $lulusCount++;
                }
            }

            $persen = $totalMurid > 0 ? min(100, round(($lulusCount / $totalMurid) * 100)) : 0;

            $statusBadge = 'Belum Mulai';
            if ($persen >= 100 && $totalMurid > 0) {
                $statusBadge = 'tuntas';
            } elseif ($lulusCount > 0 || $setoranSurat->count() > 0) {
                $statusBadge = 'Progress';
            }

            $suratCards[] = [
                'target'       => $target,
                'surat'        => $target->surat,
                'lulus_count'  => $lulusCount,
                'persen'       => $persen,
                'status'       => $statusBadge,
            ];
        }
        // akhir hitung kemajuan kelulusan per surat target

        // hitung murid yang menuntaskan seluruh target
        if ($targetSuratCount > 0 && $totalMurid > 0) {
            foreach ($muridList as $m) {
                $allDone = true;
                foreach ($targetList as $t) {
                    $totalAyatT = $t->surat->jumlah_ayat ?? 0;
                    $setoranSuratT = $riwayatSetoran->where('id_murid', $m->id_murid)
                        ->where('id_surat', $t->id_surat)
                        ->whereIn('status', ['Lancar', 'Lulus', 'Selesai']);

                    $ayatMapT = [];
                    foreach ($setoranSuratT as $st) {
                        for ($at = $st->ayat_mulai; $at <= $st->ayat_selesai; $at++) {
                            $ayatMapT[$at] = true;
                        }
                    }

                    if ($totalAyatT <= 0 || count($ayatMapT) < $totalAyatT) {
                        $allDone = false;
                        break;
                    }
                }
                if ($allDone) {
                    $muridTuntasClass++;
                }
            }
            $overallPercent = min(100, round(($muridTuntasClass / $totalMurid) * 100));
        } else {
            $muridTuntasClass = 0;
            $overallPercent = 0;
        }
        // akhir hitung murid yang menuntaskan seluruh target

        // daftar surat untuk opsi setoran
        $allSurat = Surat::orderBy('id_surat', 'asc')->get();
        // akhir daftar surat untuk opsi setoran

        // pemetaan kelulusan dan ayat terakhir tiap murid
        $muridCompletedSurat = [];
        $muridLastPassedAyat = [];
        foreach ($muridList as $m) {
            $muridCompletedSurat[$m->id_murid] = [];
            $muridLastPassedAyat[$m->id_murid] = [];
            foreach ($allSurat as $s) {
                $totalAyatS = $s->jumlah_ayat ?? 0;
                $setoranLancar = $riwayatSetoran->where('id_murid', $m->id_murid)
                    ->where('id_surat', $s->id_surat)
                    ->whereIn('status', ['Lancar', 'Lulus', 'Selesai']);

                $ayatMapS = [];
                foreach ($setoranLancar as $sl) {
                    for ($a = $sl->ayat_mulai; $a <= $sl->ayat_selesai; $a++) {
                        $ayatMapS[$a] = true;
                    }
                }

                $lastPassed = !empty($ayatMapS) ? max(array_keys($ayatMapS)) : 0;
                $muridLastPassedAyat[$m->id_murid][$s->id_surat] = $lastPassed;

                if ($totalAyatS > 0 && count($ayatMapS) >= $totalAyatS) {
                    $muridCompletedSurat[$m->id_murid][$s->id_surat] = true;
                }
            }
        }
        // akhir pemetaan kelulusan dan ayat terakhir tiap murid

        return view('tahfizd.show', compact(
            'kelas',
            'muridList',
            'totalMurid',
            'targetList',
            'targetSuratCount',
            'riwayatSetoran',
            'totalSetoranCount',
            'suratCards',
            'muridTuntasClass',
            'overallPercent',
            'allSurat',
            'muridCompletedSurat',
            'muridLastPassedAyat'
        ));
    }
    // akhir detail progres hafalan kelas

    // simpan setoran hafalan murid baru
    public function storeSetoran(Request $request)
    {
        // validasi input data setoran
        $request->validate([
            'id_kelas'     => 'required|exists:kelas,id_kelas',
            'id_murid'     => 'required|exists:murid,id_murid',
            'id_surat'     => 'required|exists:surat,id_surat',
            'ayat_mulai'   => 'required|integer|min:1',
            'ayat_selesai' => 'required|integer|min:1|gte:ayat_mulai',
            'tanggal'      => 'required|date',
            'status'       => 'required|string|max:50',
        ], [
            'ayat_selesai.gte' => 'Ayat selesai tidak boleh lebih kecil dari ayat mulai.',
        ]);
        // akhir validasi input data setoran

        $surat = Surat::findOrFail($request->id_surat);
        $murid = Murid::with('user')->findOrFail($request->id_murid);
        $maxAyat = $surat->jumlah_ayat ?? 0;

        // validasi batas jumlah ayat surat
        if ($maxAyat > 0 && ($request->ayat_mulai > $maxAyat || $request->ayat_selesai > $maxAyat)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal: Surat {$surat->nama_surat} hanya memiliki {$maxAyat} ayat. Rentang ayat yang dimasukkan ({$request->ayat_mulai} - {$request->ayat_selesai}) melebihi batas ayat.");
        }
        // akhir validasi batas jumlah ayat surat

        // riwayat kelulusan surat murid
        $setoranLancar = SetoranHafalan::where('id_murid', $request->id_murid)
            ->where('id_surat', $request->id_surat)
            ->whereIn('status', ['Lancar', 'Lulus', 'Selesai'])
            ->get();

        $ayatLulusMap = [];
        foreach ($setoranLancar as $s) {
            for ($a = $s->ayat_mulai; $a <= $s->ayat_selesai; $a++) {
                $ayatLulusMap[$a] = true;
            }
        }
        // akhir riwayat kelulusan surat murid

        // periksa status kelulusan penuh surat
        if ($maxAyat > 0 && count($ayatLulusMap) >= $maxAyat) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal: Murid {$murid->user->nama} sudah dinyatakan LULUS (selesai) untuk Surat {$surat->nama_surat} ({$maxAyat} ayat). Tidak dapat menambahkan setoran baru pada surat yang sudah tuntas.");
        }
        // akhir periksa status kelulusan penuh surat

        // periksa urutan ayat setoran
        $lastPassedAyat = 0;
        if (!empty($ayatLulusMap)) {
            $lastPassedAyat = max(array_keys($ayatLulusMap));
        }

        $expectedNextAyat = $lastPassedAyat + 1;
        if ($request->ayat_mulai > $expectedNextAyat) {
            $pesanUrutan = $lastPassedAyat > 0
                ? "Gagal: Setoran harus berurutan. {$murid->user->nama} terakhir lulus di ayat {$lastPassedAyat} pada Surat {$surat->nama_surat}. Setoran berikutnya harus dimulai dari ayat {$expectedNextAyat}, tidak boleh melompat ke ayat {$request->ayat_mulai}."
                : "Gagal: Setoran pertama untuk Surat {$surat->nama_surat} harus dimulai dari ayat 1. Tidak boleh langsung melompat ke ayat {$request->ayat_mulai}.";

            return redirect()->back()
                ->withInput()
                ->with('error', $pesanUrutan);
        }
        // akhir periksa urutan ayat setoran

        // periksa pengulangan ayat yang telah lulus
        $sudahSemuaLulus = true;
        for ($a = $request->ayat_mulai; $a <= $request->ayat_selesai; $a++) {
            if (!isset($ayatLulusMap[$a])) {
                $sudahSemuaLulus = false;
                break;
            }
        }

        if ($sudahSemuaLulus && in_array($request->status, ['Lancar', 'Lulus', 'Selesai'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal: Ayat {$request->ayat_mulai} – {$request->ayat_selesai} pada Surat {$surat->nama_surat} sudah pernah disetorkan dengan status Lancar oleh {$murid->user->nama}. Silakan lanjutkan ke ayat berikutnya (ayat {$expectedNextAyat}).");
        }
        // akhir periksa pengulangan ayat yang telah lulus

        // periksa status tahun akademik kelas
        $kelas = Kelas::with('tahunAkademik')->findOrFail($request->id_kelas);
        if ($kelas->tahunAkademik && !$kelas->tahunAkademik->is_aktif) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tahun akademik kelas ini belum aktif. Setoran hafalan hanya dapat diubah pada tahun akademik yang aktif.');
        }
        // akhir periksa status tahun akademik kelas

        // buat data setoran hafalan
        $setoran = SetoranHafalan::create([
            'id_murid'     => $request->id_murid,
            'id_kelas'     => $request->id_kelas,
            'id_guru'      => $kelas->id_guru ?? (Auth::user()->guru->id_guru ?? 1),
            'id_surat'     => $request->id_surat,
            'ayat_mulai'   => $request->ayat_mulai,
            'ayat_selesai' => $request->ayat_selesai,
            'tanggal'      => $request->tanggal,
            'status'       => $request->status,
        ]);
        // akhir buat data setoran hafalan

        // kirim notifikasi ke orang tua murid
        $setoran->load(['murid.user', 'surat']);
        $notifResult = \App\Services\NotificationService::sendSetoranNotification($setoran);
        // akhir kirim notifikasi ke orang tua murid

        return redirect()->route('tahfizd.show', $request->id_kelas)
            ->with('success', 'Data Berhasil Di Tambahkan Atau Di Perbarui')
            ->with('fonnte_sent', $notifResult['fonnte_sent'] ?? false)
            ->with('wa_url', $notifResult['wa_url'] ?? null)
            ->with('action', 'save');
    }
    // akhir simpan setoran hafalan murid baru

    // perbarui setoran hafalan murid
    public function updateSetoran(Request $request, $id)
    {
        // periksa status tahun akademik
        $setoran = SetoranHafalan::with('kelas.tahunAkademik')->findOrFail($id);
        if ($setoran->kelas && $setoran->kelas->tahunAkademik && !$setoran->kelas->tahunAkademik->is_aktif) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tahun akademik kelas ini belum aktif. Setoran hafalan hanya dapat diubah pada tahun akademik yang aktif.');
        }
        // akhir periksa status tahun akademik

        // validasi input perbaruan setoran
        $request->validate([
            'id_surat'     => 'required|exists:surat,id_surat',
            'ayat_mulai'   => 'required|integer|min:1',
            'ayat_selesai' => 'required|integer|min:1|gte:ayat_mulai',
            'tanggal'      => 'required|date',
            'status'       => 'required|string|max:50',
        ], [
            'ayat_selesai.gte' => 'Ayat selesai tidak boleh lebih kecil dari ayat mulai.',
        ]);
        // akhir validasi input perbaruan setoran

        // validasi batas jumlah ayat surat
        $surat = Surat::findOrFail($request->id_surat);
        $maxAyat = $surat->jumlah_ayat ?? 0;

        if ($maxAyat > 0 && ($request->ayat_mulai > $maxAyat || $request->ayat_selesai > $maxAyat)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal: Surat {$surat->nama_surat} hanya memiliki {$maxAyat} ayat. Rentang ayat yang dimasukkan ({$request->ayat_mulai} - {$request->ayat_selesai}) melebihi batas ayat.");
        }
        // akhir validasi batas jumlah ayat surat

        // periksa riwayat tuntas surat
        $setoranLancar = SetoranHafalan::where('id_murid', $setoran->id_murid)
            ->where('id_surat', $request->id_surat)
            ->where('id_setoran', '!=', $id)
            ->whereIn('status', ['Lancar', 'Lulus', 'Selesai'])
            ->get();

        $ayatLulusMap = [];
        foreach ($setoranLancar as $s) {
            for ($a = $s->ayat_mulai; $a <= $s->ayat_selesai; $a++) {
                $ayatLulusMap[$a] = true;
            }
        }

        if ($maxAyat > 0 && count($ayatLulusMap) >= $maxAyat) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal: Murid sudah dinyatakan LULUS untuk Surat {$surat->nama_surat}. Tidak dapat mengubah setoran menjadi surat yang sudah tuntas.");
        }
        // akhir periksa riwayat tuntas surat

        // perbarui record setoran
        $setoran->update([
            'id_surat'     => $request->id_surat,
            'ayat_mulai'   => $request->ayat_mulai,
            'ayat_selesai' => $request->ayat_selesai,
            'tanggal'      => $request->tanggal,
            'status'       => $request->status,
        ]);
        // akhir perbarui record setoran

        return redirect()->route('tahfizd.show', $setoran->id_kelas)
            ->with('success', 'Data Berhasil Di Tambahkan Atau Di Perbarui')
            ->with('action', 'save');
    }
    // akhir perbarui setoran hafalan murid

    // hapus riwayat setoran hafalan
    public function destroySetoran($id)
    {
        // periksa tahun akademik aktif
        $setoran = SetoranHafalan::with('kelas.tahunAkademik')->findOrFail($id);
        if ($setoran->kelas && $setoran->kelas->tahunAkademik && !$setoran->kelas->tahunAkademik->is_aktif) {
            return redirect()->back()
                ->with('error', 'Tahun akademik kelas ini belum aktif. Setoran hafalan hanya dapat diubah pada tahun akademik yang aktif.');
        }
        // akhir periksa tahun akademik aktif

        // proses hapus setoran
        $idKelas = $setoran->id_kelas;
        $setoran->delete();
        // akhir proses hapus setoran

        return redirect()->route('tahfizd.show', $idKelas)
            ->with('success', 'Data Berhasil Di Hapus')
            ->with('action', 'delete');
    }
    // akhir hapus riwayat setoran hafalan
}
