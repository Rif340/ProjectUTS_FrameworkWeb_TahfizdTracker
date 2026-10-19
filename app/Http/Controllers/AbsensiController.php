<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AbsensiAdmin;
use App\Models\AbsensiGuru;
use App\Models\HariKerja;
use App\Models\TahunAkademik;
use App\Models\PengaturanAbsensi;

class AbsensiController extends Controller
{
    /**
     * Helper: Dapatkan Tahun Akademik yang sedang aktif (dari session atau DB).
     */
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

    /**
     * Helper: terjemahkan role DB (kapital) ke lowercase yang konsisten.
     */
    private function roleIs(string $userRole, string $check): bool
    {
        return strtolower($userRole) === strtolower($check);
    }

    /**
     * Menampilkan halaman Absensi Kehadiran sesuai rancangan Figma (Node 635:1687)
     */
    public function kehadiran()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Terjemahkan nama hari ke bahasa Indonesia
        $dayMap = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $currentDayName = $dayMap[Carbon::now()->format('l')] ?? 'Senin';

        // Ambil aturan jam kerja hari ini (default 07:00:00 & 14:00:00 jika belum diset)
        $hariKerja       = HariKerja::where('hari', $currentDayName)->first();
        $jamMasukDefault = $hariKerja->jam_masuk ?? '07:00:00';
        $jamPulangDefault = $hariKerja->jam_pulang ?? '14:00:00';

        // Cek data absensi hari ini sesuai role user (case-insensitive)
        $absensi = null;
        if ($this->roleIs($user->role, 'admin') && $user->admin) {
            $absensi = AbsensiAdmin::where('id_admin', $user->admin->id_admin)
                ->where('tanggal', $today)
                ->first();
        } elseif ($this->roleIs($user->role, 'guru') && $user->guru) {
            $absensi = AbsensiGuru::where('id_guru', $user->guru->id_guru)
                ->where('tanggal', $today)
                ->first();
        } elseif ($this->roleIs($user->role, 'murid') && $user->murid) {
            $absensi = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid)
                ->where('tanggal', $today)
                ->first();
        }

        // Format: "Senin, 18 Oktober 2026 07:30:37" (tanggal + jam:menit:detik)
        $tanggalMasukFormatted = null;
        $tanggalPulangFormatted = null;

        if ($absensi && $absensi->waktu_masuk) {
            $tgl = $absensi->tanggal instanceof \Carbon\Carbon
                ? $absensi->tanggal->toDateString()
                : (string) $absensi->tanggal;
            $tanggalMasukFormatted = Carbon::parse($tgl . ' ' . $absensi->waktu_masuk)
                ->locale('id')
                ->translatedFormat('l, d F Y H:i:s');
        }

        if ($absensi && $absensi->waktu_pulang) {
            $tgl = $absensi->tanggal instanceof \Carbon\Carbon
                ? $absensi->tanggal->toDateString()
                : (string) $absensi->tanggal;
            $tanggalPulangFormatted = Carbon::parse($tgl . ' ' . $absensi->waktu_pulang)
                ->locale('id')
                ->translatedFormat('l, d F Y H:i:s');
        }

        $bukaMasukDefault = Carbon::parse($jamMasukDefault)->subHours(2)->format('H:i:s');
        $isHariLibur      = \App\Http\Controllers\HariLiburController::isTodayHoliday();
        $isHariKerjaAktif = ($hariKerja && strtolower($hariKerja->status) === 'aktif' && !$isHariLibur);

        return view('absensi.kehadiran', compact(
            'user',
            'absensi',
            'hariKerja',
            'jamMasukDefault',
            'jamPulangDefault',
            'bukaMasukDefault',
            'isHariKerjaAktif',
            'isHariLibur',
            'currentDayName',
            'tanggalMasukFormatted',
            'tanggalPulangFormatted'
        ));
    }

    /**
     * Riwayat Absensi Pengguna — tampilkan rekap semua user (hanya Admin)
     */
    public function riwayatPengguna()
    {
        $user = Auth::user();
        $activeTahun = $this->getActiveTahunAkademik();

        // Kumpulkan semua data absensi dari semua tipe user
        $semuaRiwayat = collect();

        // Guru
        $guruList = \App\Models\Guru::with('user')->get();
        foreach ($guruList as $guru) {
            $query = AbsensiGuru::with('kelas')
                ->where('id_guru', $guru->id_guru);

            if ($activeTahun) {
                $query->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $absens = $query->orderBy('tanggal', 'desc')->get();
            foreach ($absens as $abs) {
                $semuaRiwayat->push([
                    'nama'          => $guru->user->nama ?? '-',
                    'role'          => 'Guru',
                    'nomor_induk'   => $guru->nip ?? '-',
                    'tanggal'       => $abs->tanggal,
                    'hari'          => \Carbon\Carbon::parse($abs->tanggal)->locale('id')->translatedFormat('l'),
                    'waktu_masuk'   => $abs->waktu_masuk ?? '-',
                    'waktu_pulang'  => $abs->waktu_pulang ?? '-',
                    'kelas'         => $abs->kelas->nama_kelas ?? '-',
                    'status'        => $abs->status,
                ]);
            }
        }

        // Admin
        $adminList = \App\Models\Admin::with('user')->get();
        foreach ($adminList as $adm) {
            $query = AbsensiAdmin::where('id_admin', $adm->id_admin);

            if ($activeTahun) {
                $query->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $absens = $query->orderBy('tanggal', 'desc')->get();
            foreach ($absens as $abs) {
                $semuaRiwayat->push([
                    'nama'          => $adm->user->nama ?? '-',
                    'role'          => 'Admin',
                    'nomor_induk'   => $adm->nip ?? '-',
                    'tanggal'       => $abs->tanggal,
                    'hari'          => \Carbon\Carbon::parse($abs->tanggal)->locale('id')->translatedFormat('l'),
                    'waktu_masuk'   => $abs->waktu_masuk ?? '-',
                    'waktu_pulang'  => $abs->waktu_pulang ?? '-',
                    'kelas'         => '-',
                    'status'        => $abs->status,
                ]);
            }
        }

        // Murid
        $muridList = \App\Models\Murid::with('user', 'kelas')->get();
        foreach ($muridList as $murid) {
            $query = \App\Models\AbsensiMurid::with('kelas')
                ->where('id_murid', $murid->id_murid);

            if ($activeTahun) {
                $query->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $absens = $query->orderBy('tanggal', 'desc')->get();
            foreach ($absens as $abs) {
                $semuaRiwayat->push([
                    'nama'          => $murid->user->nama ?? '-',
                    'role'          => 'Murid',
                    'nomor_induk'   => $murid->nisn ?? '-',
                    'tanggal'       => $abs->tanggal,
                    'hari'          => \Carbon\Carbon::parse($abs->tanggal)->locale('id')->translatedFormat('l'),
                    'waktu_masuk'   => $abs->waktu_masuk ?? '-',
                    'waktu_pulang'  => $abs->waktu_pulang ?? '-',
                    'kelas'         => $abs->kelas->nama_kelas ?? ($murid->kelas->nama_kelas ?? '-'),
                    'status'        => $abs->status,
                ]);
            }
        }

        // Urutkan: terbaru dulu
        $semuaRiwayat = $semuaRiwayat->sortByDesc('tanggal')->values();

        return view('absensi.riwayat_pengguna', compact('user', 'semuaRiwayat'));
    }

    /**
     * Menampilkan halaman Detail Absensi sesuai rancangan Figma (Node 638:4162)
     */
    public function detail()
    {
        $user = Auth::user();

        $riwayat        = collect();
        $totalTepatWaktu = 0;
        $totalTerlambat  = 0;
        $totalTidakHadir = 0;

        $activeTahun = $this->getActiveTahunAkademik();

        if ($this->roleIs($user->role, 'guru') && $user->guru) {
            $query = AbsensiGuru::with('kelas')
                ->where('id_guru', $user->guru->id_guru);

            if ($activeTahun) {
                $query->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $riwayat = $query->orderBy('tanggal', 'desc')->get();

            $baseCount = AbsensiGuru::where('id_guru', $user->guru->id_guru);
            if ($activeTahun) {
                $baseCount->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $totalTepatWaktu = (clone $baseCount)->where('status', 'tepat waktu')->count();
            $totalTerlambat  = (clone $baseCount)->where('status', 'terlambat')->count();
            $totalTidakHadir = (clone $baseCount)->where('status', 'tidak hadir')->count();

        } elseif ($this->roleIs($user->role, 'admin') && $user->admin) {
            $query = AbsensiAdmin::where('id_admin', $user->admin->id_admin);

            if ($activeTahun) {
                $query->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $riwayat = $query->orderBy('tanggal', 'desc')->get();

            $baseCount = AbsensiAdmin::where('id_admin', $user->admin->id_admin);
            if ($activeTahun) {
                $baseCount->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $totalTepatWaktu = (clone $baseCount)->where('status', 'tepat waktu')->count();
            $totalTerlambat  = (clone $baseCount)->where('status', 'terlambat')->count();
            $totalTidakHadir = (clone $baseCount)->where('status', 'tidak hadir')->count();

        } elseif ($this->roleIs($user->role, 'murid') && $user->murid) {
            $query = \App\Models\AbsensiMurid::with('kelas')
                ->where('id_murid', $user->murid->id_murid);

            if ($activeTahun) {
                $query->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $riwayat = $query->orderBy('tanggal', 'desc')->get();

            $baseCount = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid);
            if ($activeTahun) {
                $baseCount->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }

            $totalTepatWaktu = (clone $baseCount)->where('status', 'tepat waktu')->count();
            $totalTerlambat  = (clone $baseCount)->where('status', 'terlambat')->count();
            $totalTidakHadir = (clone $baseCount)->where('status', 'tidak hadir')->count();
        }

        $totalSemua = $totalTepatWaktu + $totalTerlambat + $totalTidakHadir;

        // Gunakan data real dari database (bukan fallback dummy)
        $chartStats = [
            'tepat_waktu' => $totalTepatWaktu,
            'terlambat'   => $totalTerlambat,
            'tidak_masuk' => $totalTidakHadir,
            'total'       => $totalSemua,
        ];

        $isAdmin = $this->roleIs($user->role, 'admin');
        $isGuru  = $this->roleIs($user->role, 'guru');
        $isMurid = $this->roleIs($user->role, 'murid');

        $compactData = compact(
            'user',
            'riwayat',
            'chartStats',
            'totalTepatWaktu',
            'totalTerlambat',
            'totalTidakHadir',
            'totalSemua',
            'isAdmin',
            'isGuru',
            'isMurid'
        );

        if ($isGuru) {
            return view('absensi.riwayat_guru', $compactData);
        } elseif ($isMurid) {
            return view('absensi.riwayat_murid', $compactData);
        } else {
            return view('absensi.riwayat_admin', $compactData);
        }
    }

    /**
     * Cetak Laporan Kehadiran INDIVIDU (sesuai akun user yang login)
     * Ditampilkan saat klik Cetak Kehadiran di halaman Detail Absensi pribadi
     */
    public function cetakIndividu(Request $request)
    {
        $user        = Auth::user();
        $activeTahun = $this->getActiveTahunAkademik();

        if ($activeTahun && $activeTahun->tanggal_mulai && $activeTahun->tanggal_selesai) {
            $start = Carbon::parse($activeTahun->tanggal_mulai)->locale('id');
            $end   = Carbon::parse($activeTahun->tanggal_selesai)->locale('id');
            if ($start->year === $end->year) {
                $bulanTahun = $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F Y');
            } else {
                $bulanTahun = $start->translatedFormat('F Y') . ' - ' . $end->translatedFormat('F Y');
            }
        } elseif ($activeTahun && $activeTahun->nama_tahun) {
            $bulanTahun = 'Tahun Ajaran ' . $activeTahun->nama_tahun . ($activeTahun->semester ? ' Semester ' . $activeTahun->semester : '');
        } else {
            $bulanTahun = Carbon::now()->locale('id')->translatedFormat('F Y');
        }

        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('d F Y');

        $jumlahHariKerja = HariKerja::where('status', 'Aktif')->count() * 4;

        $hadir      = 0;
        $terlambat  = 0;
        $tidakHadir = 0;
        $jabatan    = 'Staf';

        if ($this->roleIs($user->role, 'guru') && $user->guru) {
            $base = AbsensiGuru::where('id_guru', $user->guru->id_guru);
            if ($activeTahun) {
                $base->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }
            $hadir      = (clone $base)->where('status', 'tepat waktu')->count();
            $terlambat  = (clone $base)->where('status', 'terlambat')->count();
            $tidakHadir = (clone $base)->where('status', 'tidak hadir')->count();
            $jabatan    = 'Guru';
        } elseif ($this->roleIs($user->role, 'admin') && $user->admin) {
            $base = AbsensiAdmin::where('id_admin', $user->admin->id_admin);
            if ($activeTahun) {
                $base->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }
            $hadir      = (clone $base)->where('status', 'tepat waktu')->count();
            $terlambat  = (clone $base)->where('status', 'terlambat')->count();
            $tidakHadir = (clone $base)->where('status', 'tidak hadir')->count();
            $jabatan    = 'Admin Tata Usaha';
        } elseif ($this->roleIs($user->role, 'murid') && $user->murid) {
            $base = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid);
            if ($activeTahun) {
                $base->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }
            $hadir      = (clone $base)->where('status', 'tepat waktu')->count();
            $terlambat  = (clone $base)->where('status', 'terlambat')->count();
            $tidakHadir = (clone $base)->where('status', 'tidak hadir')->count();
            $kelasName  = $user->murid->kelas->nama_kelas ?? 'Murid';
            $jabatan    = 'Murid (' . $kelasName . ')';
        }

        $laporan = [
            [
                'no'          => 1,
                'nama'        => $user->nama,
                'jabatan'     => $jabatan,
                'hadir'       => $hadir,
                'terlambat'   => $terlambat,
                'tidak_hadir' => $tidakHadir,
                'hari_kerja'  => $hadir + $terlambat,
            ]
        ];

        $judulLaporan  = 'LAPORAN KEHADIRAN PRIBADI';
        $backUrl       = route('absensi.detail');
        $exportUrl     = route('absensi.export.excel');
        $isIndividu    = true;

        return view('absensi.cetak', compact(
            'user',
            'bulanTahun',
            'tanggalCetak',
            'jumlahHariKerja',
            'laporan',
            'judulLaporan',
            'backUrl',
            'exportUrl',
            'isIndividu'
        ));
    }

    /**
     * Cetak Laporan Kehadiran SELURUH PENGGUNA (hanya Guru & Admin, murid tidak dimasukkan)
     * Ditampilkan saat klik Cetak Kehadiran di halaman Riwayat Absensi Pengguna
     */
    public function cetakSemua(Request $request)
    {
        $user        = Auth::user();
        $activeTahun = $this->getActiveTahunAkademik();

        if ($activeTahun && $activeTahun->tanggal_mulai && $activeTahun->tanggal_selesai) {
            $start = Carbon::parse($activeTahun->tanggal_mulai)->locale('id');
            $end   = Carbon::parse($activeTahun->tanggal_selesai)->locale('id');
            if ($start->year === $end->year) {
                $bulanTahun = $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F Y');
            } else {
                $bulanTahun = $start->translatedFormat('F Y') . ' - ' . $end->translatedFormat('F Y');
            }
        } elseif ($activeTahun && $activeTahun->nama_tahun) {
            $bulanTahun = 'Tahun Ajaran ' . $activeTahun->nama_tahun . ($activeTahun->semester ? ' Semester ' . $activeTahun->semester : '');
        } else {
            $bulanTahun = Carbon::now()->locale('id')->translatedFormat('F Y');
        }

        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('d F Y');

        $jumlahHariKerja = HariKerja::where('status', 'Aktif')->count() * 4;

        $laporan = [];
        $no = 1;

        // 1. Guru
        $gurus = \App\Models\Guru::with('user')->get();
        foreach ($gurus as $guru) {
            $base = AbsensiGuru::where('id_guru', $guru->id_guru);
            if ($activeTahun) {
                $base->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }
            $hadir      = (clone $base)->where('status', 'tepat waktu')->count();
            $terlambat  = (clone $base)->where('status', 'terlambat')->count();
            $tidakHadir = (clone $base)->where('status', 'tidak hadir')->count();

            $laporan[] = [
                'no'          => $no++,
                'nama'        => $guru->user->nama ?? '-',
                'jabatan'     => 'Guru',
                'hadir'       => $hadir,
                'terlambat'   => $terlambat,
                'tidak_hadir' => $tidakHadir,
                'hari_kerja'  => $hadir + $terlambat,
            ];
        }

        // 2. Admin
        $admins = \App\Models\Admin::with('user')->get();
        foreach ($admins as $adm) {
            $base = AbsensiAdmin::where('id_admin', $adm->id_admin);
            if ($activeTahun) {
                $base->whereBetween('tanggal', [$activeTahun->tanggal_mulai, $activeTahun->tanggal_selesai]);
            }
            $hadir      = (clone $base)->where('status', 'tepat waktu')->count();
            $terlambat  = (clone $base)->where('status', 'terlambat')->count();
            $tidakHadir = (clone $base)->where('status', 'tidak hadir')->count();

            $laporan[] = [
                'no'          => $no++,
                'nama'        => $adm->user->nama ?? '-',
                'jabatan'     => 'Admin Tata Usaha',
                'hadir'       => $hadir,
                'terlambat'   => $terlambat,
                'tidak_hadir' => $tidakHadir,
                'hari_kerja'  => $hadir + $terlambat,
            ];
        }

        // CATATAN: Murid TIDAK dimasukkan sesuai permintaan user:
        // "untuk murid gausah di masukin di cetak laporan yang seluruh pengguna yang di admin"

        $judulLaporan  = 'LAPORAN REKAPITULASI KEHADIRAN GURU & STAF';
        $backUrl       = route('absensi.riwayat-pengguna');
        $exportUrl     = route('absensi.export.excel.semua');
        $isIndividu    = false;

        return view('absensi.cetak', compact(
            'user',
            'bulanTahun',
            'tanggalCetak',
            'jumlahHariKerja',
            'laporan',
            'judulLaporan',
            'backUrl',
            'exportUrl',
            'isIndividu'
        ));
    }

    /**
     * Export Excel/CSV Kehadiran Pribadi
     */
    public function exportExcelIndividu(Request $request)
    {
        $user       = Auth::user();
        $bulanTahun = Carbon::now()->locale('id')->translatedFormat('F Y');
        $fileName   = 'Laporan_Kehadiran_' . str_replace(' ', '_', $user->nama) . '_' . date('Y_m_d') . '.csv';

        $hadir      = 0;
        $terlambat  = 0;
        $tidakHadir = 0;
        $jabatan    = 'Staf';

        if ($this->roleIs($user->role, 'guru') && $user->guru) {
            $hadir      = AbsensiGuru::where('id_guru', $user->guru->id_guru)->where('status', 'tepat waktu')->count();
            $terlambat  = AbsensiGuru::where('id_guru', $user->guru->id_guru)->where('status', 'terlambat')->count();
            $tidakHadir = AbsensiGuru::where('id_guru', $user->guru->id_guru)->where('status', 'tidak hadir')->count();
            $jabatan    = 'Guru';
        } elseif ($this->roleIs($user->role, 'admin') && $user->admin) {
            $hadir      = AbsensiAdmin::where('id_admin', $user->admin->id_admin)->where('status', 'tepat waktu')->count();
            $terlambat  = AbsensiAdmin::where('id_admin', $user->admin->id_admin)->where('status', 'terlambat')->count();
            $tidakHadir = AbsensiAdmin::where('id_admin', $user->admin->id_admin)->where('status', 'tidak hadir')->count();
            $jabatan    = 'Admin Tata Usaha';
        } elseif ($this->roleIs($user->role, 'murid') && $user->murid) {
            $hadir      = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid)->where('status', 'tepat waktu')->count();
            $terlambat  = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid)->where('status', 'terlambat')->count();
            $tidakHadir = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid)->where('status', 'tidak hadir')->count();
            $jabatan    = 'Murid';
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($user, $jabatan, $hadir, $terlambat, $tidakHadir, $bulanTahun) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['SDIT AT-TAUBAH KARAWANG']);
            fputcsv($handle, ['LAPORAN KEHADIRAN INDIVIDU']);
            fputcsv($handle, ['Periode: ' . $bulanTahun]);
            fputcsv($handle, []);

            fputcsv($handle, ['No', 'Nama Pengguna', 'Jabatan', 'Hadir', 'Terlambat', 'Tidak Hadir', 'Hari Kerja']);
            fputcsv($handle, [1, $user->nama, $jabatan, $hadir, $terlambat, $tidakHadir, ($hadir + $terlambat)]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Excel/CSV Kehadiran Seluruh Pengguna (Guru & Admin tanpa Murid)
     */
    public function exportExcelSemua(Request $request)
    {
        $bulanTahun = Carbon::now()->locale('id')->translatedFormat('F Y');
        $fileName   = 'Laporan_Rekapitulasi_Kehadiran_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($bulanTahun) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['SDIT AT-TAUBAH KARAWANG']);
            fputcsv($handle, ['LAPORAN REKAPITULASI KEHADIRAN GURU & STAF']);
            fputcsv($handle, ['Periode: ' . $bulanTahun]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No', 'Nama Pengguna', 'Peran / Jabatan',
                'Hadir', 'Terlambat', 'Tidak Hadir', 'Hari Kerja'
            ]);

            $no = 1;

            // 1. Guru
            $gurus = \App\Models\Guru::with('user')->get();
            foreach ($gurus as $guru) {
                $hadir      = AbsensiGuru::where('id_guru', $guru->id_guru)->where('status', 'tepat waktu')->count();
                $terlambat  = AbsensiGuru::where('id_guru', $guru->id_guru)->where('status', 'terlambat')->count();
                $tidakHadir = AbsensiGuru::where('id_guru', $guru->id_guru)->where('status', 'tidak hadir')->count();
                $hariKerja  = $hadir + $terlambat;

                fputcsv($handle, [
                    $no++,
                    $guru->user->nama ?? '-',
                    'Guru',
                    $hadir,
                    $terlambat,
                    $tidakHadir,
                    $hariKerja,
                ]);
            }

            // 2. Admin
            $admins = \App\Models\Admin::with('user')->get();
            foreach ($admins as $adm) {
                $hadir      = AbsensiAdmin::where('id_admin', $adm->id_admin)->where('status', 'tepat waktu')->count();
                $terlambat  = AbsensiAdmin::where('id_admin', $adm->id_admin)->where('status', 'terlambat')->count();
                $tidakHadir = AbsensiAdmin::where('id_admin', $adm->id_admin)->where('status', 'tidak hadir')->count();
                $hariKerja  = $hadir + $terlambat;

                fputcsv($handle, [
                    $no++,
                    $adm->user->nama ?? '-',
                    'Admin Tata Usaha',
                    $hadir,
                    $terlambat,
                    $tidakHadir,
                    $hariKerja,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Proses Absensi Masuk (Sesuai Sequence Diagram)
     */
    public function absenMasuk(Request $request)
    {
        $user          = Auth::user();
        $today         = Carbon::today()->toDateString();
        $now           = Carbon::now();
        $waktuSekarang = $now->toTimeString();

        // ── Cek Hari Libur ─────────────────────────────────────────
        if (\App\Http\Controllers\HariLiburController::isTodayHoliday()) {
            return back()->with('error', 'Hari ini adalah hari libur. Absensi tidak dapat dilakukan.');
        }

        $dayMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis',
            'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $currentDayName = $dayMap[$now->format('l')] ?? 'Senin';
        $hariKerja      = HariKerja::where('hari', $currentDayName)->first();

        if (!$hariKerja || strtolower($hariKerja->status) !== 'aktif') {
            return back()->with('error', "Hari ini ({$currentDayName}) bukan hari kerja aktif atau sekolah sedang libur.");
        }

        $jamMasukResmi  = $hariKerja->jam_masuk ?? '07:00:00';
        $jamPulangResmi = $hariKerja->jam_pulang ?? '14:00:00';
        $bukaAbsenMasuk = Carbon::parse($jamMasukResmi)->subHours(2)->format('H:i:s');

        $isOvernight = $jamPulangResmi < $jamMasukResmi;

        if ($isOvernight) {
            $isValidMasuk = ($waktuSekarang >= $bukaAbsenMasuk || $waktuSekarang <= $jamPulangResmi);
            if (!$isValidMasuk) {
                if ($waktuSekarang < '12:00:00') {
                    return back()->with('error', "Waktu absensi masuk sudah berakhir karena sudah melewati jam pulang sekolah (" . substr($jamPulangResmi, 0, 5) . " WIB).");
                } else {
                    return back()->with('error', "Absensi masuk belum dibuka! Absensi baru dapat dilakukan mulai pukul " . substr($bukaAbsenMasuk, 0, 5) . " WIB (2 jam sebelum jam masuk resmi " . substr($jamMasukResmi, 0, 5) . " WIB).");
                }
            }
            $status = ($waktuSekarang >= $bukaAbsenMasuk && $waktuSekarang <= $jamMasukResmi) ? 'tepat waktu' : 'terlambat';
        } else {
            // Validasi jam buka absensi masuk (2 jam sebelum jam masuk)
            if ($waktuSekarang < $bukaAbsenMasuk) {
                return back()->with('error', "Absensi masuk belum dibuka! Absensi baru dapat dilakukan mulai pukul " . substr($bukaAbsenMasuk, 0, 5) . " WIB (2 jam sebelum jam masuk resmi " . substr($jamMasukResmi, 0, 5) . " WIB).");
            }

            // Validasi batas akhir absensi masuk (lewat jam pulang)
            if ($waktuSekarang > $jamPulangResmi) {
                return back()->with('error', "Waktu absensi masuk sudah berakhir karena sudah melewati jam pulang sekolah (" . substr($jamPulangResmi, 0, 5) . " WIB).");
            }

            $status = ($waktuSekarang <= $jamMasukResmi) ? 'tepat waktu' : 'terlambat';
        }

        if ($this->roleIs($user->role, 'admin') && $user->admin) {
            $absensi = AbsensiAdmin::firstOrNew([
                'id_admin' => $user->admin->id_admin,
                'tanggal'  => $today,
            ]);

            if ($absensi->waktu_masuk) {
                return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini!');
            }

            $absensi->waktu_masuk = $waktuSekarang;
            $absensi->status      = $status;
            $absensi->save();

        } elseif ($this->roleIs($user->role, 'guru') && $user->guru) {
            $absensi = AbsensiGuru::firstOrNew([
                'id_guru' => $user->guru->id_guru,
                'tanggal' => $today,
            ]);

            if ($absensi->waktu_masuk) {
                return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini!');
            }

            $absensi->waktu_masuk = $waktuSekarang;
            $absensi->status      = $status;
            $absensi->save();

        } elseif ($this->roleIs($user->role, 'murid') && $user->murid) {
            $absensi = \App\Models\AbsensiMurid::firstOrNew([
                'id_murid' => $user->murid->id_murid,
                'tanggal'  => $today,
            ]);

            if ($absensi->waktu_masuk) {
                return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini!');
            }

            if ($user->murid->id_kelas && !$absensi->id_kelas) {
                $absensi->id_kelas = $user->murid->id_kelas;
            }

            $absensi->waktu_masuk = $waktuSekarang;
            $absensi->status      = $status;
            $absensi->save();
        }

        return back()->with('success', 'Absensi masuk berhasil dicatat (' . ucfirst($status) . ')');
    }

    /**
     * Proses Absensi Pulang (Sesuai Sequence Diagram)
     */
    public function absenPulang(Request $request)
    {
        $user          = Auth::user();
        $today         = Carbon::today()->toDateString();
        $now           = Carbon::now();
        $waktuSekarang = $now->toTimeString();

        // ── Cek Hari Libur ─────────────────────────────────────────
        if (\App\Http\Controllers\HariLiburController::isTodayHoliday()) {
            return back()->with('error', 'Hari ini adalah hari libur. Absensi tidak dapat dilakukan.');
        }

        $dayMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis',
            'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $currentDayName = $dayMap[$now->format('l')] ?? 'Senin';
        $hariKerja      = HariKerja::where('hari', $currentDayName)->first();

        if (!$hariKerja || strtolower($hariKerja->status) !== 'aktif') {
            return back()->with('error', "Hari ini ({$currentDayName}) bukan hari kerja aktif atau sekolah sedang libur.");
        }

        $jamMasukResmi  = $hariKerja->jam_masuk ?? '07:00:00';
        $jamPulangResmi = $hariKerja->jam_pulang ?? '14:00:00';
        $isOvernight    = $jamPulangResmi < $jamMasukResmi;

        if ($isOvernight) {
            $isBelumPulang = ($waktuSekarang >= $jamMasukResmi || $waktuSekarang < $jamPulangResmi);
            if ($isBelumPulang) {
                return back()->with('error', "Belum waktunya absensi pulang! Absensi pulang baru dibuka mulai pukul " . substr($jamPulangResmi, 0, 5) . " WIB.");
            }
        } else {
            if ($waktuSekarang < $jamPulangResmi) {
                return back()->with('error', "Belum waktunya absensi pulang! Absensi pulang baru dibuka mulai pukul " . substr($jamPulangResmi, 0, 5) . " WIB.");
            }
        }

        if ($this->roleIs($user->role, 'admin') && $user->admin) {
            $absensi = AbsensiAdmin::where('id_admin', $user->admin->id_admin)
                ->where('tanggal', $today)
                ->first();

            if (!$absensi || !$absensi->waktu_masuk) {
                return back()->with('error', 'Anda belum melakukan absensi masuk!');
            }
            if ($absensi->waktu_pulang) {
                return back()->with('error', 'Anda sudah melakukan absensi keluar hari ini!');
            }

            $absensi->waktu_pulang = $waktuSekarang;
            $absensi->save();

        } elseif ($this->roleIs($user->role, 'guru') && $user->guru) {
            $absensi = AbsensiGuru::where('id_guru', $user->guru->id_guru)
                ->where('tanggal', $today)
                ->first();

            if (!$absensi || !$absensi->waktu_masuk) {
                return back()->with('error', 'Anda belum melakukan absensi masuk!');
            }
            if ($absensi->waktu_pulang) {
                return back()->with('error', 'Anda sudah melakukan absensi keluar hari ini!');
            }

            $absensi->waktu_pulang = $waktuSekarang;
            $absensi->save();

        } elseif ($this->roleIs($user->role, 'murid') && $user->murid) {
            $absensi = \App\Models\AbsensiMurid::where('id_murid', $user->murid->id_murid)
                ->where('tanggal', $today)
                ->first();

            if (!$absensi || !$absensi->waktu_masuk) {
                return back()->with('error', 'Anda belum melakukan absensi masuk!');
            }
            if ($absensi->waktu_pulang) {
                return back()->with('error', 'Anda sudah melakukan absensi keluar hari ini!');
            }

            $absensi->waktu_pulang = $waktuSekarang;
            $absensi->save();
        }

        return back()->with('success', 'Absensi keluar berhasil dicatat. Selamat beristirahat!');
    }

    /**
     * Verifikasi QR Code saat discan (Real-time Ajax)
     */
    public function scanVerify(Request $request)
    {
        $qrData        = trim($request->input('qr_data', ''));
        $tipe          = strtolower($request->input('tipe', 'masuk'));
        $today         = Carbon::today()->toDateString();
        $now           = Carbon::now();
        $waktuSekarang = $now->toTimeString();

        if (empty($qrData)) {
            return response()->json(['success' => false, 'message' => 'QR Code kosong atau tidak terbaca.'], 400);
        }

        // ── Cek Hari Libur ─────────────────────────────────────────
        if (\App\Http\Controllers\HariLiburController::isTodayHoliday()) {
            return response()->json([
                'success'      => false,
                'message'      => 'Hari ini adalah hari libur. Absensi tidak dapat dilakukan.',
                'scanned_code' => $qrData,
            ], 422);
        }

        // ── Cek Radius Batas Lokasi Absensi ────────────────────────
        $lokasiAktif = PengaturanAbsensi::where('status', 'Aktif')->get();
        if ($lokasiAktif->isNotEmpty()) {
            $userLat = $request->input('latitude');
            $userLng = $request->input('longitude');

            if ($userLat === null || $userLng === null || $userLat === '' || $userLng === '') {
                return response()->json([
                    'success'      => false,
                    'message'      => 'Lokasi GPS Anda tidak terdeteksi! Pastikan GPS/Lokasi perangkat Anda aktif dan izinkan akses lokasi pada browser untuk melakukan absensi.',
                    'scanned_code' => $qrData,
                ], 422);
            }

            $userLat = (float) $userLat;
            $userLng = (float) $userLng;
            $dalamRadius = false;
            $jarakTerdekat = null;
            $radiusBatas = null;
            $namaLokasiTerdekat = '';

            foreach ($lokasiAktif as $lokasi) {
                $jarak = $this->hitungJarak($userLat, $userLng, (float) $lokasi->latitude, (float) $lokasi->longitude);
                if ($jarakTerdekat === null || $jarak < $jarakTerdekat) {
                    $jarakTerdekat = $jarak;
                    $radiusBatas = $lokasi->radius_meter;
                    $namaLokasiTerdekat = $lokasi->nama_lokasi;
                }
                if ($jarak <= (float) $lokasi->radius_meter) {
                    $dalamRadius = true;
                    break;
                }
            }

            if (!$dalamRadius) {
                $jarakFormat = number_format($jarakTerdekat, 0, ',', '.');
                $radiusFormat = number_format($radiusBatas, 0, ',', '.');
                return response()->json([
                    'success'      => false,
                    'message'      => "Absensi Gagal! Anda berada di luar radius lokasi absensi ({$namaLokasiTerdekat}). Jarak Anda saat ini sekitar {$jarakFormat} meter (Maksimal radius: {$radiusFormat} meter).",
                    'scanned_code' => $qrData,
                ], 422);
            }
        }

        $foundUser  = null;
        $role       = null;
        $idKelas    = null;
        $nomorInduk = '';
        $idEntity   = null;

        // 1. Cek Guru
        $guru = \App\Models\Guru::with('user', 'kelas')
            ->where('qr_code', $qrData)
            ->orWhere('nip', $qrData)
            ->first();
        if ($guru) {
            $foundUser  = $guru->user;
            $role       = 'Guru';
            $nomorInduk = $guru->nip;
            $idEntity   = $guru->id_guru;
            $idKelas    = $guru->kelas->first()->id_kelas ?? null;
        }

        // 2. Cek Admin
        if (!$foundUser) {
            $admin = \App\Models\Admin::with('user')
                ->where('qr_code', $qrData)
                ->orWhere('nip', $qrData)
                ->first();
            if ($admin) {
                $foundUser  = $admin->user;
                $role       = 'Admin';
                $nomorInduk = $admin->nip;
                $idEntity   = $admin->id_admin;
            }
        }

        // 3. Cek Murid
        if (!$foundUser) {
            $murid = \App\Models\Murid::with('user', 'kelas')
                ->where('qr_code', $qrData)
                ->orWhere('nisn', $qrData)
                ->first();
            if ($murid) {
                $foundUser  = $murid->user;
                $role       = 'Murid';
                $nomorInduk = $murid->nisn;
                $idEntity   = $murid->id_murid;
                $idKelas    = $murid->id_kelas;
            }
        }

        if (!$foundUser) {
            return response()->json([
                'success'      => false,
                'message'      => 'QR Code Tidak Valid! Barcode tidak dikenali atau bukan dari SDIT At-Taubah.',
                'scanned_code' => $qrData,
            ], 404);
        }

        // Validasi kecocokan akun: SEMUA PENGGUNA (Admin, Guru, maupun Murid)
        // HANYA BOLEH melakukan absensi untuk akun miliknya sendiri!
        $currentUser = Auth::user();
        if ($currentUser && (int) $foundUser->id_user !== (int) $currentUser->id_user) {
            return response()->json([
                'success'      => false,
                'message'      => "QR Code ini milik {$foundUser->nama} ({$role}), bukan akun Anda ({$currentUser->nama})! Anda hanya dapat melakukan absensi untuk akun Anda sendiri.",
                'scanned_code' => $qrData,
            ], 422);
        }

        // Aturan Hari Kerja
        $dayMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis',
            'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $currentDayName = $dayMap[$now->format('l')] ?? 'Senin';
        $hariKerja      = \App\Models\HariKerja::where('hari', $currentDayName)->first();

        if (!$hariKerja || strtolower($hariKerja->status) !== 'aktif') {
            return response()->json([
                'success'      => false,
                'message'      => "Hari ini ({$currentDayName}) bukan hari kerja aktif atau sekolah sedang libur. Absensi tidak dapat dilakukan.",
                'scanned_code' => $qrData,
            ], 422);
        }

        $jamMasukResmi  = $hariKerja->jam_masuk ?? '07:00:00';
        $jamPulangResmi = $hariKerja->jam_pulang ?? '14:00:00';
        $bukaAbsenMasuk = Carbon::parse($jamMasukResmi)->subHours(2)->format('H:i:s');

        // Tentukan Model Absensi sesuai role
        $absensi = null;
        if ($role === 'Guru') {
            $absensi = \App\Models\AbsensiGuru::firstOrNew([
                'id_guru' => $idEntity,
                'tanggal' => $today,
            ]);
            if ($idKelas && !$absensi->id_kelas) {
                $absensi->id_kelas = $idKelas;
            }
        } elseif ($role === 'Admin') {
            $absensi = \App\Models\AbsensiAdmin::firstOrNew([
                'id_admin' => $idEntity,
                'tanggal'  => $today,
            ]);
        } elseif ($role === 'Murid') {
            $absensi = \App\Models\AbsensiMurid::firstOrNew([
                'id_murid' => $idEntity,
                'tanggal'  => $today,
            ]);
            if ($idKelas && !$absensi->id_kelas) {
                $absensi->id_kelas = $idKelas;
            }
        }

        $isOvernight    = $jamPulangResmi < $jamMasukResmi;

        if ($tipe === 'masuk') {
            if ($isOvernight) {
                $isValidMasuk = ($waktuSekarang >= $bukaAbsenMasuk || $waktuSekarang <= $jamPulangResmi);
                if (!$isValidMasuk) {
                    if ($waktuSekarang < '12:00:00') {
                        return response()->json([
                            'success'      => false,
                            'message'      => "Waktu absensi masuk sudah berakhir karena sudah melewati jam pulang sekolah (" . substr($jamPulangResmi, 0, 5) . " WIB). Anda tidak dapat melakukan absensi masuk lagi hari ini.",
                            'scanned_code' => $qrData,
                        ], 422);
                    } else {
                        return response()->json([
                            'success'      => false,
                            'message'      => "Absensi masuk belum dibuka! Absensi baru dapat dilakukan mulai pukul " . substr($bukaAbsenMasuk, 0, 5) . " WIB (2 jam sebelum jam masuk resmi " . substr($jamMasukResmi, 0, 5) . " WIB).",
                            'scanned_code' => $qrData,
                        ], 422);
                    }
                }
                $status = ($waktuSekarang >= $bukaAbsenMasuk && $waktuSekarang <= $jamMasukResmi) ? 'tepat waktu' : 'terlambat';
            } else {
                // Validasi jam buka absensi masuk (2 jam sebelum jam masuk)
                if ($waktuSekarang < $bukaAbsenMasuk) {
                    return response()->json([
                        'success'      => false,
                        'message'      => "Absensi masuk belum dibuka! Absensi baru dapat dilakukan mulai pukul " . substr($bukaAbsenMasuk, 0, 5) . " WIB (2 jam sebelum jam masuk resmi " . substr($jamMasukResmi, 0, 5) . " WIB).",
                        'scanned_code' => $qrData,
                    ], 422);
                }

                // Validasi batas akhir absensi masuk (sudah lewat jam pulang sekolah)
                if ($waktuSekarang > $jamPulangResmi) {
                    return response()->json([
                        'success'      => false,
                        'message'      => "Waktu absensi masuk sudah berakhir karena sudah melewati jam pulang sekolah (" . substr($jamPulangResmi, 0, 5) . " WIB). Anda tidak dapat melakukan absensi masuk lagi hari ini.",
                        'scanned_code' => $qrData,
                    ], 422);
                }

                $status = ($waktuSekarang <= $jamMasukResmi) ? 'tepat waktu' : 'terlambat';
            }

            if ($absensi->waktu_masuk) {
                return response()->json([
                    'success'      => false,
                    'message'      => "{$foundUser->nama} sudah melakukan absensi masuk hari ini pada {$absensi->waktu_masuk}!",
                    'scanned_code' => $qrData,
                    'user'         => [
                        'nama'        => $foundUser->nama,
                        'role'        => $role,
                        'nomor_induk' => $nomorInduk,
                        'waktu'       => $absensi->waktu_masuk,
                        'status'      => ucfirst($absensi->status),
                    ],
                ], 422);
            }

            $absensi->waktu_masuk = $waktuSekarang;
            $absensi->status      = $status;
            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absensi Masuk Berhasil!',
                'user'    => [
                    'nama'        => $foundUser->nama,
                    'role'        => $role,
                    'nomor_induk' => $nomorInduk,
                    'waktu'       => $waktuSekarang,
                    'status'      => ucfirst($status),
                ],
            ]);
        } else {
            // Absensi Pulang
            if (!$absensi->waktu_masuk) {
                return response()->json([
                    'success'      => false,
                    'message'      => "{$foundUser->nama} belum melakukan absensi masuk! Tidak dapat melakukan absensi pulang.",
                    'scanned_code' => $qrData,
                    'user'         => [
                        'nama'        => $foundUser->nama,
                        'role'        => $role,
                        'nomor_induk' => $nomorInduk,
                    ],
                ], 422);
            }

            if ($absensi->waktu_pulang) {
                return response()->json([
                    'success'      => false,
                    'message'      => "{$foundUser->nama} sudah melakukan absensi keluar hari ini pada {$absensi->waktu_pulang}!",
                    'scanned_code' => $qrData,
                    'user'         => [
                        'nama'        => $foundUser->nama,
                        'role'        => $role,
                        'nomor_induk' => $nomorInduk,
                        'waktu'       => $absensi->waktu_pulang,
                    ],
                ], 422);
            }

            // Validasi waktu absensi pulang (belum waktunya jam pulang)
            if ($isOvernight) {
                $isBelumPulang = ($waktuSekarang >= $jamMasukResmi || $waktuSekarang < $jamPulangResmi);
                if ($isBelumPulang) {
                    return response()->json([
                        'success'      => false,
                        'message'      => "Belum waktunya absensi pulang! Absensi pulang baru dibuka mulai pukul " . substr($jamPulangResmi, 0, 5) . " WIB.",
                        'scanned_code' => $qrData,
                    ], 422);
                }
            } else {
                if ($waktuSekarang < $jamPulangResmi) {
                    return response()->json([
                        'success'      => false,
                        'message'      => "Belum waktunya absensi pulang! Absensi pulang baru dibuka mulai pukul " . substr($jamPulangResmi, 0, 5) . " WIB.",
                        'scanned_code' => $qrData,
                    ], 422);
                }
            }

            $absensi->waktu_pulang = $waktuSekarang;
            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absensi Pulang Berhasil!',
                'user'    => [
                    'nama'        => $foundUser->nama,
                    'role'        => $role,
                    'nomor_induk' => $nomorInduk,
                    'waktu'       => $waktuSekarang,
                    'status'      => 'Selesai',
                ],
            ]);
        }
    }

    /**
     * Hitung jarak dua titik koordinat GPS menggunakan formula Haversine (hasil dalam meter).
     */
    private function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
