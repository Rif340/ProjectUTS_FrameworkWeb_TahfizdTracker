@extends('layouts.app')

@php
    $role = strtolower($user->role);
@endphp

@section('title', 'Dashboard ' . $user->role)
@section('page_title', 'Dashboard ' . $user->role)

@push('styles')
<style>
    .content-card {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        min-height: auto !important;
        overflow: visible !important;
    }

    .db-container {
        padding: 0 0 35px;
        display: flex;
        flex-direction: column;
        gap: 22px;
        font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
        color: #1E293B;
        width: 100%;
        box-sizing: border-box;
    }

    /* spanduk ucapan selamat datang */
    .db-welcome-banner {
        background: linear-gradient(135deg, #0E774F 0%, #0A5A3B 100%);
        border-radius: 14px;
        padding: 24px 28px;
        color: #FFFFFF;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 14px rgba(14, 119, 79, 0.18);
        flex-wrap: wrap;
        gap: 16px;
    }
    .db-welcome-title {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }
    .db-welcome-sub {
        font-size: 13px;
        color: #D1FAE5;
        margin: 0;
        line-height: 1.5;
    }
    .db-badge-year {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: #FFFFFF;
        backdrop-filter: blur(4px);
    }

    /* akhir spanduk ucapan selamat datang */

    /* kartu statistik ringkasan */
    .db-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        width: 100%;
    }
    .db-stat-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 110px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .db-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .db-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .db-card-title {
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        letter-spacing: 0.2px;
        margin: 0;
    }
    .db-card-dots {
        font-size: 16px;
        line-height: 1;
        letter-spacing: 2px;
        color: #94A3B8;
        font-weight: 700;
        cursor: default;
    }
    .db-card-bottom {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .db-card-num-wrap {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .db-card-number {
        font-size: 30px;
        font-weight: 700;
        line-height: 1;
        color: #0F172A;
        letter-spacing: -0.5px;
    }
    .db-card-sub {
        font-size: 11px;
        color: #94A3B8;
        font-weight: 500;
    }
    .db-card-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F1F5F9;
        color: #0E774F;
        flex-shrink: 0;
    }
    .db-card-icon-box svg {
        width: 24px;
        height: 24px;
    }

    /* akhir kartu statistik ringkasan */

    /* tata letak grafik statistik */
    .db-charts-row {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 20px;
        width: 100%;
    }
    .db-chart-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
    }
    .db-chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #F1F5F9;
    }
    .db-chart-title {
        font-size: 14px;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .db-chart-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        background: #F1F5F9;
        color: #475569;
    }
    .db-chart-body {
        position: relative;
        flex: 1;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* akhir tata letak grafik statistik */

    /* tabel dan informasi tambahan */
    .db-bottom-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
        width: 100%;
    }
    .db-table-wrap {
        width: 100%;
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
    }
    .db-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .db-table th {
        background: #F8FAFC;
        padding: 12px 10px;
        font-weight: 700;
        color: #475569;
        text-align: center !important;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }
    .db-table td {
        padding: 12px 10px;
        text-align: center !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #F1F5F9;
        color: #1E293B;
    }
    .db-table tbody tr:last-child td {
        border-bottom: none;
    }
    .db-table tbody tr:hover {
        background: #F8FAFC;
    }

    /* lencana status kehadiran */
    .db-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
    }
    .db-badge-tepat { background: #ECFDF5; color: #047857; }
    .db-badge-lambat { background: #FFFBEB; color: #B45309; }
    .db-badge-alpha { background: #F1F5F9; color: #475569; }
    .db-badge-lancar { background: #ECFDF5; color: #047857; }
    .db-badge-perbaikan { background: #FFFBEB; color: #B45309; }
    .db-badge-mengulang { background: #FEF2F2; color: #B91C1C; }

    /* daftar informasi agenda libur */
    .db-info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .db-info-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 14px;
        background: #F8FAFC;
        border-radius: 10px;
        border-left: 4px solid #0E774F;
    }
    .db-info-date {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
    }
    .db-info-date-day {
        font-size: 15px;
        font-weight: 700;
        color: #0E774F;
        line-height: 1;
    }
    .db-info-date-month {
        font-size: 9px;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        margin-top: 2px;
    }
    .db-info-content {
        flex: 1;
        min-width: 0;
    }
    .db-info-title {
        font-size: 13px;
        font-weight: 600;
        color: #0F172A;
        margin: 0 0 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .db-info-sub {
        font-size: 11px;
        color: #64748B;
        margin: 0;
    }

    @media (max-width: 1024px) {
        .db-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .db-charts-row, .db-bottom-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 640px) {
        .db-stats-grid {
            grid-template-columns: 1fr;
        }
    }
    /* akhir tabel dan informasi tambahan */
</style>
@endpush

@section('content')
<div class="db-container">

    <!-- spanduk ucapan selamat datang -->
    <div class="db-welcome-banner">
        <div>
            <h1 class="db-welcome-title">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="db-welcome-sub">
                Sistem Pelacakan Absensi & Setoran Hafalan Al-Qur'an SDIT At-Taubah Karawang
            </p>
        </div>
        <div>
            <span class="db-badge-year">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                T.A: {{ $activeTahun->nama_tahun ?? '2026/2027' }} 
                @if(!empty($activeTahun->semester)) ({{ $activeTahun->semester }}) @endif
            </span>
        </div>
    </div>
    <!-- akhir spanduk ucapan selamat datang -->

    <!-- dashboard peranan admin -->
    @if($role === 'admin')
        <!-- kartu ringkasan admin -->
        <div class="db-stats-grid">
            <!-- kartu total pengguna -->
            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Total Pengguna</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalPengguna }}</span>
                        <span class="db-card-sub">{{ $totalGuru }} Guru &bull; {{ $totalMurid }} Murid</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #ECFDF5; color: #0E774F;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- akhir kartu total pengguna -->

            <!-- kartu total guru -->
            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Total Guru</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalGuru }}</span>
                        <span class="db-card-sub">Guru Aktif Mengajar</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #EFF6FF; color: #2563EB;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- akhir kartu total guru -->

            <!-- kartu total murid -->
            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Total Murid</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalMurid }}</span>
                        <span class="db-card-sub">Murid Terdaftar</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #FFFBEB; color: #D97706;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l10 5-10 5L2 7l10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- kartu kehadiran hari ini -->
            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Kehadiran Hari Ini</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $persenHadir }}%</span>
                        <span class="db-card-sub">{{ $totalHadir }} dari {{ $totalPengguna }} hadir</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #F5F3FF; color: #7C3AED;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- akhir kartu kehadiran hari ini -->
        </div>
        <!-- akhir kartu ringkasan admin -->

        <!-- baris grafik admin -->
        <div class="db-charts-row">
            <!-- grafik presensi hari ini -->
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                        </svg>
                        Presensi Pengguna Hari Ini
                    </h3>
                    <span class="db-chart-badge">{{ date('d M Y') }}</span>
                </div>
                <div class="db-chart-body" style="height: 250px;">
                    <canvas id="chartAdminAbsensi"></canvas>
                </div>
            </div>
            <!-- akhir grafik presensi hari ini -->

            <!-- grafik tren kehadiran mingguan -->
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        Tren Kehadiran 7 Hari Terakhir
                    </h3>
                    <span class="db-chart-badge">Mingguan</span>
                </div>
                <div class="db-chart-body" style="height: 250px;">
                    <canvas id="chartAdminTren"></canvas>
                </div>
            </div>
            <!-- akhir grafik tren kehadiran mingguan -->
        </div>
        <!-- akhir baris grafik admin -->

        <!-- tabel presensi dan agenda libur -->
        <div class="db-bottom-grid">
            <!-- tabel presensi pengguna -->
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Presensi Pengguna Hari Ini
                    </h3>
                    <a href="{{ route('absensi.riwayat-pengguna') }}" class="db-chart-badge" style="text-decoration:none;">Riwayat Lengkap</a>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pengguna</th>
                                <th>Role</th>
                                <th>NIP / NISN</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAbsensi as $idx => $ab)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 600;">{{ $ab['nama'] }}</td>
                                <td><span class="db-badge db-badge-alpha">{{ $ab['role'] }}</span></td>
                                <td>{{ $ab['nomor'] }}</td>
                                <td>{{ $ab['masuk'] }}</td>
                                <td>{{ $ab['pulang'] }}</td>
                                <td>
                                    @php
                                        $bClass = ($ab['status'] === 'terlambat') ? 'db-badge-lambat' : 'db-badge-tepat';
                                        $lbl = ($ab['status'] === 'terlambat') ? 'Terlambat' : 'Tepat Waktu';
                                    @endphp
                                    <span class="db-badge {{ $bClass }}">{{ $lbl }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="padding: 24px; color: #9CA3AF;">Belum ada aktivitas presensi pengguna hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- akhir tabel presensi pengguna -->

            <!-- daftar hari libur mendatang -->
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Hari Libur Terdekat
                    </h3>
                    <a href="{{ route('hari-libur.index') }}" class="db-chart-badge" style="text-decoration:none;">Kelola</a>
                </div>
                <div class="db-info-list">
                    @forelse($liburMendatang as $hl)
                    <div class="db-info-item">
                        <div class="db-info-date">
                            <span class="db-info-date-day">{{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('d') }}</span>
                            <span class="db-info-date-month">{{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('M') }}</span>
                        </div>
                        <div class="db-info-content">
                            <h4 class="db-info-title">{{ $hl->keterangan }}</h4>
                            <p class="db-info-sub">
                                {{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($hl->tanggal_selesai)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 20px; text-align: center; color: #9CA3AF;">
                        Tidak ada hari libur mendatang.
                    </div>
                    @endforelse
                </div>
            </div>
            <!-- akhir daftar hari libur mendatang -->
        </div>
        <!-- akhir tabel presensi dan agenda libur -->
    <!-- akhir dashboard peranan admin -->

    <!-- dashboard peranan guru -->
    @elseif($role === 'guru')
        <!-- kartu ringkasan guru -->
        <div class="db-stats-grid">
            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Total Murid Diampu</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalMuridDiampu }}</span>
                        <span class="db-card-sub">{{ count($kelasList) }} Kelas</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #ECFDF5; color: #0E774F;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Setoran Hafalan</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalSetoranKelas }}</span>
                        <span class="db-card-sub">{{ $setoranLancar }} status Lancar</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #EFF6FF; color: #2563EB;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Siswa Hadir Hari Ini</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $muridTepat + $muridLambat }}</span>
                        <span class="db-card-sub">Dari {{ $totalMuridDiampu }} siswa</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #FFFBEB; color: #D97706;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Presensi Guru</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number" style="font-size: 22px;">
                            @if($absensiGuruHariIni)
                                Hadir
                            @else
                                Belum
                            @endif
                        </span>
                        <span class="db-card-sub">
                            @if($absensiGuruHariIni)
                                Masuk: {{ $absensiGuruHariIni->waktu_masuk ?? '-' }}
                            @else
                                <a href="{{ route('absensi.kehadiran') }}" style="color:#0E774F; text-decoration:underline;">Buka Presensi QR</a>
                            @endif
                        </span>
                    </div>
                    <div class="db-card-icon-box" style="background: #F5F3FF; color: #7C3AED;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- akhir kartu ringkasan guru -->

        <!-- baris grafik guru -->
        <div class="db-charts-row">
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Presensi Siswa Hari Ini
                    </h3>
                    <span class="db-chart-badge">{{ date('d M Y') }}</span>
                </div>
                <div class="db-chart-body" style="height: 250px;">
                    <canvas id="chartGuruAbsensi"></canvas>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        Tren Setoran Hafalan Siswa
                    </h3>
                    <span class="db-chart-badge">7 Hari Terakhir</span>
                </div>
                <div class="db-chart-body" style="height: 250px;">
                    <canvas id="chartGuruSetoran"></canvas>
                </div>
            </div>
        </div>
        <!-- akhir baris grafik guru -->

        <!-- tabel setoran dan agenda guru -->
        <div class="db-bottom-grid">
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Setoran Hafalan Terbaru Kelas
                    </h3>
                    <a href="{{ route('tahfizd.index') }}" class="db-chart-badge" style="text-decoration:none;">Buka Tahfizd</a>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Murid</th>
                                <th>Surat & Ayat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSetoran as $idx => $st)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 600;">{{ $st->murid->user->nama ?? '-' }}</td>
                                <td>{{ $st->surat->nama_surat ?? '-' }} ({{ $st->ayat_mulai }}-{{ $st->ayat_selesai }})</td>
                                <td>{{ \Carbon\Carbon::parse($st->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'db-badge-lancar';
                                        if (in_array($st->status, ['Perlu Perbaikan', 'Kurang Lancar'])) $badgeClass = 'db-badge-perbaikan';
                                        elseif ($st->status === 'Mengulang') $badgeClass = 'db-badge-mengulang';
                                    @endphp
                                    <span class="db-badge {{ $badgeClass }}">{{ $st->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="padding: 24px; color: #9CA3AF;">Belum ada riwayat setoran di kelas ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Hari Libur Terdekat
                    </h3>
                    <span class="db-chart-badge">Informasi</span>
                </div>
                <div class="db-info-list">
                    @forelse($liburMendatang as $hl)
                    <div class="db-info-item">
                        <div class="db-info-date">
                            <span class="db-info-date-day">{{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('d') }}</span>
                            <span class="db-info-date-month">{{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('M') }}</span>
                        </div>
                        <div class="db-info-content">
                            <h4 class="db-info-title">{{ $hl->keterangan }}</h4>
                            <p class="db-info-sub">
                                {{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($hl->tanggal_selesai)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 20px; text-align: center; color: #9CA3AF;">
                        Tidak ada hari libur mendatang.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- akhir tabel setoran dan agenda guru -->
    <!-- akhir dashboard peranan guru -->

    <!-- dashboard peranan murid -->
    @else
        <!-- kartu ringkasan murid -->
        <div class="db-stats-grid">
            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Kelas Saya</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number" style="font-size: 26px;">{{ $kelas->nama_kelas ?? '-' }}</span>
                        <span class="db-card-sub">NISN: {{ $murid->nisn ?? '-' }}</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #ECFDF5; color: #0E774F;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Target Tuntas</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $persenTarget }}%</span>
                        <span class="db-card-sub">{{ $targetLulus }} dari {{ $totalTarget }} Surat</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #EFF6FF; color: #2563EB;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 10"></polyline>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Ayat Disetorkan</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalAyat }}</span>
                        <span class="db-card-sub">Ayat Al-Qur'an</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #FFFBEB; color: #D97706;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="db-stat-card">
                <div class="db-card-top">
                    <span class="db-card-title">Kehadiran Bulan Ini</span>
                    <span class="db-card-dots">&bull;&bull;&bull;</span>
                </div>
                <div class="db-card-bottom">
                    <div class="db-card-num-wrap">
                        <span class="db-card-number">{{ $totalHadirBulanIni }} Hari</span>
                        <span class="db-card-sub">{{ $hadirBulanIniTepat }} Tepat &bull; {{ $hadirBulanIniLambat }} Terlambat</span>
                    </div>
                    <div class="db-card-icon-box" style="background: #F5F3FF; color: #7C3AED;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- akhir kartu ringkasan murid -->

        <!-- baris grafik murid -->
        <div class="db-charts-row">
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                        </svg>
                        Progres Target Hafalan Kelas
                    </h3>
                    <span class="db-chart-badge">{{ $persenTarget }}% Tuntas</span>
                </div>
                <div class="db-chart-body" style="height: 250px; display: flex; align-items: center; justify-content: center;">
                    @if($totalTarget > 0)
                        <canvas id="chartMuridTarget"></canvas>
                    @else
                        <div style="text-align: center; padding: 20px;">
                            <div style="width: 54px; height: 54px; border-radius: 50%; background: #ECFDF5; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                                    <path d="M12 2l10 5-10 5L2 7l10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                            </div>
                            <div style="font-weight: 600; font-size: 14px; color: #334155; margin-bottom: 4px;">Belum Ada Target Hafalan</div>
                            <div style="font-size: 12px; color: #94A3B8; max-width: 250px; margin: 0 auto; line-height: 1.4;">
                                @if(!$kelas)
                                    Akun Anda belum didaftarkan ke kelas oleh Guru.
                                @else
                                    Target surat hafalan untuk kelas {{ $kelas->nama_kelas }} belum ditentukan oleh Guru.
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        Tren Setoran Hafalan Saya
                    </h3>
                    <span class="db-chart-badge">7 Hari Terakhir</span>
                </div>
                <div class="db-chart-body" style="height: 250px;">
                    <canvas id="chartMuridSetoran"></canvas>
                </div>
            </div>
        </div>
        <!-- akhir baris grafik murid -->

        <!-- tabel setoran dan agenda murid -->
        <div class="db-bottom-grid">
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Riwayat Setoran Hafalan Saya
                    </h3>
                    <span class="db-chart-badge">5 Terakhir</span>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Surat</th>
                                <th>Ayat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSetoran as $idx => $st)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 600;">{{ $st->surat->nama_surat ?? '-' }}</td>
                                <td>Ayat {{ $st->ayat_mulai }} - {{ $st->ayat_selesai }}</td>
                                <td>{{ \Carbon\Carbon::parse($st->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'db-badge-lancar';
                                        if (in_array($st->status, ['Perlu Perbaikan', 'Kurang Lancar'])) $badgeClass = 'db-badge-perbaikan';
                                        elseif ($st->status === 'Mengulang') $badgeClass = 'db-badge-mengulang';
                                    @endphp
                                    <span class="db-badge {{ $badgeClass }}">{{ $st->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="padding: 24px; color: #9CA3AF;">Belum ada riwayat setoran hafalan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3 class="db-chart-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E774F" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Hari Libur Terdekat
                    </h3>
                    <span class="db-chart-badge">Informasi</span>
                </div>
                <div class="db-info-list">
                    @forelse($liburMendatang as $hl)
                    <div class="db-info-item">
                        <div class="db-info-date">
                            <span class="db-info-date-day">{{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('d') }}</span>
                            <span class="db-info-date-month">{{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('M') }}</span>
                        </div>
                        <div class="db-info-content">
                            <h4 class="db-info-title">{{ $hl->keterangan }}</h4>
                            <p class="db-info-sub">
                                {{ \Carbon\Carbon::parse($hl->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($hl->tanggal_selesai)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 20px; text-align: center; color: #9CA3AF;">
                        Tidak ada hari libur mendatang.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- akhir tabel setoran dan agenda murid -->
    <!-- akhir dashboard peranan murid -->
    @endif

</div>
@endsection

@section('scripts')
<!-- pustaka grafik chart js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- akhir pustaka grafik chart js -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const role = "{{ $role }}";

    if (role === 'admin') {
        // grafik lingkaran absensi admin
        const ctxAbsensi = document.getElementById('chartAdminAbsensi');
        if (ctxAbsensi) {
            new Chart(ctxAbsensi, {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat', 'Belum Absen'],
                    datasets: [{
                        data: [{{ $hadirTepat ?? 0 }}, {{ $hadirTerlambat ?? 0 }}, {{ $belumAbsen ?? 0 }}],
                        backgroundColor: ['#0E774F', '#F59E0B', '#E2E8F0'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '65%'
                }
            });
        }

        // akhir grafik lingkaran absensi admin

        // grafik tren kehadiran admin
        const canvasTren = document.getElementById('chartAdminTren');
        if (canvasTren) {
            const ctxTren = canvasTren.getContext('2d');
            const gradTren = ctxTren.createLinearGradient(0, 0, 0, 230);
            gradTren.addColorStop(0, 'rgba(14, 119, 79, 0.28)');
            gradTren.addColorStop(1, 'rgba(14, 119, 79, 0.00)');

            new Chart(ctxTren, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartDates ?? []) !!},
                    datasets: [{
                        label: 'Pengguna Hadir',
                        data: {!! json_encode($chartHadir ?? []) !!},
                        borderColor: '#0E774F',
                        borderWidth: 2.8,
                        backgroundColor: gradTren,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#0E774F',
                        pointBorderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { precision: 0, color: '#64748B' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748B' }
                        }
                    }
                }
            });
        }
        // akhir grafik tren kehadiran admin
    } else if (role === 'guru') {
        // grafik lingkaran presensi siswa
        const ctxGuruAbsensi = document.getElementById('chartGuruAbsensi');
        if (ctxGuruAbsensi) {
            new Chart(ctxGuruAbsensi, {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat', 'Belum Absen'],
                    datasets: [{
                        data: [{{ $muridTepat ?? 0 }}, {{ $muridLambat ?? 0 }}, {{ $muridBelum ?? 0 }}],
                        backgroundColor: ['#0E774F', '#F59E0B', '#E2E8F0'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '65%'
                }
            });
        }

        // akhir grafik lingkaran presensi siswa

        // grafik tren setoran kelas
        const canvasGuruSetoran = document.getElementById('chartGuruSetoran');
        if (canvasGuruSetoran) {
            const ctxGuru = canvasGuruSetoran.getContext('2d');
            const gradGuru = ctxGuru.createLinearGradient(0, 0, 0, 230);
            gradGuru.addColorStop(0, 'rgba(14, 119, 79, 0.32)');
            gradGuru.addColorStop(1, 'rgba(14, 119, 79, 0.00)');

            new Chart(ctxGuru, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartDates ?? []) !!},
                    datasets: [{
                        label: 'Setoran Hafalan',
                        data: {!! json_encode($chartSetoran ?? []) !!},
                        borderColor: '#0E774F',
                        borderWidth: 2.8,
                        backgroundColor: gradGuru,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#0E774F',
                        pointBorderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { precision: 0, color: '#64748B' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748B' }
                        }
                    }
                }
            });
        }
        // akhir grafik tren setoran kelas
    } else if (role === 'murid') {
        // grafik donat kemajuan target murid
        const ctxMuridTarget = document.getElementById('chartMuridTarget');
        if (ctxMuridTarget) {
            const lulus = {{ $targetLulus ?? 0 }};
            const total = {{ $totalTarget ?? 0 }};
            const sisa = Math.max(0, total - lulus);
            new Chart(ctxMuridTarget, {
                type: 'doughnut',
                data: {
                    labels: ['Surat Tuntas', 'Belum Tuntas'],
                    datasets: [{
                        data: total > 0 ? [lulus, sisa] : [0, 1],
                        backgroundColor: total > 0 ? ['#0E774F', '#E2E8F0'] : ['#E2E8F0', '#E2E8F0'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '70%'
                }
            });
        }

        // akhir grafik donat kemajuan target murid

        // grafik tren setoran pribadi
        const canvasMuridSetoran = document.getElementById('chartMuridSetoran');
        if (canvasMuridSetoran) {
            const ctxMurid = canvasMuridSetoran.getContext('2d');
            const gradMurid = ctxMurid.createLinearGradient(0, 0, 0, 230);
            gradMurid.addColorStop(0, 'rgba(14, 119, 79, 0.32)');
            gradMurid.addColorStop(1, 'rgba(14, 119, 79, 0.00)');

            new Chart(ctxMurid, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartDates ?? []) !!},
                    datasets: [{
                        label: 'Setoran Saya',
                        data: {!! json_encode($chartSetoran ?? []) !!},
                        borderColor: '#0E774F',
                        borderWidth: 2.8,
                        backgroundColor: gradMurid,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#0E774F',
                        pointBorderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { precision: 0, color: '#64748B' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748B' }
                        }
                    }
                }
            });
        }
        // akhir grafik tren setoran pribadi
    }
});
</script>
@endsection
