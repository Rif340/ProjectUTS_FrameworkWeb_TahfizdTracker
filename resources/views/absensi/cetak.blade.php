@extends('layouts.blank', ['title' => $judulLaporan ?? 'Laporan Rekapitulasi Kehadiran'])

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulLaporan ?? 'Laporan Kehadiran' }} - SDIT At-Taubah</title>
    <link rel="stylesheet" href="{{ asset('css/cetak-laporan.css') }}">
</head>
<body>

    <!-- bilah aksi atas cetak -->
    <div class="print-toolbar no-print">
        <a href="{{ $backUrl ?? route('absensi.detail') }}" class="btn-toolbar btn-kembali">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span>Kembali</span>
        </a>

        <div class="toolbar-right">
            <a href="{{ $exportUrl ?? route('absensi.export.excel') }}" class="btn-toolbar btn-excel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="8" y1="13" x2="16" y2="13"></line>
                    <line x1="8" y1="17" x2="16" y2="17"></line>
                </svg>
                <span>Export Excel</span>
            </a>

            <button type="button" onclick="window.print()" class="btn-toolbar btn-print">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>
    <!-- akhir bilah aksi atas cetak -->

    <!-- lembar dokumen cetak -->
    <div class="paper-sheet">

        <!-- kop surat -->
        <div class="school-letterhead">
            <img src="/gambar/logo.png" alt="Logo SDIT At-Taubah" class="school-logo-img">
            <h1 class="school-name">SDIT AT-TAUBAH</h1>
            <p class="school-address">
                Jl. KH. Ahmad Dahlan No. E/4 Kaum 1, Kelurahan Karawang Kulon, Kecamatan Karawang Barat, Kabupaten Karawang, Jawa Barat
            </p>
        </div>
        <!-- akhir kop surat -->

        <div class="rule-thick"></div>
        <div class="rule-gap"></div>
        <div class="rule-thin"></div>

        <!-- judul laporan -->
        <div class="report-heading">
            <h2 class="report-title">{{ $judulLaporan ?? 'LAPORAN REKAPITULASI KEHADIRAN' }}</h2>
            <p class="report-subtitle">Periode {{ $bulanTahun }}</p>
        </div>
        <!-- akhir judul laporan -->

        <!-- rincian informasi laporan -->
        <div class="report-details-grid">
            <div class="details-col">
                <div class="details-row">
                    <span class="details-label">Periode laporan</span>
                    <span>: {{ $bulanTahun }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">{{ !empty($isIndividu) ? 'Nama pengguna' : 'Total pengguna' }}</span>
                    <span>: {{ !empty($isIndividu) ? ($laporan[0]['nama'] ?? '-') : (count($laporan) . ' orang') }}</span>
                </div>
            </div>

            <div class="details-col">
                <div class="details-row">
                    <span class="details-label">Jumlah hari kerja</span>
                    <span>: {{ $jumlahHariKerja }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Tanggal cetak</span>
                    <span>: {{ $tanggalCetak }}</span>
                </div>
            </div>
        </div>
        <!-- akhir rincian informasi laporan -->

        <!-- tabel rekapitulasi kehadiran -->
        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 45px;">No</th>
                    <th rowspan="2" style="width: 280px;">Nama Pengguna</th>
                    <th rowspan="2" style="width: 120px;">Peran / Jabatan</th>
                    <th colspan="3">Rekapitulasi Kehadiran</th>
                    <th rowspan="2" style="width: 95px;">Hari<br>Kerja</th>
                </tr>
                <tr>
                    <th style="width: 75px;">Hadir</th>
                    <th style="width: 95px;">Terlambat</th>
                    <th style="width: 125px;">Tidak Hadir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $row)
                    <tr>
                        <td class="text-center">{{ $row['no'] }}</td>
                        <td class="text-center">{{ $row['nama'] }}</td>
                        <td class="text-center">{{ $row['jabatan'] }}</td>
                        <td class="text-center">{{ $row['hadir'] }}</td>
                        <td class="text-center">{{ $row['terlambat'] }}</td>
                        <td class="text-center">{{ $row['tidak_hadir'] }}</td>
                        <td class="text-center">{{ $row['hari_kerja'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- akhir tabel rekapitulasi kehadiran -->

        <!-- tanda tangan pengesahan -->
        <div class="signatures-wrapper">
            <div class="signature-box">
                <p class="sig-date">Karawang, {{ $tanggalCetak }}</p>
                <p class="sig-title">Admin Tata Usaha</p>
                <div class="sig-name-container">
                    <span>(</span>
                    <div class="sig-line"></div>
                    <span>)</span>
                </div>
            </div>

            <div class="signature-box">
                <p class="sig-date">Karawang, {{ $tanggalCetak }}</p>
                <p class="sig-title">Kepala SDIT At-Taubah</p>
                <div class="sig-name-container">
                    <span>(</span>
                    <div class="sig-line"></div>
                    <span>)</span>
                </div>
            </div>
        </div>
        <!-- akhir tanda tangan pengesahan -->

    </div>
    <!-- akhir lembar dokumen cetak -->

</body>
</html>
@endsection
