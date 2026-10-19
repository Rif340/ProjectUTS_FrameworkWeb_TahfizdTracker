<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Kehadiran Guru - SDIT At-Taubah</title>

    <link rel="stylesheet" href="/css/cetak-laporan.css">
</head>
<body>

    <!-- TOP TOOLBAR (Hidden when printed) -->
    <div class="toolbar-top">
        <div class="toolbar-left">
            <a href="{{ $backUrl ?? route('absensi.detail') }}" class="btn-toolbar btn-back">
                &larr; Kembali
            </a>
            <span style="font-size: 14px; font-weight: 600; color: #111;">
                Pratinjau Dokumen Cetak Kehadiran
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <!-- Download Excel Format -->
            <a href="{{ $exportUrl ?? route('absensi.export.excel') }}" class="btn-toolbar btn-excel">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="8" y1="13" x2="16" y2="13"></line>
                    <line x1="8" y1="17" x2="16" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span>Unduh Format Excel</span>
            </a>

            <!-- Print / Save to PDF -->
            <button type="button" class="btn-toolbar btn-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- PRINTABLE PAPER SHEET (Figma Node 639:8484) -->
    <div class="paper-sheet">

        <!-- 1. KOP SURAT (Figma Node 639:8485) -->
        <div class="school-letterhead">
            <img src="/gambar/logo.png" alt="Logo SDIT At-Taubah" class="school-logo-img">
            <h1 class="school-name">SDIT AT-TAUBAH</h1>
            <p class="school-address">
                Jl. KH. Ahmad Dahlan No. E/4 Kaum 1, Kelurahan Karawang Kulon, Kecamatan Karawang Barat, Kabupaten Karawang, Jawa Barat
            </p>
        </div>

        <div class="rule-thick"></div>
        <div class="rule-gap"></div>
        <div class="rule-thin"></div>

        <!-- 2. JUDUL LAPORAN (Figma Node 639:8493) -->
        <div class="report-heading">
            <h2 class="report-title">{{ $judulLaporan ?? 'LAPORAN REKAPITULASI KEHADIRAN' }}</h2>
            <p class="report-subtitle">Periode {{ $bulanTahun }}</p>
        </div>

        <!-- 3. DETAIL LAPORAN / METADATA (Figma Node 639:8496) -->
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

        <!-- 4. TABEL REKAPITULASI KEHADIRAN (Figma Node 639:8515) -->
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

        <!-- 5. TANDA TANGAN OTORISASI (Figma Node 639:8574) -->
        <div class="signatures-wrapper">
            <!-- Tanda Tangan Kiri: Admin Tata Usaha -->
            <div class="signature-box">
                <p class="sig-date">Karawang, {{ $tanggalCetak }}</p>
                <p class="sig-title">Admin Tata Usaha</p>
                <div class="sig-name-container">
                    <span>(</span>
                    <div class="sig-line"></div>
                    <span>)</span>
                </div>
            </div>

            <!-- Tanda Tangan Kanan: Kepala SDIT At-Taubah -->
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

    </div>

</body>
</html>
