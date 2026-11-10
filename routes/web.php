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
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// rute autentikasi masuk
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login-murid', [AuthController::class, 'showLoginMuridForm'])->name('login.murid');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// akhir rute autentikasi masuk

// rute pengguna terautentikasi
Route::middleware(['auth'])->group(function () {
    // rute halaman beranda utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // akhir rute halaman beranda utama

    // modul absensi kehadiran
    Route::get('/absensi/kehadiran', [AbsensiController::class, 'kehadiran'])->name('absensi.kehadiran');
    Route::get('/absensi/detail', [AbsensiController::class, 'detail'])->name('absensi.detail');
    Route::get('/absensi/cetak-kehadiran', [AbsensiController::class, 'cetakIndividu'])->name('absensi.cetak');
    Route::get('/absensi/cetak-semua', [AbsensiController::class, 'cetakSemua'])->name('absensi.cetak.semua');
    Route::get('/absensi/export-excel', [AbsensiController::class, 'exportExcelIndividu'])->name('absensi.export.excel');
    Route::get('/absensi/export-excel-semua', [AbsensiController::class, 'exportExcelSemua'])->name('absensi.export.excel.semua');
    Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
    Route::post('/absensi/pulang', [AbsensiController::class, 'absenPulang'])->name('absensi.pulang');
    Route::post('/absensi/scan-verify', [AbsensiController::class, 'scanVerify'])->name('absensi.scan.verify');
    Route::get('/absensi/riwayat-pengguna', [AbsensiController::class, 'riwayatPengguna'])->name('absensi.riwayat-pengguna');
    // akhir modul absensi kehadiran

    // modul pengaturan batas lokasi absensi
    Route::prefix('absensi/pengaturan')->name('absensi.pengaturan.')->group(function () {
        Route::get('/',        [PengaturanAbsensiController::class, 'index'])->name('index');
        Route::post('/tambah', [PengaturanAbsensiController::class, 'store'])->name('store');
        Route::put('/{id}',    [PengaturanAbsensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PengaturanAbsensiController::class, 'destroy'])->name('destroy');
    });
    // akhir modul pengaturan batas lokasi absensi

    // modul hari libur
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
    // akhir modul hari libur

    // modul hari kerja
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
    // akhir modul hari kerja

    // modul tahun akademik
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
    // akhir modul tahun akademik

    // modul kelola pengguna
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
    // akhir modul kelola pengguna

    // modul profil pengguna
    Route::get('/profile',         [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile',        [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    // akhir modul profil pengguna

    // modul data murid
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
    // akhir modul data murid

    // modul kelas tahfizd dan setoran hafalan
    Route::prefix('tahfizd')->name('tahfizd.')->group(function () {
        Route::get('/',                     [\App\Http\Controllers\TahfizdController::class, 'index'])    ->name('index');
        Route::post('/tambah',              [\App\Http\Controllers\TahfizdController::class, 'store'])    ->name('store');
        Route::post('/{id}/update',         [\App\Http\Controllers\TahfizdController::class, 'update'])   ->name('update');
        Route::get('/berhasil',             [\App\Http\Controllers\TahfizdController::class, 'berhasil']) ->name('berhasil');
        Route::get('/gagal',                [\App\Http\Controllers\TahfizdController::class, 'gagal'])    ->name('gagal');
        Route::delete('/{id}',              [\App\Http\Controllers\TahfizdController::class, 'destroy'])  ->name('destroy');

        Route::get('/target',               [TargetHafalanController::class, 'index'])    ->name('target.index');
        Route::post('/target/tambah',       [TargetHafalanController::class, 'store'])    ->name('target.store');
        Route::delete('/target/{id}',       [TargetHafalanController::class, 'destroy'])  ->name('target.destroy');
        Route::get('/target/berhasil',      [TargetHafalanController::class, 'berhasil']) ->name('target.berhasil');
        Route::get('/target/gagal',         [TargetHafalanController::class, 'gagal'])    ->name('target.gagal');
        Route::get('/cetak/{kelasId}',      [TargetHafalanController::class, 'cetakKelas'])->name('cetak.kelas');

        Route::post('/setoran/tambah',      [\App\Http\Controllers\TahfizdController::class, 'storeSetoran'])->name('setoran.store');
        Route::post('/setoran/{id}/update', [\App\Http\Controllers\TahfizdController::class, 'updateSetoran'])->name('setoran.update');
        Route::delete('/setoran/{id}',      [\App\Http\Controllers\TahfizdController::class, 'destroySetoran'])->name('setoran.destroy');

        Route::get('/kelas/{id}',           [\App\Http\Controllers\TahfizdController::class, 'show'])->name('show');
        Route::get('/{id}',                 [\App\Http\Controllers\TahfizdController::class, 'show'])->whereNumber('id');
    });
    // akhir modul kelas tahfizd dan setoran hafalan
});
// akhir rute pengguna terautentikasi
