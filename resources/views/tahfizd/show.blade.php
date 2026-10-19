@extends('layouts.app')

@section('title', 'Progres Kelas - ' . $kelas->nama_kelas)

@section('breadcrumb')
<div style="display:flex;align-items:center;gap:8px;font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;color:#71717A;">
    <a href="{{ route('tahfizd.index') }}" style="color:#71717A;text-decoration:none;">Tahfidz</a>
    <span style="color:#A1A1AA;font-size:11px;">&gt;</span>
    <span>Detail Kelas</span>
    <span style="color:#A1A1AA;font-size:11px;">&gt;</span>
    <span>{{ $kelas->nama_kelas }}</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/tahfizd.css">
@endpush

@section('content')
@php
    $isKelasYearActive = $kelas->tahunAkademik ? (bool)$kelas->tahunAkademik->is_aktif : ($isCurrentYearActive ?? true);
@endphp

@if(session('success'))
{{-- ===================================================
     MODAL ALERT BERHASIL (Figma Node 599:8275 & 599:8260)
     =================================================== --}}
<div class="tz-del-overlay open" id="modal-alert-berhasil" onclick="closeAlertBerhasil(event)" style="z-index: 99999;">
    <div class="tz-del-modal" style="border-color: #6FBE8E; max-width: 440px;" onclick="event.stopPropagation()">
        <button type="button" class="tz-del-close" onclick="closeAlertBerhasil()" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-del-logo">

        <div style="text-align: center; width: 100%;">
            <h2 class="tz-del-title">Berhasil</h2>
            <p class="tz-del-subtitle" style="font-size: 14px; line-height: 1.5; color: #91919C; margin-top: 8px;">
                @if(session('action') === 'delete' || session('success') === 'Data Berhasil Di Hapus' || str_contains(strtolower(session('success')), 'hapus'))
                    Data Berhasil Di Hapus
                @else
                    Data Berhasil Di Tambahkan Atau Di Perbarui
                @endif
            </p>
        </div>

        <div style="width: 100%; margin-top: 15px;">
            <button type="button" class="tz-del-btn-setujui" onclick="closeAlertBerhasil()" style="width: 100%; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 44px; font-weight: 500; font-size: 16px;">Terima</button>
        </div>
    </div>
</div>
<script>
function closeAlertBerhasil(e) {
    const m = document.getElementById('modal-alert-berhasil');
    if (m) m.style.display = 'none';
}
</script>
@endif

@if(session('error'))
{{-- ===================================================
     MODAL ALERT GAGAL (Sesuai Figma Node Alert Gagal)
     =================================================== --}}
<div class="tz-del-overlay open" id="modal-alert-gagal" onclick="closeAlertGagal(event)" style="z-index: 99999;">
    <div class="tz-del-modal" style="border-color: #E53E3E; max-width: 440px;" onclick="event.stopPropagation()">
        <button type="button" class="tz-del-close" onclick="closeAlertGagal()" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-del-logo">

        <div style="text-align: center; width: 100%;">
            <h2 class="tz-del-title" style="color: #E53E3E;">Gagal</h2>
            <p class="tz-del-subtitle" style="font-size: 14px; line-height: 1.5; color: #4B5563; margin-top: 8px;">{{ session('error') }}</p>
        </div>

        <div style="width: 100%; margin-top: 15px;">
            <button type="button" class="tz-del-btn-batal" onclick="closeAlertGagal()" style="width: 100%; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 44px; font-weight: 500; font-size: 16px;">Kembali</button>
        </div>
    </div>
</div>
<script>
function closeAlertGagal(e) {
    const m = document.getElementById('modal-alert-gagal');
    if (m) m.style.display = 'none';
}
</script>
@endif

{{-- ===== CARD PROGRES KELAS (Figma 607:7363) ===== --}}
<div class="tz-pk-wrap">

    {{-- ---- Header Bar (Figma 607:7364) ---- --}}
    <div class="tz-pk-header">
        <div class="tz-pk-header-left">
            {{-- mdi:account-school icon --}}
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                <path d="M10.6667 5.33333C10.6667 6.80667 9.47333 8 8 8C6.52667 8 5.33333 6.80667 5.33333 5.33333L5.40667 4.70667L3.33333 3.66667L8 1.33333L12.6667 3.66667V7H12V4L10.5933 4.70667L10.6667 5.33333ZM8 9.33333C10.9467 9.33333 13.3333 10.5267 13.3333 12V13.3333H2.66667V12C2.66667 10.5267 5.05333 9.33333 8 9.33333Z" fill="#0E774F"/>
            </svg>
            <span class="tz-pk-title">Progres Kelas</span>
        </div>
        <div class="tz-pk-header-right">
            {{-- Cetak Hafalan (Figma 607:7371) --}}
            <a href="{{ route('tahfizd.cetak.kelas', $kelas->id_kelas) }}" class="tz-pk-btn-cetak" target="_blank" title="Cetak Rekap Kelas">
                <span>Cetak Hafalan</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 3H6V7H18V3ZM19 12C18.4477 12 18 11.5523 18 11C18 10.4477 18.4477 10 19 10C19.5523 10 20 10.4477 20 11C20 11.5523 19.5523 12 19 12ZM16 19H8V14H16V19ZM19 8H5C3.34 8 2 9.34 2 11V17H6V21H18V17H22V11C22 9.34 20.66 8 19 8Z" fill="white"/></svg>
            </a>
            {{-- + Setoran (Figma 607:7375) --}}
            @if($isKelasYearActive)
                <button type="button" class="tz-pk-btn-setoran" onclick="openSetoranModal()">+ Setoran</button>
            @else
                <button type="button" class="tz-pk-btn-setoran" disabled style="opacity: 0.5; cursor: not-allowed; background: #94a3b8;" title="Tahun akademik belum aktif">+ Setoran</button>
            @endif
        </div>
    </div>

    {{-- ---- Info Kelas + Stats (Figma 607:7377) ---- --}}
    <div class="tz-pk-info-row">
        {{-- Metadata (Figma 607:7379) --}}
        <div class="tz-pk-meta">
            <div class="tz-pk-meta-item">
                <span class="tz-pk-meta-label">Mata Pelajaran</span>
                <span class="tz-pk-meta-val">:Tahfidz</span>
            </div>
            <div class="tz-pk-meta-item">
                <span class="tz-pk-meta-label">Kelas</span>
                <span class="tz-pk-meta-val">:{{ $kelas->nama_kelas }}</span>
            </div>
            <div class="tz-pk-meta-item">
                <span class="tz-pk-meta-label">Guru Pengampu</span>
                <span class="tz-pk-meta-val">:{{ $kelas->guru->user->nama ?? '-' }}</span>
            </div>
            <div class="tz-pk-meta-item">
                <span class="tz-pk-meta-label">Jadwal Waktu</span>
                <span class="tz-pk-meta-val">:{{ $kelas->hari }}, {{ date('H.i', strtotime($kelas->jam_mulai)) }} - {{ date('H.i', strtotime($kelas->jam_selesai)) }}</span>
            </div>
        </div>

        {{-- V-Divider --}}
        <div class="tz-pk-vdivider"></div>

        {{-- Stat: Total Setoran (Figma 607:7393) — bi:patch-check-fill --}}
        <div class="tz-pk-stat-card">
            <div class="tz-pk-stat-icon">
                <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip_s1)">
                    <path d="M21.3923 1.84877C20.8203 1.26341 20.1371 0.798295 19.3828 0.48074C18.6285 0.163185 17.8183 -0.000399064 16.9999 -0.000399064C16.1815 -0.000399064 15.3714 0.163185 14.6171 0.48074C13.8628 0.798295 13.1795 1.26341 12.6076 1.84877L11.2858 3.20452L9.39456 3.18114C8.5759 3.17145 7.76357 3.32555 7.00534 3.63438C6.24711 3.9432 5.55828 4.40052 4.97936 4.97944C4.40044 5.55836 3.94312 6.24719 3.6343 7.00542C3.32547 7.76366 3.17137 8.57599 3.18106 9.39464L3.20231 11.2859L1.85081 12.6076C1.26546 13.1796 0.800338 13.8628 0.482783 14.6171C0.165228 15.3714 0.00164421 16.1816 0.00164421 17C0.00164421 17.8184 0.165228 18.6286 0.482783 19.3829C0.800338 20.1372 1.26546 20.8204 1.85081 21.3924L3.20443 22.7141L3.18106 24.6054C3.17137 25.424 3.32547 26.2364 3.6343 26.9946C3.94312 27.7528 4.40044 28.4417 4.97936 29.0206C5.55828 29.5995 6.24711 30.0568 7.00534 30.3657C7.76357 30.6745 8.5759 30.8286 9.39456 30.8189L11.2858 30.7976L12.6076 32.1491C13.1795 32.7345 13.8628 33.1996 14.6171 33.5172C15.3714 33.8347 16.1815 33.9983 16.9999 33.9983C17.8183 33.9983 18.6285 33.8347 19.3828 33.5172C20.1371 33.1996 20.8203 32.7345 21.3923 32.1491L22.7141 30.7955L24.6053 30.8189C25.424 30.8286 26.2363 30.6745 26.9945 30.3657C27.7528 30.0568 28.4416 29.5995 29.0205 29.0206C29.5994 28.4417 30.0567 27.7528 30.3656 26.9946C30.6744 26.2364 30.8285 25.424 30.8188 24.6054L30.7976 22.7141L32.1491 21.3924C32.7344 20.8204 33.1995 20.1372 33.5171 19.3829C33.8346 18.6286 33.9982 17.8184 33.9982 17C33.9982 16.1816 33.8346 15.3714 33.5171 14.6171C33.1995 13.8628 32.7344 13.1796 32.1491 12.6076L30.7954 11.2859L30.8188 9.39464C30.8285 8.57599 30.6744 7.76366 30.3656 7.00542C30.0567 6.24719 29.5994 5.55836 29.0205 4.97944C28.4416 4.40052 27.7528 3.9432 26.9945 3.63438C26.2363 3.32555 25.424 3.17145 24.6053 3.18114L22.7141 3.20239L21.3923 1.84877ZM22.0022 14.5648L15.6272 20.9398C15.4278 21.1393 15.1572 21.2514 14.8749 21.2514C14.5927 21.2514 14.3221 21.1393 14.1227 20.9398L10.9352 17.7523C10.7357 17.5528 10.6236 17.2822 10.6236 17C10.6236 16.7178 10.7357 16.4472 10.9352 16.2478C11.1346 16.0483 11.4052 15.9362 11.6874 15.9362C11.9696 15.9362 12.2402 16.0483 12.4397 16.2478L14.8749 18.6851L20.4977 13.0603C20.6972 12.8608 20.9678 12.7487 21.2499 12.7487C21.5321 12.7487 21.8027 12.8608 22.0022 13.0603C22.2017 13.2598 22.3138 13.5304 22.3138 13.8125C22.3138 14.0947 22.2017 14.3653 22.0022 14.5648Z" fill="#11BB82"/>
                    </g>
                    <defs><clipPath id="clip_s1"><rect width="34" height="34" fill="white"/></clipPath></defs>
                </svg>
            </div>
            <div class="tz-pk-stat-info">
                <span class="tz-pk-stat-label">Total Setoran</span>
                <span class="tz-pk-stat-value">{{ $totalSetoranCount }} kali</span>
            </div>
        </div>

        {{-- Stat: Target (Figma 607:7400) — fluent:target-arrow-20-filled --}}
        <div class="tz-pk-stat-card">
            <div class="tz-pk-stat-icon">
                <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M29.9251 12.7823C30.871 15.6883 30.8105 18.8282 29.7532 21.6956C28.6959 24.5629 26.7036 26.9906 24.0976 28.5869C21.4916 30.1833 18.4238 30.8552 15.3891 30.4944C12.3544 30.1336 9.52974 28.7611 7.37067 26.5982C5.2116 24.4353 3.84404 21.6082 3.48857 18.5729C3.1331 15.5376 3.81045 12.471 5.41136 9.8678C7.01227 7.2646 9.44341 5.27655 12.3126 4.22434C15.1818 3.17212 18.3219 3.11708 21.2262 4.0681L19.4463 5.848C19.3488 5.94547 19.2593 6.0503 19.1777 6.1625C16.8523 5.69313 14.4375 5.98463 12.2905 6.99388C10.1436 8.00313 8.37842 9.67655 7.25613 11.7666C6.13385 13.8567 5.71402 16.2525 6.05874 18.5997C6.40345 20.9469 7.49441 23.1208 9.17026 24.7999C10.8461 26.4791 13.0179 27.5743 15.3644 27.9236C17.7108 28.2729 20.1075 27.8578 22.1998 26.7396C24.2921 25.6215 25.9689 23.8596 26.9824 21.7146C27.9959 19.5697 28.2921 17.1555 27.8273 14.8291C27.9436 14.7466 28.0528 14.6545 28.1537 14.5537L29.9251 12.7823ZM25.3266 15.3C25.4399 15.8508 25.4966 16.4175 25.4966 17C25.4961 18.7541 24.9529 20.4652 23.9415 21.8984C22.9302 23.3316 21.5001 24.4168 19.8476 25.0052C18.1951 25.5935 16.401 25.6562 14.7114 25.1846C13.0219 24.7131 11.5196 23.7303 10.4107 22.3712C9.30172 21.012 8.64043 19.3431 8.5175 17.5933C8.39457 15.8434 8.81601 14.0984 9.72403 12.5976C10.6321 11.0968 11.9822 9.91361 13.5892 9.21043C15.1962 8.50724 16.9814 8.31847 18.7 8.67V11.2982C17.4312 10.9194 16.0723 10.9747 14.8385 11.4553C13.6047 11.9359 12.5664 12.8143 11.8881 13.9514C11.2098 15.0886 10.9301 16.4195 11.0934 17.7335C11.2568 19.0475 11.8537 20.2695 12.7898 21.206C13.7259 22.1425 14.9476 22.7399 16.2616 22.9038C17.5755 23.0677 18.9065 22.7886 20.044 22.1108C21.1814 21.4329 22.0603 20.395 22.5414 19.1614C23.0225 17.9278 23.0783 16.5689 22.7001 15.3H25.3266ZM16.9966 19.55C17.3877 19.5502 17.7735 19.4605 18.1244 19.2877C18.4752 19.115 18.7816 18.8638 19.0199 18.5537C19.2582 18.2436 19.422 17.8829 19.4985 17.4994C19.5751 17.1159 19.5625 16.7199 19.4616 16.3421L22.151 13.651L22.202 13.6H26.35C26.4617 13.6002 26.5723 13.5784 26.6756 13.5358C26.7789 13.4932 26.8727 13.4307 26.9518 13.3518L30.3518 9.9518C30.471 9.83292 30.5522 9.68133 30.5851 9.51624C30.618 9.35116 30.6012 9.18002 30.5367 9.02451C30.4723 8.86901 30.3631 8.73614 30.223 8.64277C30.083 8.54939 29.9183 8.4997 29.75 8.5H25.5V4.25C25.5 4.08201 25.4502 3.91781 25.3569 3.77811C25.2636 3.63841 25.131 3.52949 24.9758 3.4651C24.8207 3.40071 24.6499 3.38374 24.4851 3.41633C24.3203 3.44892 24.1689 3.5296 24.0499 3.6482L20.6499 7.0482C20.5707 7.12714 20.5079 7.22093 20.465 7.3242C20.4221 7.42747 20.4 7.53818 20.4 7.65V11.798L20.349 11.849L17.6596 14.5367C17.2814 14.4349 16.8849 14.4215 16.5008 14.4977C16.1166 14.5738 15.7551 14.7374 15.4444 14.9758C15.1337 15.2142 14.8821 15.521 14.709 15.8723C14.536 16.2236 14.4462 16.6101 14.4466 17.0017C14.4466 17.678 14.7153 18.3266 15.1935 18.8048C15.6717 19.283 16.3203 19.5517 16.9966 19.5517" fill="#DE4D6E"/>
                </svg>
            </div>
            <div class="tz-pk-stat-info">
                <span class="tz-pk-stat-label">Target</span>
                <span class="tz-pk-stat-value">{{ $targetSuratCount }} Surat</span>
            </div>
        </div>

        {{-- V-Divider --}}
        <div class="tz-pk-vdivider"></div>

        {{-- Circular Progress (Figma 607:7408) --}}
        <div class="tz-pk-circle-wrap">
            <div class="tz-pk-circle-chart">
                <svg viewBox="0 0 100 100" class="tz-pk-circle-svg">
                    <circle cx="50" cy="50" r="40" stroke="#E5E7EB" stroke-width="10" fill="none"/>
                    <circle cx="50" cy="50" r="40" stroke="#0E7651" stroke-width="10" fill="none"
                        stroke-dasharray="{{ round($overallPercent * 2.513) }} 251.3"
                        stroke-dashoffset="0"
                        stroke-linecap="round"
                        transform="rotate(-90 50 50)"/>
                </svg>
                <div class="tz-pk-circle-text">{{ $overallPercent }}%</div>
            </div>
            <p class="tz-pk-circle-sub">
                <span style="color:#fdba74;font-weight:700;">{{ $muridTuntasClass }}</span>
                <span> Dari </span>
                <span style="color:#0e7651;font-weight:700;">{{ $totalMurid }}</span>
                <span> Murid Tuntas</span>
            </p>
        </div>
    </div>

    {{-- ---- Daftar Surat Target Grid (Figma 607:7415) ---- --}}
    @if($suratCards)
    <div class="tz-pk-section">
        <p class="tz-pk-section-title">Daftar Surat Target</p>
        <div class="tz-pk-surat-grid">
            @foreach($suratCards as $card)
            @php
                $surat = $card['surat'];
                $status = $card['status'];
                $persen = $card['persen'];
                $lulusCount = $card['lulus_count'];

                // Status badge classes
                $statusClass = 'tz-pk-badge--belum';
                if ($status === 'tuntas') $statusClass = 'tz-pk-badge--tuntas';
                elseif ($status === 'Progress') $statusClass = 'tz-pk-badge--progress';

                // Lulus count color
                $lulusColor = '#6c757d'; // belum mulai -> gray
                if ($status === 'tuntas') $lulusColor = '#0e7651'; // green
                elseif ($status === 'Progress') $lulusColor = '#fdba74'; // orange
            @endphp
            <div class="tz-pk-surat-card">
                {{-- Islamic 8-Point Star Badge --}}
                <div class="tz-pk-star-badge">
                    <svg viewBox="0 0 50 52" fill="none" xmlns="http://www.w3.org/2000/svg" class="tz-pk-star-svg">
                        <path d="M19.5494 4.18663C20.9117 2.65366 21.5928 1.88717 22.3206 1.46016C23.9342 0.513281 25.8991 0.513281 27.5128 1.46016C28.2405 1.88717 28.9216 2.65366 30.2839 4.18663C30.8977 4.87729 31.2045 5.22261 31.5459 5.49988C32.2951 6.10849 33.185 6.49616 34.124 6.62302C34.5518 6.6808 35.001 6.66484 35.8993 6.63291C37.8933 6.56204 38.8902 6.5266 39.6919 6.76584C41.4695 7.29635 42.8589 8.75762 43.3633 10.6272C43.5908 11.4703 43.5571 12.5189 43.4897 14.616C43.4594 15.5608 43.4442 16.0332 43.4992 16.4831C43.6198 17.4706 43.9884 18.4066 44.5671 19.1946C44.8307 19.5536 45.159 19.8763 45.8157 20.5218C47.2733 21.9545 48.0021 22.6709 48.4081 23.4363C49.3084 25.1334 49.3084 27.1999 48.4081 28.8971C48.0021 29.6624 47.2733 30.3788 45.8157 31.8115C45.159 32.457 44.8307 32.7798 44.5671 33.1388C43.9884 33.9268 43.6198 34.8627 43.4992 35.8503C43.4442 36.3002 43.4594 36.7726 43.4897 37.7174C43.5571 39.8145 43.5908 40.863 43.3633 41.7061C42.8589 43.5757 41.4695 45.037 39.6919 45.5675C38.8902 45.8067 37.8933 45.7713 35.8993 45.7004C35.001 45.6685 34.5518 45.6525 34.124 45.7103C33.185 45.8372 32.2951 46.2248 31.5459 46.8335C31.2045 47.1107 30.8977 47.456 30.2839 48.1467C28.9216 49.6797 28.2405 50.4462 27.5128 50.8732C25.8991 51.8201 23.9342 51.8201 22.3206 50.8732C21.5928 50.4462 20.9117 49.6797 19.5494 48.1467C18.9357 47.456 18.6288 47.1107 18.2875 46.8335C17.5382 46.2248 16.6483 45.8372 15.7093 45.7103C15.2815 45.6525 14.8324 45.6685 13.934 45.7004C11.9401 45.7713 10.9431 45.8067 10.1414 45.5675C8.3638 45.037 6.9744 43.5757 6.46998 41.7061C6.24251 40.863 6.2762 39.8145 6.34359 37.7174C6.37395 36.7726 6.38913 36.3002 6.33418 35.8503C6.21357 34.8627 5.84496 33.9268 5.26628 33.1388C5.00265 32.7798 4.6743 32.457 4.01762 31.8115C2.56003 30.3788 1.83124 29.6624 1.42523 28.8971C0.524922 27.1999 0.524922 25.1334 1.42523 23.4363C1.83124 22.6709 2.56003 21.9545 4.01762 20.5218C4.6743 19.8763 5.00265 19.5536 5.26628 19.1946C5.84496 18.4066 6.21357 17.4706 6.33418 16.4831C6.38913 16.0332 6.37395 15.5608 6.34359 14.616C6.2762 12.5189 6.24251 11.4703 6.46998 10.6272C6.9744 8.75762 8.3638 7.29635 10.1414 6.76584C10.9431 6.5266 11.9401 6.56204 13.934 6.63291Z" fill="#0E7651" stroke="#0E7651" stroke-width="1.5"/>
                    </svg>
                    <span class="tz-pk-star-num">{{ $surat->id_surat ?? '' }}</span>
                </div>

                {{-- Surat Name & Ayat --}}
                <div class="tz-pk-surat-info">
                    <p class="tz-pk-surat-name">{{ $surat->nama_surat ?? '-' }}</p>
                    <p class="tz-pk-surat-ayat">{{ $surat->jumlah_ayat ?? 0 }} Ayat</p>
                </div>

                {{-- Status Pill (top right) --}}
                <div class="tz-pk-badge {{ $statusClass }}">{{ $status }}</div>

                {{-- Lulus text --}}
                <p class="tz-pk-lulus-text">
                    @php
                        $lulusClass = 'tz-lulus-belum';
                        if ($status === 'tuntas') $lulusClass = 'tz-lulus-lancar';
                        elseif ($status === 'Progress') $lulusClass = 'tz-lulus-progress';
                    @endphp
                    <span class="{{ $lulusClass }}">{{ $lulusCount }}</span>
                    <span> dari </span>
                    <span class="tz-lulus-lancar">{{ $totalMurid }}</span>
                    <span> murid lulus</span>
                </p>

                {{-- Progress Bar --}}
                <div class="tz-pk-progbar-row">
                    <div class="tz-pk-progbar-track">
                        <div class="tz-pk-progbar-fill" data-progress="{{ $persen }}"></div>
                    </div>
                    <span class="tz-pk-progbar-label">{{ $persen }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="tz-pk-section">
        <p class="tz-pk-section-title">Daftar Surat Target</p>
        <div class="tz-pk-empty">
            <p>Belum ada target hafalan untuk kelas ini.</p>
            <a href="{{ route('tahfizd.target.index') }}" style="color:#0E7651;">→ Tambah Target Hafalan</a>
        </div>
    </div>
    @endif

    {{-- ---- Riwayat Setoran Murid Table (Figma 607:7486) ---- --}}
    <div class="tz-pk-section">
        <p class="tz-pk-section-title">Riwayat Setoran Murid</p>
        <div class="tz-pk-table-wrap">
            <table class="tz-pk-table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th style="width:190px;">Nama Lengkap</th>
                        <th style="width:160px;">Surat</th>
                        <th style="width:140px;">Ayat</th>
                        <th style="width:120px;">Tanggal Setoran</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatSetoran as $i => $setoran)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="text-align:left;padding-left:16px;">{{ $setoran->murid->user->nama ?? '-' }}</td>
                        <td>{{ $setoran->surat->nama_surat ?? '-' }}</td>
                        <td>{{ $setoran->ayat_mulai }} – {{ $setoran->ayat_selesai }}</td>
                        <td>{{ $setoran->tanggal ? $setoran->tanggal->format('d/m/Y') : '-' }}</td>
                        <td>
                            @php
                                $s = $setoran->status;
                                $scClass = 'tz-status-lancar';
                                if ($s === 'Perlu Perbaikan' || $s === 'Kurang Lancar') $scClass = 'tz-status-perbaikan';
                                elseif ($s === 'Mengulang') $scClass = 'tz-status-mengulang';
                                elseif ($s === 'Menunggu') $scClass = 'tz-status-menunggu';
                            @endphp
                            <span class="{{ $scClass }}">{{ $s }}</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;align-items:center;">
                                @if($isKelasYearActive)
                                    {{-- Edit --}}
                                    <button type="button" class="tz-pk-act-btn btn-edit-setoran" title="Edit Setoran"
                                        data-id="{{ $setoran->id_setoran }}"
                                        data-surat="{{ $setoran->id_surat }}"
                                        data-mulai="{{ $setoran->ayat_mulai }}"
                                        data-selesai="{{ $setoran->ayat_selesai }}"
                                        data-tanggal="{{ $setoran->tanggal ? $setoran->tanggal->format('Y-m-d') : '' }}"
                                        data-status="{{ $setoran->status }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 17.25V21H6.75L17.81 9.94L14.06 6.19L3 17.25ZM20.71 7.04C21.1 6.65 21.1 6.02 20.71 5.63L18.37 3.29C17.98 2.9 17.35 2.9 16.96 3.29L15.13 5.12L18.88 8.87L20.71 7.04Z" fill="#0E7651"/></svg>
                                    </button>
                                    {{-- Hapus --}}
                                    <button type="button" class="tz-pk-act-btn btn-hapus-setoran" title="Hapus Setoran"
                                        data-id="{{ $setoran->id_setoran }}"
                                        data-msg="{{ ($setoran->murid->user->nama ?? 'Murid') }} - Surat {{ ($setoran->surat->nama_surat ?? '') }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M16 9V19H8V9H16ZM14.5 3H9.5L8.5 4H5V6H19V4H15.5L14.5 3ZM18 7H6V19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7Z" fill="#E11D48"/></svg>
                                    </button>
                                @else
                                    <button type="button" class="tz-pk-act-btn" disabled style="opacity: 0.35; cursor: not-allowed;" title="Tahun akademik belum aktif">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 17.25V21H6.75L17.81 9.94L14.06 6.19L3 17.25ZM20.71 7.04C21.1 6.65 21.1 6.02 20.71 5.63L18.37 3.29C17.98 2.9 17.35 2.9 16.96 3.29L15.13 5.12L18.88 8.87L20.71 7.04Z" fill="#94a3b8"/></svg>
                                    </button>
                                    <button type="button" class="tz-pk-act-btn" disabled style="opacity: 0.35; cursor: not-allowed;" title="Tahun akademik belum aktif">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M16 9V19H8V9H16ZM14.5 3H9.5L8.5 4H5V6H19V4H15.5L14.5 3ZM18 7H6V19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7Z" fill="#94a3b8"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#9CA3AF;padding:24px;">Belum ada riwayat setoran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ---- Daftar Murid Table (Figma 607:7559) ---- --}}
    <div class="tz-pk-section">
        <p class="tz-pk-section-title">Daftar Murid</p>
        <div class="tz-pk-table-wrap">
            <table class="tz-pk-table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th style="width:300px;">Nama Lengkap</th>
                        <th>NISN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($muridList as $i => $murid)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="text-align:center;">{{ $murid->user->nama ?? '-' }}</td>
                        <td>{{ $murid->nisn ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;color:#9CA3AF;padding:24px;">Belum ada murid di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end tz-pk-wrap --}}


{{-- ===================================================
     MODAL: TAMBAH SETORAN (Exact Figma Node 607:7613 / 607:7933)
     =================================================== --}}
<div class="tz-overlay" id="modal-tambah-setoran" onclick="closeSetoranModal(event)">
    <div class="tz-form-modal" onclick="event.stopPropagation()">
        {{-- Logo Sekolah --}}
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-fm-logo">
        <h2 class="tz-fm-title">Form Tambah Setoran</h2>
        <p class="tz-fm-subtitle">Isi Dengan Lengkap, dan pastikan tidak ada yang salah</p>

        <form method="POST" action="{{ route('tahfizd.setoran.store') }}" onsubmit="return validateAyatRange('tambah')">
            @csrf
            <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">

            <div class="tz-fm-cols">
                {{-- Kolom Kiri --}}
                <div class="tz-fm-col">
                    {{-- Murid --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Murid <span>*</span></label>
                        <select name="id_murid" id="tambah-id_murid" class="tz-fm-select" required onchange="handleMuridChange('tambah')">
                            <option value="">Pilih Murid</option>
                            @foreach($muridList as $m)
                            <option value="{{ $m->id_murid }}">{{ $m->user->nama ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Surat (Hanya surat yang ada di target hafalan kelas ini) --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Surat <span>*</span></label>
                        <select name="id_surat" id="tambah-id_surat" class="tz-fm-select" required onchange="handleSuratChange('tambah')">
                            <option value="" data-max="">Pilih Surat</option>
                            @forelse($targetList as $t)
                                @if($t->surat)
                                <option value="{{ $t->id_surat }}" data-max="{{ $t->surat->jumlah_ayat }}" data-nama="{{ $t->surat->nama_surat }}">{{ $t->id_surat }}. {{ $t->surat->nama_surat }} ({{ $t->surat->jumlah_ayat }} Ayat)</option>
                                @endif
                            @empty
                                @foreach($allSurat as $s)
                                <option value="{{ $s->id_surat }}" data-max="{{ $s->jumlah_ayat }}" data-nama="{{ $s->nama_surat }}">{{ $s->id_surat }}. {{ $s->nama_surat }}</option>
                                @endforeach
                            @endforelse
                        </select>
                    </div>

                    {{-- Ayat Mulai --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Ayat Mulai <span>*</span></label>
                        <input type="number" name="ayat_mulai" id="tambah-ayat_mulai" class="tz-fm-input" min="1" required placeholder="Masukan Jumlah Ayat">
                    </div>

                    {{-- Ayat Selesai --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Ayat Selesai <span>*</span></label>
                        <input type="number" name="ayat_selesai" id="tambah-ayat_selesai" class="tz-fm-input" min="1" required placeholder="Masukan Jumlah Ayat">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="tz-fm-col">
                    {{-- Tanggal --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Tanggal <span>*</span></label>
                        <input type="date" name="tanggal" class="tz-fm-input" required value="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Hasil Setoran (Radio: Lancar, Mengulang, Kurang Lancar) --}}
                    <div class="tz-fm-field" style="align-items: flex-start; flex-direction: column; gap: 8px;">
                        <label class="tz-fm-label">Hasil Setoran <span>*</span></label>
                        <div class="tz-radio-group">
                            <label class="tz-radio-label">
                                <input type="radio" name="status" value="Lancar" checked required>
                                <span>Lancar</span>
                            </label>
                            <label class="tz-radio-label">
                                <input type="radio" name="status" value="Mengulang" required>
                                <span>Mengulang</span>
                            </label>
                            <label class="tz-radio-label">
                                <input type="radio" name="status" value="Kurang Lancar" required>
                                <span>Kurang Lancar</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Batal & Simpan --}}
            <div class="tz-fm-actions">
                <button type="button" class="tz-fm-btn-batal" onclick="closeSetoranModal()">Batal</button>
                <button type="submit" class="tz-fm-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL: EDIT SETORAN
     =================================================== --}}
<div class="tz-overlay" id="modal-edit-setoran" onclick="closeEditSetoran(event)">
    <div class="tz-form-modal" onclick="event.stopPropagation()">
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-fm-logo">
        <h2 class="tz-fm-title">Form Perbarui Setoran</h2>
        <p class="tz-fm-subtitle">Isi Dengan Lengkap, dan pastikan tidak ada yang salah</p>

        <form method="POST" id="form-edit-setoran" action="" onsubmit="return validateAyatRange('edit')">
            @csrf
            <div class="tz-fm-cols">
                {{-- Kolom Kiri --}}
                <div class="tz-fm-col">
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Surat <span>*</span></label>
                        <select name="id_surat" id="edit-id_surat" class="tz-fm-select" required onchange="handleSuratChange('edit')">
                            <option value="" data-max="">Pilih Surat</option>
                            @forelse($targetList as $t)
                                @if($t->surat)
                                <option value="{{ $t->id_surat }}" data-max="{{ $t->surat->jumlah_ayat }}" data-nama="{{ $t->surat->nama_surat }}">{{ $t->id_surat }}. {{ $t->surat->nama_surat }} ({{ $t->surat->jumlah_ayat }} Ayat)</option>
                                @endif
                            @empty
                                @foreach($allSurat as $s)
                                <option value="{{ $s->id_surat }}" data-max="{{ $s->jumlah_ayat }}" data-nama="{{ $s->nama_surat }}">{{ $s->id_surat }}. {{ $s->nama_surat }}</option>
                                @endforeach
                            @endforelse
                        </select>
                    </div>

                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Ayat Mulai <span>*</span></label>
                        <input type="number" name="ayat_mulai" id="edit-ayat_mulai" class="tz-fm-input" min="1" required placeholder="Masukan Jumlah Ayat">
                    </div>

                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Ayat Selesai <span>*</span></label>
                        <input type="number" name="ayat_selesai" id="edit-ayat_selesai" class="tz-fm-input" min="1" required placeholder="Masukan Jumlah Ayat">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="tz-fm-col">
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Tanggal <span>*</span></label>
                        <input type="date" name="tanggal" id="edit-tanggal" class="tz-fm-input" required>
                    </div>

                    <div class="tz-fm-field" style="align-items: flex-start; flex-direction: column; gap: 8px;">
                        <label class="tz-fm-label">Hasil Setoran <span>*</span></label>
                        <div class="tz-radio-group">
                            <label class="tz-radio-label">
                                <input type="radio" name="status" id="edit-status-lancar" value="Lancar" required>
                                <span>Lancar</span>
                            </label>
                            <label class="tz-radio-label">
                                <input type="radio" name="status" id="edit-status-mengulang" value="Mengulang" required>
                                <span>Mengulang</span>
                            </label>
                            <label class="tz-radio-label">
                                <input type="radio" name="status" id="edit-status-kurang" value="Kurang Lancar" required>
                                <span>Kurang Lancar</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tz-fm-actions">
                <button type="button" class="tz-fm-btn-batal" onclick="closeEditSetoran()">Batal</button>
                <button type="submit" class="tz-fm-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL: HAPUS SETORAN HAFALAN (Figma Node 599:7804 format)
     =================================================== --}}
<div class="tz-del-overlay" id="modal-hapus-setoran">
    <div class="tz-del-modal">
        <button class="tz-del-close" onclick="closeDeleteSetoran()" type="button" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-del-logo">

        <div style="text-align: center; width: 100%;">
            <h2 class="tz-del-title">Menghapus</h2>
            <p class="tz-del-subtitle" id="delete-setoran-msg">Apakah Anda Yakin Ingin Menghapusnya?</p>
        </div>

        <div class="tz-del-actions">
            <button class="tz-del-btn-batal" type="button" onclick="closeDeleteSetoran()">Batal</button>
            <form id="form-hapus-setoran" method="POST" action="" class="tz-del-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="tz-del-btn-setujui">Setujui</button>
            </form>
        </div>
    </div>
</div>

<script type="application/json" id="murid-completed-surat-data">
{!! json_encode($muridCompletedSurat ?? (object)[]) !!}
</script>

<script type="application/json" id="murid-last-passed-ayat-data">
{!! json_encode($muridLastPassedAyat ?? (object)[]) !!}
</script>

<script>
const setoranBaseUrl = '/tahfizd/setoran';
// ---- Modal Tambah Setoran ----
function openSetoranModal() {
    document.getElementById('modal-tambah-setoran').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSetoranModal(e) {
    if (!e || e.target === document.getElementById('modal-tambah-setoran')) {
        document.getElementById('modal-tambah-setoran').classList.remove('open');
        document.body.style.overflow = '';
    }
}

// ---- Modal Edit Setoran ----
function openEditSetoran(id, idSurat, ayatMulai, ayatSelesai, tanggal, status) {
    document.getElementById('form-edit-setoran').action = setoranBaseUrl + '/' + id + '/update';
    document.getElementById('edit-id_surat').value = idSurat;
    document.getElementById('edit-ayat_mulai').value = ayatMulai;
    document.getElementById('edit-ayat_selesai').value = ayatSelesai;
    document.getElementById('edit-tanggal').value = tanggal;

    // Trigger max ayat setting
    handleSuratChange('edit');

    // Set radio status
    const statusVal = status === 'Lulus' ? 'Lancar' : (status === 'Perlu Perbaikan' ? 'Kurang Lancar' : status);
    const radioLancar = document.getElementById('edit-status-lancar');
    const radioMengulang = document.getElementById('edit-status-mengulang');
    const radioKurang = document.getElementById('edit-status-kurang');

    if (statusVal === 'Lancar') radioLancar.checked = true;
    else if (statusVal === 'Mengulang') radioMengulang.checked = true;
    else if (statusVal === 'Kurang Lancar') radioKurang.checked = true;
    else radioLancar.checked = true;

    document.getElementById('modal-edit-setoran').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEditSetoran(e) {
    if (!e || e.target === document.getElementById('modal-edit-setoran')) {
        document.getElementById('modal-edit-setoran').classList.remove('open');
        document.body.style.overflow = '';
    }
}

// ---- Modal Hapus Setoran ----
function openDeleteSetoran(id, info) {
    document.getElementById('form-hapus-setoran').action = setoranBaseUrl + '/' + id;
    if (info) {
        document.getElementById('delete-setoran-msg').textContent = 'Apakah Anda yakin ingin menghapus data setoran (' + info + ')?';
    } else {
        document.getElementById('delete-setoran-msg').textContent = 'Apakah Anda Yakin Ingin Menghapusnya?';
    }
    document.getElementById('modal-hapus-setoran').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeDeleteSetoran() {
    document.getElementById('modal-hapus-setoran').style.display = 'none';
    document.body.style.overflow = '';
}

// Event Delegation for Edit & Hapus Buttons
document.addEventListener('DOMContentLoaded', function() {
    // Fill progress bars smoothly
    document.querySelectorAll('.tz-pk-progbar-fill[data-progress]').forEach(function(bar) {
        bar.style.width = bar.getAttribute('data-progress') + '%';
    });

    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.btn-edit-setoran');
        if (editBtn) {
            const d = editBtn.dataset;
            openEditSetoran(d.id, d.surat, d.mulai, d.selesai, d.tanggal, d.status);
            return;
        }

        const hapusBtn = e.target.closest('.btn-hapus-setoran');
        if (hapusBtn) {
            const d = hapusBtn.dataset;
            openDeleteSetoran(d.id, d.msg);
            return;
        }
    });
});

// Murid completed surah map
var muridCompletedSuratEl = document.getElementById('murid-completed-surat-data');
var muridCompletedSurat = muridCompletedSuratEl ? JSON.parse(muridCompletedSuratEl.textContent) : {};

var muridLastPassedAyatEl = document.getElementById('murid-last-passed-ayat-data');
var muridLastPassedAyat = muridLastPassedAyatEl ? JSON.parse(muridLastPassedAyatEl.textContent) : {};

function handleMuridChange(prefix) {
    const muridSelect = document.getElementById(prefix + '-id_murid');
    const suratSelect = document.getElementById(prefix + '-id_surat');
    if (!muridSelect || !suratSelect) return;

    const muridId = muridSelect.value;
    const completed = (muridId && muridCompletedSurat[muridId]) ? muridCompletedSurat[muridId] : {};

    for (let i = 0; i < suratSelect.options.length; i++) {
        const opt = suratSelect.options[i];
        const suratId = opt.value;
        if (!suratId) continue;

        if (!opt.getAttribute('data-base-text')) {
            opt.setAttribute('data-base-text', opt.textContent);
        }
        const baseText = opt.getAttribute('data-base-text');

        if (completed[suratId]) {
            opt.disabled = true;
            opt.textContent = baseText + ' (Sudah Lulus)';
            opt.style.color = '#9CA3AF';
        } else {
            opt.disabled = false;
            opt.textContent = baseText;
            opt.style.color = '';
        }
    }

    if (suratSelect.value && completed[suratSelect.value]) {
        suratSelect.value = '';
        handleSuratChange(prefix);
    }
}

// Dynamic max ayat & validation
function handleSuratChange(prefix) {
    const select = document.getElementById(prefix + '-id_surat');
    const selectedOption = select.options[select.selectedIndex];
    const max = selectedOption ? selectedOption.getAttribute('data-max') : null;

    const mulai = document.getElementById(prefix + '-ayat_mulai');
    const selesai = document.getElementById(prefix + '-ayat_selesai');

    if (max) {
        mulai.max = max;
        selesai.max = max;
        mulai.placeholder = '1 - ' + max;
        selesai.placeholder = '1 - ' + max;
    } else {
        mulai.removeAttribute('max');
        selesai.removeAttribute('max');
        mulai.placeholder = 'Masukan Jumlah Ayat';
        selesai.placeholder = 'Masukan Jumlah Ayat';
    }

    // Auto set ayat_mulai untuk form tambah berdasarkan riwayat lulus
    if (prefix === 'tambah' && select.value) {
        const muridSelect = document.getElementById('tambah-id_murid');
        const muridId = muridSelect ? muridSelect.value : null;
        const suratId = select.value;
        if (muridId && muridLastPassedAyat[muridId] && typeof muridLastPassedAyat[muridId][suratId] !== 'undefined') {
            const nextAyat = muridLastPassedAyat[muridId][suratId] + 1;
            mulai.value = nextAyat;
            mulai.min = nextAyat;
        } else {
            mulai.min = 1;
        }
    }
}

function validateAyatRange(prefix) {
    const select = document.getElementById(prefix + '-id_surat');
    const selectedOption = select.options[select.selectedIndex];
    const max = parseInt(selectedOption.getAttribute('data-max'), 10);
    const namaSurat = selectedOption.getAttribute('data-nama') || 'Surat terpilih';

    const muridSelect = document.getElementById(prefix + '-id_murid');
    const mulaiInput = document.getElementById(prefix + '-ayat_mulai');
    const selesaiInput = document.getElementById(prefix + '-ayat_selesai');

    const mulai = parseInt(mulaiInput.value, 10);
    const selesai = parseInt(selesaiInput.value, 10);

    if (muridSelect && muridSelect.value) {
        const muridId = muridSelect.value;
        const suratId = select.value;
        if (muridCompletedSurat[muridId] && muridCompletedSurat[muridId][suratId]) {
            alert('Gagal: Murid ini sudah dinyatakan LULUS untuk ' + namaSurat + '. Tidak dapat menambahkan setoran baru pada surat yang sudah tuntas.');
            return false;
        }

        // Validasi urutan ayat berurutan (hanya pada tambah setoran)
        if (prefix === 'tambah' && muridLastPassedAyat[muridId] && typeof muridLastPassedAyat[muridId][suratId] !== 'undefined') {
            const lastPassed = muridLastPassedAyat[muridId][suratId];
            const expectedNext = lastPassed + 1;
            if (mulai > expectedNext) {
                if (lastPassed > 0) {
                    alert('Gagal: Setoran harus berurutan. Murid terakhir lulus di ayat ' + lastPassed + ' pada ' + namaSurat + '. Setoran berikutnya harus dimulai dari ayat ' + expectedNext + ', tidak boleh melompat ke ayat ' + mulai + '.');
                } else {
                    alert('Gagal: Setoran pertama untuk ' + namaSurat + ' harus dimulai dari ayat 1. Tidak boleh langsung melompat ke ayat ' + mulai + '.');
                }
                mulaiInput.focus();
                return false;
            }
        }
    }

    if (isNaN(mulai) || mulai < 1) {
        alert('Ayat mulai harus minimal 1.');
        mulaiInput.focus();
        return false;
    }

    if (isNaN(selesai) || selesai < 1) {
        alert('Ayat selesai harus minimal 1.');
        selesaiInput.focus();
        return false;
    }

    if (selesai < mulai) {
        alert('Ayat selesai (' + selesai + ') tidak boleh lebih kecil dari ayat mulai (' + mulai + ').');
        selesaiInput.focus();
        return false;
    }

    if (!isNaN(max) && max > 0) {
        if (mulai > max) {
            alert('Gagal: ' + namaSurat + ' hanya memiliki ' + max + ' ayat. Ayat mulai (' + mulai + ') melebihi batas ayat.');
            mulaiInput.focus();
            return false;
        }
        if (selesai > max) {
            alert('Gagal: ' + namaSurat + ' hanya memiliki ' + max + ' ayat. Ayat selesai (' + selesai + ') melebihi batas ayat.');
            selesaiInput.focus();
            return false;
        }
    }

    return true;
}

// Auto-dismiss flash
setTimeout(function() {
    const flash = document.querySelectorAll('.tz-pk-flash');
    flash.forEach(f => { f.style.opacity = '0'; f.style.transition = 'opacity 0.5s'; setTimeout(() => f.remove(), 600); });
}, 5000);
</script>

@endsection
