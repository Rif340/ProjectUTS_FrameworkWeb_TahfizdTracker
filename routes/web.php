<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\HariKerjaController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\TargetHafalanController;
use App\Http\Controllers\PengaturanAbsensiController;
use Illuminate\Support\Facades\Auth;

// Route Halaman Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login-murid', [AuthController::class, 'showLoginMuridForm'])->name('login.murid');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Authenticated (Multi-Role)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');

    // Modul Absensi Kehadiran (Figma Node 635:1687, 637:2471, 638:4162, & 639:8484)
    Route::get('/absensi/kehadiran', [AbsensiController::class, 'kehadiran'])->name('absensi.kehadiran');
    Route::get('/absensi/detail', [AbsensiController::class, 'detail'])->name('absensi.detail');
    Route::get('/absensi/cetak-kehadiran', [AbsensiController::class, 'cetakIndividu'])->name('absensi.cetak'); // Cetak Akun Pribadi
    Route::get('/absensi/cetak-semua', [AbsensiController::class, 'cetakSemua'])->name('absensi.cetak.semua'); // Cetak Seluruh Pengguna (Admin & Guru)
    Route::get('/absensi/export-excel', [AbsensiController::class, 'exportExcelIndividu'])->name('absensi.export.excel'); // Export Excel Pribadi
    Route::get('/absensi/export-excel-semua', [AbsensiController::class, 'exportExcelSemua'])->name('absensi.export.excel.semua'); // Export Excel Semua
    Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
    Route::post('/absensi/pulang', [AbsensiController::class, 'absenPulang'])->name('absensi.pulang');
    Route::post('/absensi/scan-verify', [AbsensiController::class, 'scanVerify'])->name('absensi.scan.verify');
    Route::get('/absensi/riwayat-pengguna', [AbsensiController::class, 'riwayatPengguna'])->name('absensi.riwayat-pengguna');

    // Modul Pengaturan Absensi (Figma Node 709:72997)
    Route::prefix('absensi/pengaturan')->name('absensi.pengaturan.')->group(function () {
        Route::get('/',        [PengaturanAbsensiController::class, 'index'])->name('index');
        Route::post('/tambah', [PengaturanAbsensiController::class, 'store'])->name('store');
        Route::put('/{id}',    [PengaturanAbsensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PengaturanAbsensiController::class, 'destroy'])->name('destroy');
    });

    // ── Modul Hari Libur (Admin only) ──────────────────────────────
    Route::prefix('hari-libur')->name('hari-libur.')->group(function () {
        Route::get('/',            [HariLiburController::class, 'index'])  ->name('index');
        Route::get('/tambah',      [HariLiburController::class, 'create']) ->name('create');
        Route::post('/tambah',     [HariLiburController::class, 'store'])  ->name('store');
        Route::get('/{id}/edit',   [HariLiburController::class, 'edit'])   ->name('edit');
        Route::put('/{id}',        [HariLiburController::class, 'update']) ->name('update');
        Route::get('/berhasil',    [HariLiburController::class, 'berhasil'])->name('berhasil');
        Route::get('/gagal',       [HariLiburController::class, 'gagal'])  ->name('gagal');
        Route::delete('/{id}',     [HariLiburController::class, 'destroy'])->name('destroy');
    });

    // ── Modul Hari Kerja (Admin only) ──────────────────────────────
    Route::prefix('hari-kerja')->name('hari-kerja.')->group(function () {
        Route::get('/',            [HariKerjaController::class, 'index'])   ->name('index');
        Route::get('/tambah',      [HariKerjaController::class, 'create'])  ->name('create');
        Route::post('/tambah',     [HariKerjaController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit',   [HariKerjaController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',        [HariKerjaController::class, 'update'])  ->name('update');
        Route::get('/berhasil',    [HariKerjaController::class, 'berhasil'])->name('berhasil');
        Route::get('/gagal',       [HariKerjaController::class, 'gagal'])   ->name('gagal');
        Route::delete('/{id}',     [HariKerjaController::class, 'destroy']) ->name('destroy');
    });

    // ── Modul Tahun Akademik (Admin only) ──────────────────────────
    Route::prefix('tahun-akademik')->name('tahun-akademik.')->group(function () {
        Route::get('/',            [TahunAkademikController::class, 'index'])   ->name('index');
        Route::get('/tambah',      [TahunAkademikController::class, 'create'])  ->name('create');
        Route::post('/tambah',     [TahunAkademikController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit',   [TahunAkademikController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',        [TahunAkademikController::class, 'update'])  ->name('update');
        Route::get('/berhasil',    [TahunAkademikController::class, 'berhasil'])->name('berhasil');
        Route::get('/gagal',       [TahunAkademikController::class, 'gagal'])   ->name('gagal');
        Route::post('/pilih/{id}',    [TahunAkademikController::class, 'setActiveYear'])->name('set-active');
        Route::post('/{id}/aktifkan', [TahunAkademikController::class, 'aktifkan'])->name('aktifkan');
        Route::delete('/{id}',        [TahunAkademikController::class, 'destroy']) ->name('destroy');
    });

    // ── Modul Kelola Pengguna (Admin only) ─────────────────────
    Route::prefix('pengguna')->name('pengguna.')->group(function () {
        Route::get('/',            [PenggunaController::class, 'index'])   ->name('index');
        Route::get('/tambah',      [PenggunaController::class, 'create'])  ->name('create');
        Route::post('/tambah',     [PenggunaController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit',   [PenggunaController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',        [PenggunaController::class, 'update'])  ->name('update');
        Route::get('/berhasil',    [PenggunaController::class, 'berhasil'])->name('berhasil');
        Route::get('/gagal',       [PenggunaController::class, 'gagal'])   ->name('gagal');
        Route::delete('/{id}',     [PenggunaController::class, 'destroy']) ->name('destroy');
    });

    // ── Modul Profile (Semua Role: Admin, Guru, Murid) ─────────
    Route::get('/profile',         [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile',        [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // ── Modul Kelola Murid / Siswa (Guru) ──────────────────────
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/',                     [MuridController::class, 'index'])    ->name('index');
        Route::post('/tambah',              [MuridController::class, 'store'])    ->name('store');
        Route::post('/{id}/update',         [MuridController::class, 'update'])   ->name('update');
        Route::delete('/{id}',              [MuridController::class, 'destroy'])  ->name('destroy');
        Route::get('/berhasil',             [MuridController::class, 'berhasil']) ->name('berhasil');
        Route::get('/gagal',                [MuridController::class, 'gagal'])    ->name('gagal');
        Route::get('/{id}/progress',        [MuridController::class, 'show'])     ->name('show');
        Route::get('/{id}/cetak',           [MuridController::class, 'cetak'])    ->name('cetak');
        Route::get('/kelas/{kelasId}/cetak',[MuridController::class, 'cetakKelas'])->name('cetak.kelas');
    });

    // ── Modul Tahfizd (Guru) ──────────────────
    Route::prefix('tahfizd')->name('tahfizd.')->group(function () {
        // Detail Tahfizd / Daftar Kelas (Figma Node 393:4682)
        Route::get('/',                     [\App\Http\Controllers\TahfizdController::class, 'index'])    ->name('index');
        Route::post('/tambah',              [\App\Http\Controllers\TahfizdController::class, 'store'])    ->name('store');
        Route::post('/{id}/update',         [\App\Http\Controllers\TahfizdController::class, 'update'])   ->name('update');
        Route::get('/berhasil',             [\App\Http\Controllers\TahfizdController::class, 'berhasil']) ->name('berhasil');
        Route::get('/gagal',                [\App\Http\Controllers\TahfizdController::class, 'gagal'])    ->name('gagal');
        Route::delete('/{id}',              [\App\Http\Controllers\TahfizdController::class, 'destroy'])  ->name('destroy');

        // Target Hafalan
        Route::get('/target',               [TargetHafalanController::class, 'index'])    ->name('target.index');
        Route::post('/target/tambah',       [TargetHafalanController::class, 'store'])    ->name('target.store');
        Route::delete('/target/{id}',       [TargetHafalanController::class, 'destroy'])  ->name('target.destroy');
        Route::get('/target/berhasil',      [TargetHafalanController::class, 'berhasil']) ->name('target.berhasil');
        Route::get('/target/gagal',         [TargetHafalanController::class, 'gagal'])    ->name('target.gagal');
        // Cetak rekap per kelas
        Route::get('/cetak/{kelasId}',      [TargetHafalanController::class, 'cetakKelas'])->name('cetak.kelas');

        // Setoran Hafalan CRUD
        Route::post('/setoran/tambah',      [\App\Http\Controllers\TahfizdController::class, 'storeSetoran'])->name('setoran.store');
        Route::post('/setoran/{id}/update', [\App\Http\Controllers\TahfizdController::class, 'updateSetoran'])->name('setoran.update');
        Route::delete('/setoran/{id}',      [\App\Http\Controllers\TahfizdController::class, 'destroySetoran'])->name('setoran.destroy');

        // Detail Kelas / Progres Kelas (Figma Node 603:2185)
        Route::get('/kelas/{id}',           [\App\Http\Controllers\TahfizdController::class, 'show'])->name('show');
        Route::get('/{id}',                 [\App\Http\Controllers\TahfizdController::class, 'show'])->whereNumber('id');
    });
});

