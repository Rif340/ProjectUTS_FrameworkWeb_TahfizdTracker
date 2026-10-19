<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Setoran Tahfizd - {{ $murid->user->nama ?? ($kelas->nama_kelas ?? 'SDIT At-Taubah') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Tinos:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        /* === PAGE SETUP: A4 LANDSCAPE === */
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 8mm 10mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 10pt;
            color: #000000;
            background: #FFFFFF;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #F3F4F6;
            border-bottom: 1px solid #E5E7EB;
            padding: 12px 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .no-print-bar-title {
            font-size: 14px;
            font-weight: 600;
            color: #1F2937;
        }
        .no-print-bar-actions {
            display: flex;
            gap: 10px;
        }
        .btn-action-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0E7651;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-action-print:hover { opacity: 0.9; }
        .btn-action-back {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            color: #374151;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-action-back:hover { background: #E5E7EB; }

        /* Main Report Container */
        .rekap-sheet {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 10px 10px 0;
            background: #FFFFFF;
        }

        /* --- School Letterhead (Kop Surat - Figma Node 439:3679) --- */
        .kop-surat {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 85px;
            margin-bottom: 4px;
            text-align: center;
        }
        .kop-logo {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .kop-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 95px;
        }
        .kop-instansi {
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 22pt;
            font-weight: 700;
            color: #000000;
            line-height: 1.15;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .kop-alamat {
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 9.5pt;
            color: #000000;
            line-height: 1.35;
            margin-top: 4px;
            max-width: 680px;
            text-align: center;
        }

        /* Double Rule (Figma 439:3684 & 439:3686) */
        .kop-rule-thick {
            width: 100%;
            height: 3px;
            background: #000000;
            margin-bottom: 2px;
        }
        .kop-rule-thin {
            width: 100%;
            height: 1px;
            background: #000000;
            margin-bottom: 12px;
        }

        /* --- Report Heading (Figma Node 439:3687) --- */
        .report-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title {
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 15pt;
            font-weight: 700;
            text-decoration: underline;
            text-underline-position: under;
            color: #000000;
            line-height: 1.25;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .report-period {
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 11pt;
            color: #000000;
            margin-top: 2px;
        }

        /* --- Report Details Metadata (Figma Node 439:3690) --- */
        .report-meta-grid {
            display: flex;
            justify-content: space-between;
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 10.5pt;
            color: #000000;
            line-height: 1.35;
            margin-bottom: 10px;
            padding: 0 2px;
        }
        .meta-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .meta-item {
            display: flex;
            align-items: center;
        }
        .meta-label {
            width: 165px;
        }
        .meta-sep {
            width: 12px;
            text-align: center;
        }
        .meta-value {
            font-weight: 400;
        }

        /* --- Table Layout (Figma Node 439:3709) --- */
        .table-wrap {
            width: 100%;
            margin-bottom: 16px;
        }
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #000000;
        }
        .rekap-table th,
        .rekap-table td {
            border: 1px solid #000000;
            box-sizing: border-box;
        }

        /* Header styling */
        .th-no {
            width: 28px;
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 10pt;
            font-weight: 700;
            vertical-align: middle;
            background: #FFFFFF;
        }
        .th-nama {
            width: 145px;
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 10pt;
            font-weight: 700;
            vertical-align: middle;
            background: #FFFFFF;
        }
        .th-rekap {
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 10.5pt;
            font-weight: 700;
            height: 26px;
            vertical-align: middle;
            background: #FFFFFF;
        }
        .th-total {
            width: 58px;
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 10pt;
            font-weight: 700;
            vertical-align: middle;
            background: #FFFFFF;
        }

        /* Vertical Surat Column Header */
        .th-surat {
            height: 98px;
            vertical-align: bottom;
            text-align: center;
            padding: 4px 1px;
            background: #FFFFFF;
        }
        .th-surat-txt {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.2pt;
            font-weight: 700;
            line-height: 1;
            display: inline-block;
            margin: 0 auto;
            text-align: left;
        }

        /* Body cell styling */
        .td-no {
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 9pt;
            height: 25px;
            vertical-align: middle;
        }
        .td-nama {
            text-align: left;
            padding-left: 6px;
            padding-right: 4px;
            font-family: 'Tinos', serif;
            font-size: 9pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }
        .td-status {
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 8.5pt;
            vertical-align: middle;
            padding: 2px 0;
        }
        .td-status.is-lulus {
            font-weight: 700;
            font-size: 9.5pt;
        }
        .td-total {
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 9.5pt;
            font-weight: 400;
            vertical-align: middle;
        }

        /* Footer Total row (Figma 439:4362) */
        .td-total-label {
            text-align: right;
            padding-right: 10px;
            font-family: 'Tinos', serif;
            font-size: 9.5pt;
            font-weight: 700;
            height: 26px;
            vertical-align: middle;
        }
        .td-total-col {
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 8.5pt;
            font-weight: 700;
            vertical-align: middle;
        }
        .td-total-grand {
            text-align: center;
            font-family: 'Tinos', serif;
            font-size: 9.5pt;
            font-weight: 700;
            vertical-align: middle;
        }

        /* --- Report Authorization / Signatures (Figma Node 439:4441) --- */
        .signatures-wrap {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 10pt;
            line-height: 1.35;
            page-break-inside: avoid;
            padding: 0 20px;
        }
        .sig-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 260px;
            text-align: center;
        }
        .sig-date {
            margin: 0;
        }
        .sig-role {
            margin: 1px 0 52px;
        }
        .sig-name {
            font-weight: 400;
            margin: 0;
            white-space: nowrap;
        }

        /* Print Media Query */
        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: #FFFFFF !important;
            }
            .rekap-sheet {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Screen-Only Action Bar --}}
    <div class="no-print-bar">
        <span class="no-print-bar-title">Pratinjau Cetak: Rekapitulasi Tahfizd (Format A4 Landscape)</span>
        <div class="no-print-bar-actions">
            <button type="button" class="btn-action-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak Dokumen</span>
            </button>
            <a href="javascript:history.back()" class="btn-action-back">Tutup / Kembali</a>
        </div>
    </div>

    <div class="rekap-sheet">

        {{-- ---- KOP SURAT (Figma Node 439:3679) ---- --}}
        <div class="kop-surat">
            <img src="/gambar/logo.png" alt="Logo" class="kop-logo">
            <div class="kop-content">
                <h1 class="kop-instansi">SDIT AT-TAUBAH</h1>
                <p class="kop-alamat">
                    Jl. KH. Ahmad Dahlan No. E/4 Kaum 1, Kelurahan Karawang Kulon, Kecamatan Karawang Barat, Kabupaten Karawang, Jawa Barat
                </p>
            </div>
        </div>

        {{-- Double Rule --}}
        <div class="kop-rule-thick"></div>
        <div class="kop-rule-thin"></div>

        {{-- ---- JUDUL LAPORAN (Figma Node 439:3687) ---- --}}
        <div class="report-header">
            <h2 class="report-title">LAPORAN REKAPITULASI SETORAN TAHFIZD</h2>
            @php
                $ta = $kelas->tahunAkademik ?? ($murid->kelas->tahunAkademik ?? null);
                if ($ta && $ta->tanggal_mulai && $ta->tanggal_selesai) {
                    $start = \Carbon\Carbon::parse($ta->tanggal_mulai)->locale('id');
                    $end   = \Carbon\Carbon::parse($ta->tanggal_selesai)->locale('id');
                    if ($start->year === $end->year) {
                        $periodeText = $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F Y');
                    } else {
                        $periodeText = $start->translatedFormat('F Y') . ' - ' . $end->translatedFormat('F Y');
                    }
                } elseif ($ta && $ta->nama_tahun) {
                    $periodeText = 'Tahun Ajaran ' . $ta->nama_tahun . ($ta->semester ? ' Semester ' . $ta->semester : '');
                } else {
                    $periodeText = \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y');
                }
            @endphp
            <p class="report-period">
                Periode {{ $periodeText }}
            </p>
        </div>

        {{-- ---- DETAIL LAPORAN METADATA (Figma Node 439:3690) ---- --}}
        <div class="report-meta-grid">
            <div class="meta-col">
                <div class="meta-item">
                    <span class="meta-label">Kelas</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-value">{{ $kelas->nama_kelas ?? ($murid->kelas->nama_kelas ?? '-') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Guru Pengampu</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-value">{{ $kelas->guru->user->nama ?? ($murid->kelas->guru->user->nama ?? '-') }}</span>
                </div>
            </div>
            <div class="meta-col">
                <div class="meta-item">
                    <span class="meta-label">Jumlah Mencapai Target</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-value">{{ $jumlahTargetTuntas ?? 0 }} Orang</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal cetak</span>
                    <span class="meta-sep">:</span>
                    <span class="meta-value">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}</span>
                </div>
            </div>
        </div>

        {{-- ---- TABEL REKAPITULASI (Figma Node 439:3709) ---- --}}
        <div class="table-wrap">
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="th-no">No</th>
                        <th rowspan="2" class="th-nama">Nama Siswa</th>
                        <th colspan="{{ count($suratList) }}" class="th-rekap">Rekapitulasi Surat</th>
                        <th rowspan="2" class="th-total">Total Surat</th>
                    </tr>
                    <tr>
                        @foreach($suratList as $s)
                        <th class="th-surat" title="{{ $s->nama_surat }} ({{ $s->jumlah_ayat }} Ayat)">
                            <div class="th-surat-txt">
                                {{ $s->nama_surat }} ({{ $s->jumlah_ayat }})
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                    <tr>
                        <td class="td-no">{{ $i + 1 }}</td>
                        <td class="td-nama" title="{{ $row['murid']->user->nama ?? '-' }}">
                            {{ $row['murid']->user->nama ?? '-' }}
                        </td>
                        @foreach($suratList as $s)
                            @php
                                $cell = $row['suratStatus'][$s->id_surat] ?? ['symbol' => '—'];
                                $isPass = ($cell['symbol'] === '✓');
                            @endphp
                            <td class="td-status {{ $isPass ? 'is-lulus' : '' }}">
                                {{ $cell['symbol'] }}
                            </td>
                        @endforeach
                        <td class="td-total">{{ $row['totalSuratSelesai'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($suratList) + 3 }}" style="text-align: center; padding: 12px; font-size: 9pt;">
                            Tidak ada data siswa untuk ditampilkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @if(count($rows) <= 1)
                    <tr>
                        <td colspan="2" class="td-total-label">TOTAL</td>
                        <td colspan="{{ count($suratList) }}" style="text-align: center; font-family: 'Tinos', serif; font-size: 9.5pt; font-weight: 700;">
                            {{ $grandTotalSelesai ?? 0 }} Surat Selesai
                        </td>
                        <td class="td-total-grand">
                            {{ $grandTotalSelesai ?? 0 }}
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" class="td-total-label">TOTAL</td>
                        @foreach($suratList as $s)
                        <td class="td-total-col">
                            {{ $totalPerSurat[$s->id_surat] ?? 0 }}
                        </td>
                        @endforeach
                        <td class="td-total-grand">
                            {{ $grandTotalSelesai ?? 0 }}
                        </td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>

        {{-- ---- PENGESAHAN / TANDA TANGAN (Figma Node 439:4441) ---- --}}
        <div class="signatures-wrap">
            {{-- Guru Pengampu (Left) --}}
            <div class="sig-box">
                <p class="sig-date">Karawang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}</p>
                <p class="sig-role">Guru Pengampu</p>
                <p class="sig-name">( {{ $kelas->guru->user->nama ?? ($murid->kelas->guru->user->nama ?? '__________________________') }} )</p>
            </div>

            {{-- Kepala SDIT At-Taubah (Right) --}}
            <div class="sig-box">
                <p class="sig-date">Karawang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}</p>
                <p class="sig-role">Kepala SDIT At-Taubah</p>
                <p class="sig-name">( Hj.R.Rina Noor'aeni, SE )</p>
            </div>
        </div>

    </div>

</body>
</html>
