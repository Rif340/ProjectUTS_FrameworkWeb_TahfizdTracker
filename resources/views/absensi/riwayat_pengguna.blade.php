@extends('layouts.app')

@section('title', 'Riwayat Absensi Pengguna')

@section('breadcrumb')
<div class="breadcrumb-nav">
    <a href="{{ route('absensi.kehadiran') }}" class="breadcrumb-link">
        Absensi
    </a>
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="transform: rotate(-90deg);">
        <path d="M2.5 3.75L5 6.25L7.5 3.75" stroke="#71717A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="breadcrumb-current">
        Riwayat Absensi Pengguna
    </span>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/css/riwayat-pengguna.css">
@endsection

@section('content')
    <!-- Card Header -->
    <div class="detail-card-header">
        <div class="detail-header-left">
            <img src="/icon/icon_account_school.svg" alt="Riwayat Absensi Pengguna">
            <span>Riwayat Absensi Pengguna</span>
        </div>

        <div class="detail-header-actions">
            <!-- Tombol Cetak Kehadiran Seluruh Pengguna (Khusus Admin) -->
            <a href="{{ route('absensi.cetak.semua') }}" class="btn-action-print">
                <img src="/icon/icon_print_kehadiran.svg" alt="Print">
                <span>Cetak Kehadiran</span>
            </a>
        </div>
    </div>

    <!-- Scrollable Content Body -->
    <div class="detail-card-body">
        <div>
            <h3 class="section-title" style="margin-bottom:1rem;" >Semua Riwayat Presensi Pengguna</h3>

            <div class="table-container-responsive">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 160px;">Nama Pengguna</th>
                            <th style="width: 90px;">Role</th>
                            <th style="width: 120px;">NIP / NISN</th>
                            <th style="width: 95px;">Hari</th>
                            <th style="width: 105px;">Tanggal</th>
                            <th style="width: 110px;">Kelas</th>
                            <th style="width: 110px;">Waktu Masuk</th>
                            <th style="width: 110px;">Waktu Pulang</th>
                            <th style="width: 130px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semuaRiwayat as $index => $row)
                            @php
                                $roleLower = strtolower($row['role']);
                                $statusRaw = strtolower($row['status']);
                                $statusClass = ($statusRaw === 'tepat waktu') ? 'tepat' : (($statusRaw === 'terlambat') ? 'terlambat' : 'tidak-masuk');
                                $statusLabel = ($statusRaw === 'tepat waktu') ? 'Tepat Waktu' : (($statusRaw === 'terlambat') ? 'Terlambat' : 'Tidak Hadir');
                            @endphp
                            <tr>
                                <td style="font-weight: 600;">{{ $index + 1 }}</td>
                                <td style="text-align: left; padding-left: 14px; font-weight: 600;">{{ $row['nama'] }}</td>
                                <td>
                                    <span class="role-badge {{ $roleLower }}">{{ $row['role'] }}</span>
                                </td>
                                <td>{{ $row['nomor_induk'] }}</td>
                                <td>{{ $row['hari'] }}</td>
                                <td>{{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') : '-' }}</td>
                                <td>{{ $row['kelas'] }}</td>
                                <td>{{ $row['waktu_masuk'] }}</td>
                                <td>{{ $row['waktu_pulang'] }}</td>
                                <td class="status-cell {{ $statusClass }}">{{ $statusLabel }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 30px; color: #71717A;">
                                    Belum ada data riwayat absensi pengguna yang tercatat di database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
