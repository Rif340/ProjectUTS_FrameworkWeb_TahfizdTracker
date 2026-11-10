<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\TahunAkademik;
use App\Models\HariLibur;
use App\Models\HariKerja;
use App\Models\AbsensiAdmin;
use App\Models\AbsensiGuru;
use App\Models\AbsensiMurid;
use App\Models\SetoranHafalan;
use App\Models\TargetHafalan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ambil tahun akademik aktif
    private function getActiveTahunAkademik(): ?TahunAkademik
    {
        $activeYearId = session('active_tahun_akademik_id');
        if ($activeYearId) {
            $tahun = TahunAkademik::find($activeYearId);
            if ($tahun) return $tahun;
        }
        return TahunAkademik::where('is_aktif', true)->first() 
            ?? TahunAkademik::orderBy('tanggal_mulai', 'desc')->first();
    }
    // akhir ambil tahun akademik aktif

    // halaman utama dasbor
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role);
        $today = Carbon::today()->format('Y-m-d');
        $activeTahun = $this->getActiveTahunAkademik();

        if ($role === 'admin') {
            return $this->adminDashboard($user, $today, $activeTahun);
        } elseif ($role === 'guru') {
            return $this->guruDashboard($user, $today, $activeTahun);
        } else {
            return $this->muridDashboard($user, $today, $activeTahun);
        }
    }
    // akhir halaman utama dasbor

    // dasbor admin
    private function adminDashboard($user, $today, $activeTahun)
    {
        // hitung total data master
        $totalPengguna = User::count();
        $totalGuru     = Guru::where('status', 'Aktif')->count();
        $totalMurid    = Murid::where('status', 'Aktif')->count();
        $totalKelas    = Kelas::count();
        $totalSetoran  = SetoranHafalan::count();
        // akhir hitung total data master

        // rekap absensi hari ini
        $tepatAdmin = AbsensiAdmin::where('tanggal', $today)->where('status', 'tepat_waktu')->count();
        $lambatAdmin = AbsensiAdmin::where('tanggal', $today)->where('status', 'terlambat')->count();

        $tepatGuru  = AbsensiGuru::where('tanggal', $today)->where('status', 'tepat_waktu')->count();
        $lambatGuru = AbsensiGuru::where('tanggal', $today)->where('status', 'terlambat')->count();

        $tepatMurid  = AbsensiMurid::where('tanggal', $today)->where('status', 'tepat_waktu')->count();
        $lambatMurid = AbsensiMurid::where('tanggal', $today)->where('status', 'terlambat')->count();

        $hadirTepat     = $tepatAdmin + $tepatGuru + $tepatMurid;
        $hadirTerlambat = $lambatAdmin + $lambatGuru + $lambatMurid;
        $totalHadir     = $hadirTepat + $hadirTerlambat;
        $belumAbsen     = max(0, $totalPengguna - $totalHadir);

        $persenHadir = $totalPengguna > 0 ? round(($totalHadir / $totalPengguna) * 100) : 0;
        // akhir rekap absensi hari ini

        // grafik tren absensi tujuh hari terakhir
        $chartDates = [];
        $chartHadir = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $label = Carbon::today()->subDays($i)->translatedFormat('D, d/m');
            $chartDates[] = $label;

            $cAdmin = AbsensiAdmin::where('tanggal', $date)->count();
            $cGuru  = AbsensiGuru::where('tanggal', $date)->count();
            $cMurid = AbsensiMurid::where('tanggal', $date)->count();
            $chartHadir[] = $cAdmin + $cGuru + $cMurid;
        }
        // akhir grafik tren absensi tujuh hari terakhir

        // grafik distribusi murid per kelas
        $kelasList = Kelas::withCount('murid')->orderBy('nama_kelas')->get();
        $kelasLabels = $kelasList->pluck('nama_kelas')->toArray();
        $kelasData   = $kelasList->pluck('murid_count')->toArray();
        // akhir grafik distribusi murid per kelas

        // daftar hari libur mendatang
        $liburMendatang = HariLibur::where('tanggal_mulai', '>=', $today)
            ->orderBy('tanggal_mulai', 'asc')
            ->take(3)
            ->get();
        // akhir daftar hari libur mendatang

        // daftar absensi pengguna hari ini
        $admAbs = AbsensiAdmin::with('admin.user')->where('tanggal', $today)->get()->map(fn($a) => [
            'nama'   => $a->admin->user->nama ?? 'Admin',
            'role'   => 'Admin',
            'nomor'  => $a->admin->nip ?? '-',
            'masuk'  => $a->waktu_masuk ? substr($a->waktu_masuk, 0, 5) : '-',
            'pulang' => $a->waktu_pulang ? substr($a->waktu_pulang, 0, 5) : '-',
            'status' => $a->status ?? 'tepat_waktu',
        ]);
        $guruAbs = AbsensiGuru::with('guru.user')->where('tanggal', $today)->get()->map(fn($g) => [
            'nama'   => $g->guru->user->nama ?? 'Guru',
            'role'   => 'Guru',
            'nomor'  => $g->guru->nip ?? '-',
            'masuk'  => $g->waktu_masuk ? substr($g->waktu_masuk, 0, 5) : '-',
            'pulang' => $g->waktu_pulang ? substr($g->waktu_pulang, 0, 5) : '-',
            'status' => $g->status ?? 'tepat_waktu',
        ]);
        $muridAbs = AbsensiMurid::with('murid.user')->where('tanggal', $today)->get()->map(fn($m) => [
            'nama'   => $m->murid->user->nama ?? 'Murid',
            'role'   => 'Murid',
            'nomor'  => $m->murid->nisn ?? '-',
            'masuk'  => $m->waktu_masuk ? substr($m->waktu_masuk, 0, 5) : '-',
            'pulang' => $m->waktu_pulang ? substr($m->waktu_pulang, 0, 5) : '-',
            'status' => $m->status ?? 'tepat_waktu',
        ]);
        $todayAbsensi = $admAbs->concat($guruAbs)->concat($muridAbs)->take(6);
        // akhir daftar absensi pengguna hari ini

        // daftar pengguna terdaftar terbaru
        $recentUsers = User::latest('id_user')->take(5)->get();
        // akhir daftar pengguna terdaftar terbaru

        return view('dashboard', compact(
            'user', 'activeTahun', 'totalPengguna', 'totalGuru', 'totalMurid', 'totalKelas',
            'hadirTepat', 'hadirTerlambat', 'belumAbsen', 'totalHadir', 'persenHadir',
            'chartDates', 'chartHadir', 'kelasLabels', 'kelasData',
            'liburMendatang', 'todayAbsensi', 'recentUsers'
        ));
    }
    // akhir dasbor admin

    // dasbor guru
    private function guruDashboard($user, $today, $activeTahun)
    {
        $guru = $user->guru;
        $guruId = $guru->id_guru ?? 0;

        // data kelas pengampu
        $kelasList = Kelas::where('id_guru', $guruId)->withCount('murid')->get();
        $kelasIds  = $kelasList->pluck('id_kelas')->toArray();

        $totalMuridDiampu = Murid::whereIn('id_kelas', $kelasIds)->count();
        $totalSetoranKelas = SetoranHafalan::whereIn('id_kelas', $kelasIds)->count();
        // akhir data kelas pengampu

        // absensi guru hari ini
        $absensiGuruHariIni = AbsensiGuru::where('id_guru', $guruId)->where('tanggal', $today)->first();

        // absensi murid yang diampu hari ini
        $muridIds = Murid::whereIn('id_kelas', $kelasIds)->pluck('id_murid')->toArray();
        $muridTepat = AbsensiMurid::whereIn('id_murid', $muridIds)->where('tanggal', $today)->where('status', 'tepat_waktu')->count();
        $muridLambat = AbsensiMurid::whereIn('id_murid', $muridIds)->where('tanggal', $today)->where('status', 'terlambat')->count();
        $muridHadir = $muridTepat + $muridLambat;
        $muridBelum = max(0, count($muridIds) - $muridHadir);
        // akhir absensi murid yang diampu hari ini

        // rekap status setoran di kelas
        $setoranLancar = SetoranHafalan::whereIn('id_kelas', $kelasIds)
            ->whereIn('status', ['Lancar', 'Lulus', 'Selesai'])
            ->count();
        $setoranPerbaikan = SetoranHafalan::whereIn('id_kelas', $kelasIds)
            ->whereIn('status', ['Perlu Perbaikan', 'Kurang Lancar'])
            ->count();
        $setoranMengulang = SetoranHafalan::whereIn('id_kelas', $kelasIds)
            ->where('status', 'Mengulang')
            ->count();
        // akhir rekap status setoran di kelas

        // grafik tren setoran tujuh hari terakhir
        $chartDates = [];
        $chartSetoran = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $label = Carbon::today()->subDays($i)->translatedFormat('D, d/m');
            $chartDates[] = $label;
            $chartSetoran[] = SetoranHafalan::whereIn('id_kelas', $kelasIds)->where('tanggal', $date)->count();
        }
        // akhir grafik tren setoran tujuh hari terakhir

        // daftar setoran kelas terbaru
        $recentSetoran = SetoranHafalan::whereIn('id_kelas', $kelasIds)
            ->with(['murid.user', 'surat', 'kelas'])
            ->latest('id_setoran')
            ->take(5)
            ->get();
        // akhir daftar setoran kelas terbaru

        // hari libur mendatang
        $liburMendatang = HariLibur::where('tanggal_mulai', '>=', $today)
            ->orderBy('tanggal_mulai', 'asc')
            ->take(3)
            ->get();
        // akhir hari libur mendatang

        return view('dashboard', compact(
            'user', 'activeTahun', 'guru', 'kelasList', 'totalMuridDiampu', 'totalSetoranKelas',
            'absensiGuruHariIni', 'muridTepat', 'muridLambat', 'muridBelum',
            'setoranLancar', 'setoranPerbaikan', 'setoranMengulang',
            'chartDates', 'chartSetoran', 'recentSetoran', 'liburMendatang'
        ));
    }
    // akhir dasbor guru

    // dasbor murid
    private function muridDashboard($user, $today, $activeTahun)
    {
        $murid = $user->murid;
        $muridId = $murid->id_murid ?? 0;
        $kelas = $murid ? $murid->kelas : null;
        $kelasId = $kelas ? $kelas->id_kelas : null;

        // capaian target hafalan murid
        $totalTarget = $kelasId ? TargetHafalan::where('id_kelas', $kelasId)->count() : 0;
        $targetLulus = SetoranHafalan::where('id_murid', $muridId)
            ->whereIn('status', ['Lancar', 'Lulus', 'Selesai'])
            ->distinct('id_surat')
            ->count('id_surat');
        
        $persenTarget = $totalTarget > 0 ? min(100, round(($targetLulus / $totalTarget) * 100)) : 0;
        // akhir capaian target hafalan murid

        // total jumlah ayat yang disetorkan
        $allSetoran = SetoranHafalan::where('id_murid', $muridId)->get();
        $totalAyat = 0;
        foreach ($allSetoran as $st) {
            $totalAyat += max(1, ($st->ayat_selesai - $st->ayat_mulai + 1));
        }
        // akhir total jumlah ayat yang disetorkan

        // absensi murid hari ini dan bulan ini
        $absensiHariIni = AbsensiMurid::where('id_murid', $muridId)->where('tanggal', $today)->first();

        $curMonth = Carbon::now()->month;
        $curYear  = Carbon::now()->year;
        $hadirBulanIniTepat = AbsensiMurid::where('id_murid', $muridId)
            ->whereMonth('tanggal', $curMonth)
            ->whereYear('tanggal', $curYear)
            ->where('status', 'tepat_waktu')
            ->count();
        $hadirBulanIniLambat = AbsensiMurid::where('id_murid', $muridId)
            ->whereMonth('tanggal', $curMonth)
            ->whereYear('tanggal', $curYear)
            ->where('status', 'terlambat')
            ->count();
        $totalHadirBulanIni = $hadirBulanIniTepat + $hadirBulanIniLambat;
        // akhir absensi murid hari ini dan bulan ini

        // riwayat setoran murid terbaru
        $recentSetoran = SetoranHafalan::where('id_murid', $muridId)
            ->with(['surat', 'kelas'])
            ->latest('tanggal')
            ->latest('id_setoran')
            ->take(5)
            ->get();
        // akhir riwayat setoran murid terbaru

        // daftar target hafalan kelas
        $targetList = $kelasId ? TargetHafalan::where('id_kelas', $kelasId)->with('surat')->get() : collect([]);

        // grafik tren setoran murid tujuh hari terakhir
        $chartDates = [];
        $chartSetoran = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $label = Carbon::today()->subDays($i)->translatedFormat('D, d/m');
            $chartDates[] = $label;
            $chartSetoran[] = SetoranHafalan::where('id_murid', $muridId)->where('tanggal', $date)->count();
        }
        // akhir grafik tren setoran murid tujuh hari terakhir

        // hari libur mendatang
        $liburMendatang = HariLibur::where('tanggal_mulai', '>=', $today)
            ->orderBy('tanggal_mulai', 'asc')
            ->take(3)
            ->get();
        // akhir hari libur mendatang

        return view('dashboard', compact(
            'user', 'activeTahun', 'murid', 'kelas',
            'totalTarget', 'targetLulus', 'persenTarget', 'totalAyat',
            'absensiHariIni', 'hadirBulanIniTepat', 'hadirBulanIniLambat', 'totalHadirBulanIni',
            'chartDates', 'chartSetoran',
            'recentSetoran', 'targetList', 'liburMendatang'
        ));
    }
    // akhir dasbor murid
}
