@extends('layouts.app')

@section('title', 'Riwayat Absensi Murid')

@section('breadcrumb')
<div class="breadcrumb-nav">
    <a href="{{ route('absensi.kehadiran') }}" class="breadcrumb-link">
        Absensi
    </a>
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="transform: rotate(-90deg);">
        <path d="M2.5 3.75L5 6.25L7.5 3.75" stroke="#71717A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="breadcrumb-current">
        Riwayat Absensi
    </span>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/css/riwayat-absensi.css">
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printAreaPresensiQR, #printAreaPresensiQR * {
        visibility: visible !important;
    }
    #printAreaPresensiQR {
        position: fixed !important;
        left: 50% !important;
        top: 40% !important;
        transform: translate(-50%, -50%) !important;
        width: 280px !important;
        padding: 24px !important;
        background: #ffffff !important;
        border: 2px dashed #0E7651 !important;
        border-radius: 12px !important;
        box-shadow: none !important;
        text-align: center !important;
    }
    #printAreaPresensiQR img {
        width: 200px !important;
        height: 200px !important;
        margin: 0 auto !important;
        display: block !important;
    }
}
</style>
@endsection

@section('content')
    <!-- Card Header (Figma Node 638:4200) -->
    <div class="detail-card-header">
        <div class="detail-header-left">
            <img src="/icon/icon_account_school.svg" alt="Riwayat Absensi">
            <span>Riwayat Absensi</span>
        </div>

        <div class="detail-header-actions">
            <!-- Tombol Oranye: Cetak QR Code -->
            <button type="button" class="btn-action-qr" onclick="openQRModal()">
                <img src="/icon/icon_cetak_qrcode.svg" alt="QR Code">
                <span>Cetak QR Code</span>
            </button>

            <!-- Tombol Hijau: Cetak Kehadiran Pribadi Murid -->
            <a href="{{ route('absensi.cetak') }}" class="btn-action-print" style="text-decoration: none;">
                <img src="/icon/icon_print_kehadiran.svg" alt="Print">
                <span>Cetak Kehadiran</span>
            </a>
        </div>
    </div>

    <!-- Scrollable Content Body -->
    <div class="detail-card-body">

        <!-- ==================== BAGIAN 1: STATISTIK KEHADIRAN ==================== -->
        <div>
            <h3 class="section-title">Statistik Kehadiran</h3>

            <div class="stats-card-box">
                <div class="stats-user-name">{{ $user->nama }}</div>

                <div class="chart-container-flex">
                    <!-- Donut Chart -->
                    @php
                        $tepat = $chartStats['tepat_waktu'];
                        $telat = $chartStats['terlambat'];
                        $alpa  = $chartStats['tidak_masuk'];
                        $total = $chartStats['total'];

                        // Keliling lingkaran SVG (2 * PI * r = 2 * 3.14159 * 75 = 471.24)
                        $circumference = 471.24;

                        if ($total > 0) {
                            $strokeTepat = ($tepat / $total) * $circumference;
                            $strokeTelat = ($telat / $total) * $circumference;
                            $strokeAlpa  = ($alpa  / $total) * $circumference;
                        } else {
                            $strokeTepat = 0;
                            $strokeTelat = 0;
                            $strokeAlpa  = 0;
                        }

                        $offsetTelat = -($strokeTepat);
                        $offsetAlpa  = -($strokeTepat + $strokeTelat);
                    @endphp

                    <div class="donut-chart-box" id="donutChartBox">
                        <svg width="190" height="190" viewBox="0 0 190 190">
                            <!-- Background track ring (abu-abu) -->
                            <circle cx="95" cy="95" r="75" fill="none" stroke="#F3F4F6" stroke-width="26" />

                            @if($total > 0)
                                @php
                                    $pctTepat = $total > 0 ? round(($tepat / $total) * 100, 1) : 0;
                                    $pctTelat = $total > 0 ? round(($telat / $total) * 100, 1) : 0;
                                    $pctAlpa  = $total > 0 ? round(($alpa  / $total) * 100, 1) : 0;
                                @endphp

                                @if($tepat > 0)
                                <!-- Slice 1: Tepat Waktu (Blue) -->
                                <circle class="donut-slice" cx="95" cy="95" r="75" fill="none" stroke="#638CFF" stroke-width="26"
                                    stroke-dasharray="{{ $strokeTepat }} {{ $circumference }}" stroke-dashoffset="0"
                                    data-title="Tepat Waktu" data-value="{{ $tepat }}" data-percent="{{ $pctTepat }}%" data-color="#638CFF" />
                                @endif

                                @if($telat > 0)
                                <!-- Slice 2: Terlambat (Green) -->
                                <circle class="donut-slice" cx="95" cy="95" r="75" fill="none" stroke="#5CD096" stroke-width="26"
                                    stroke-dasharray="{{ $strokeTelat }} {{ $circumference }}" stroke-dashoffset="{{ $offsetTelat }}"
                                    data-title="Terlambat" data-value="{{ $telat }}" data-percent="{{ $pctTelat }}%" data-color="#5CD096" />
                                @endif

                                @if($alpa > 0)
                                <!-- Slice 3: Tidak Masuk (Orange) -->
                                <circle class="donut-slice" cx="95" cy="95" r="75" fill="none" stroke="#FFA800" stroke-width="26"
                                    stroke-dasharray="{{ $strokeAlpa }} {{ $circumference }}" stroke-dashoffset="{{ $offsetAlpa }}"
                                    data-title="Tidak Masuk" data-value="{{ $alpa }}" data-percent="{{ $pctAlpa }}%" data-color="#FFA800" />
                                @endif
                            @endif
                        </svg>

                        <!-- Center Total Badge -->
                        <div class="donut-center-text">
                            <span class="donut-center-label">Total</span>
                            <span class="donut-center-value">{{ $total > 0 ? $total : '-' }}</span>
                        </div>

                        <!-- Tooltip Popup (Google Looker Style) -->
                        <div class="chart-tooltip-popup" id="chartTooltip">
                            <div class="tooltip-header">
                                <span class="tooltip-dot" id="tooltipDot"></span>
                                <span class="tooltip-title" id="tooltipTitle">Status</span>
                            </div>
                            <div class="tooltip-value-line">
                                <span id="tooltipValue">0</span> <span class="tooltip-percent" id="tooltipPercent">(0%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Legends -->
                    <div class="chart-legends-wrapper">
                        <div class="legend-item">
                            <span class="legend-dot blue"></span>
                            <span>Tepat Waktu</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot green"></span>
                            <span>Terlambat</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot orange"></span>
                            <span>Tidak Masuk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== BAGIAN 2: RIWAYAT KEHADIRAN MURID ==================== -->
        <div>
            <h3 class="section-title">Riwayat Kehadiran</h3>

            <div class="table-container-responsive">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th style="width: 120px;">Hari</th>
                            <th style="width: 140px;">Tanggal</th>
                            <th style="width: 130px;">Waktu Masuk</th>
                            <th style="width: 130px;">Waktu Pulang</th>
                            <th style="width: 160px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $row)
                            @php
                                $hariIndo   = \Carbon\Carbon::parse($row->tanggal)->locale('id')->translatedFormat('l');
                                $statusRaw  = strtolower($row->status);
                                $statusClass = ($statusRaw === 'tepat waktu') ? 'tepat' : (($statusRaw === 'terlambat') ? 'terlambat' : 'tidak-masuk');
                                $statusLabel = ($statusRaw === 'tepat waktu') ? 'Tepat Waktu' : (($statusRaw === 'terlambat') ? 'Terlambat' : 'Tidak Masuk');
                            @endphp
                            <tr>
                                <td style="font-weight: 600;">{{ $index + 1 }}</td>
                                <td>{{ $hariIndo }}</td>
                                <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}</td>
                                <td>{{ $row->waktu_masuk ?? '-' }}</td>
                                <td>{{ $row->waktu_pulang ?? '-' }}</td>
                                <td class="status-cell {{ $statusClass }}">{{ $statusLabel }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 30px; color: #71717A;">
                                    Belum ada data riwayat absensi yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ==================== MODAL CETAK QR CODE ==================== -->
    <div class="modal-overlay" id="qrModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(2px);">
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #6FBE8E; padding: 28px; width: 420px; max-width: 90vw; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px;">
            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <h4 style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; margin: 0;">Kartu QR Code Presensi Murid</h4>
                <button type="button" onclick="closeQRModal()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: #666;">&times;</button>
            </div>

            @php
                $qr = \App\Services\QRCodeService::getOrGenerateUserQR($user);
            @endphp

            <div id="printAreaPresensiQR" style="background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 10px; padding: 18px;">
                <img src="{{ $qr['url'] }}" alt="QR Code {{ $user->nama }}" style="width: 220px; height: 220px; object-fit: contain; display: block; margin: 0 auto;">
                <div style="margin-top: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 700; color: #111;">{{ $user->nama }}</div>
                <div style="font-family: 'Poppins', sans-serif; font-size: 12px; color: #71717A;">Murid - SDIT At-Taubah</div>
                <div style="font-family: 'Poppins', sans-serif; font-size: 11px; color: #0E7651; font-weight: 600; margin-top: 4px;">NISN: {{ $qr['identifier'] ?: '-' }}</div>
            </div>

            <div style="display: flex; gap: 10px; width: 100%;">
                <a href="{{ $qr['url'] }}" download="{{ $qr['filename'] }}" class="btn-action-qr" style="flex: 1; justify-content: center; text-decoration: none;">
                    Unduh Gambar
                </a>
                <button type="button" class="btn-action-print" style="flex: 1; justify-content: center;" onclick="window.print()">
                    Cetak
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openQRModal() {
        document.getElementById('qrModal').style.display = 'flex';
    }

    function closeQRModal() {
        document.getElementById('qrModal').style.display = 'none';
    }

    // Interactive Donut Chart Hover Tooltip (Google Looker Style)
    document.addEventListener('DOMContentLoaded', function () {
        const donutBox = document.getElementById('donutChartBox');
        const tooltip  = document.getElementById('chartTooltip');
        const dot      = document.getElementById('tooltipDot');
        const title    = document.getElementById('tooltipTitle');
        const value    = document.getElementById('tooltipValue');
        const percent  = document.getElementById('tooltipPercent');

        if (!donutBox || !tooltip) return;

        const slices = donutBox.querySelectorAll('.donut-slice');

        slices.forEach(slice => {
            slice.addEventListener('mouseenter', function (e) {
                const sTitle   = this.getAttribute('data-title');
                const sValue   = this.getAttribute('data-value');
                const sPercent = this.getAttribute('data-percent');
                const sColor   = this.getAttribute('data-color');

                title.textContent   = sTitle;
                value.textContent   = sValue;
                percent.textContent = '(' + sPercent + ')';
                dot.style.backgroundColor = sColor;

                tooltip.classList.add('active');
                positionTooltip(e);
            });

            slice.addEventListener('mousemove', function (e) {
                positionTooltip(e);
            });

            slice.addEventListener('mouseleave', function () {
                tooltip.classList.remove('active');
            });
        });

        function positionTooltip(e) {
            const boxRect = donutBox.getBoundingClientRect();
            let x = e.clientX - boxRect.left + 14;
            let y = e.clientY - boxRect.top - 20;

            const tooltipWidth = tooltip.offsetWidth || 130;
            if (x + tooltipWidth > boxRect.width + 60) {
                x = e.clientX - boxRect.left - tooltipWidth - 10;
            }
            if (y < 0) {
                y = 10;
            }

            tooltip.style.left = x + 'px';
            tooltip.style.top  = y + 'px';
        }
    });
</script>
@endsection
