@extends('layouts.app')

@section('title', 'Progres Murid - ' . ($murid->user->nama ?? ''))
@section('page_title', 'Data Murid')

@section('breadcrumb')
<div style="display:flex;align-items:center;gap:6px;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:700;color:#71717A;">
    <a href="{{ route('siswa.index') }}" style="color:#71717A;text-decoration:none;">Data Murid</a>
    <span style="display:inline-flex;align-items:center;margin:0 2px;">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M3.5 1.5L7 5L3.5 8.5" stroke="#71717A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </span>
    <span>{{ $murid->user->nama ?? '-' }}</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/murid.css">
@endpush

@section('content')

{{-- ===== CARD PROGRES MURID (Figma Node 412:4268 / 459:3141) ===== --}}
<div class="mu-pk-wrap">

    {{-- ---- Header Bar (Figma Node 459:3142) ---- --}}
    <div class="mu-pk-header">
        <div class="mu-pk-header-left">
            {{-- mdi:account-school icon (16x16) --}}
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                <path d="M10.6667 5.33333C10.6667 6.80667 9.47333 8 8 8C6.52667 8 5.33333 6.80667 5.33333 5.33333L5.40667 4.70667L3.33333 3.66667L8 1.33333L12.6667 3.66667V7H12V4L10.5933 4.70667L10.6667 5.33333ZM8 9.33333C10.9467 9.33333 13.3333 10.5267 13.3333 12V13.3333H2.66667V12C2.66667 10.5267 5.05333 9.33333 8 9.33333Z" fill="#0E774F"/>
            </svg>
            <span class="mu-pk-title">Progres Murid</span>
        </div>
        <div class="mu-pk-header-right">
            {{-- Cetak Hafalan (Figma 459:3148) --}}
            <a href="{{ route('siswa.cetak', $murid->id_murid) }}" class="mu-pk-btn-cetak" target="_blank" title="Cetak Hafalan Murid">
                <span>Cetak Hafalan</span>
                {{-- fa:print SVG (Figma 459:3150) --}}
                <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.46154 18.75H17.5385V16.25H9.46154V18.75ZM9.46154 12.5H17.5385V8.75H16.0962C15.8558 8.75 15.6514 8.65885 15.4832 8.47656C15.3149 8.29427 15.2308 8.07292 15.2308 7.8125V6.25H9.46154V12.5ZM19.8462 13.125C19.8462 12.9557 19.7891 12.8092 19.6749 12.6855C19.5607 12.5618 19.4255 12.5 19.2692 12.5C19.113 12.5 18.9778 12.5618 18.8636 12.6855C18.7494 12.8092 18.6923 12.9557 18.6923 13.125C18.6923 13.2943 18.7494 13.4408 18.8636 13.5645C18.9778 13.6882 19.113 13.75 19.2692 13.75C19.4255 13.75 19.5607 13.6882 19.6749 13.5645C19.7891 13.4408 19.8462 13.2943 19.8462 13.125ZM21 13.125V17.1875C21 17.2721 20.9715 17.3454 20.9144 17.4072C20.8573 17.4691 20.7897 17.5 20.7115 17.5H18.6923V19.0625C18.6923 19.3229 18.6082 19.5443 18.4399 19.7266C18.2716 19.9089 18.0673 20 17.8269 20H9.17308C8.93269 20 8.72837 19.9089 8.5601 19.7266C8.39183 19.5443 8.30769 19.3229 8.30769 19.0625V17.5H6.28846C6.21034 17.5 6.14273 17.4691 6.08564 17.4072C6.02855 17.3454 6 17.2721 6 17.1875V13.125C6 12.6107 6.16977 12.1696 6.50931 11.8018C6.84886 11.4339 7.25601 11.25 7.73077 11.25H8.30769V5.9375C8.30769 5.67708 8.39183 5.45573 8.5601 5.27344C8.72837 5.09115 8.93269 5 9.17308 5H15.2308C15.4712 5 15.7356 5.0651 16.024 5.19531C16.3125 5.32552 16.5409 5.48177 16.7091 5.66406L18.0793 7.14844C18.2476 7.33073 18.3918 7.57812 18.512 7.89062C18.6322 8.20312 18.6923 8.48958 18.6923 8.75V11.25H19.2692C19.744 11.25 20.1511 11.4339 20.4907 11.8018C20.8302 12.1696 21 12.6107 21 13.125Z" fill="white"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ---- Informasi Pribadi (Figma Node 459:3152 & 459:3154) ---- --}}
    <div class="mu-pk-section">
        <p class="mu-pk-section-title">Informasi Pribadi</p>
        <div class="mu-pk-info-row">
            {{-- Col 1: Avatar + Nama + Gender + Email (Figma 459:3155) --}}
            <div class="mu-pk-user-col">
                @if($murid->foto_url)
                    <img src="{{ $murid->foto_url }}" class="mu-pk-avatar-img" alt="Foto"
                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                    <div class="mu-pk-avatar-circle" style="display: none;">
                        {{ strtoupper(substr($murid->user->nama ?? 'M', 0, 2)) }}
                    </div>
                @else
                    <div class="mu-pk-avatar-circle">
                        {{ strtoupper(substr($murid->user->nama ?? 'M', 0, 2)) }}
                    </div>
                @endif
                <div class="mu-pk-user-info">
                    <p class="mu-pk-user-name">{{ $murid->user->nama ?? '-' }}</p>
                    <div class="mu-pk-user-meta">
                        {{-- bi:gender-ambiguous (Figma 459:3163) --}}
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.5 1C11.3674 1 11.2402 0.947322 11.1464 0.853553C11.0527 0.759785 11 0.632608 11 0.5C11 0.367392 11.0527 0.240215 11.1464 0.146447C11.2402 0.0526784 11.3674 0 11.5 0L15.5 0C15.6326 0 15.7598 0.0526784 15.8536 0.146447C15.9473 0.240215 16 0.367392 16 0.5V4.5C16 4.63261 15.9473 4.75979 15.8536 4.85355C15.7598 4.94732 15.6326 5 15.5 5C15.3674 5 15.2402 4.94732 15.1464 4.85355C15.0527 4.75979 15 4.63261 15 4.5V1.707L11.55 5.157C11.8473 5.7291 12.0018 6.36457 12.0005 7.00929C11.9993 7.65401 11.8422 8.28886 11.5426 8.85977C11.2431 9.43067 10.81 9.92071 10.2802 10.2881C9.75041 10.6556 9.13967 10.8895 8.5 10.97V13H10C10.1326 13 10.2598 13.0527 10.3536 13.1464C10.4473 13.2402 10.5 13.3674 10.5 13.5C10.5 13.6326 10.4473 13.7598 10.3536 13.8536C10.2598 13.9473 10.1326 14 10 14H8.5V15.5C8.5 15.6326 8.44732 15.7598 8.35355 15.8536C8.25979 15.9473 8.13261 16 8 16C7.86739 16 7.74021 15.9473 7.64645 15.8536C7.55268 15.7598 7.5 15.6326 7.5 15.5V14H6C5.86739 14 5.74021 13.9473 5.64645 13.8536C5.55268 13.7598 5.5 13.6326 5.5 13.5C5.5 13.3674 5.55268 13.2402 5.64645 13.1464C5.74021 13.0527 5.86739 13 6 13H7.5V10.97C6.85984 10.8892 6.24874 10.6547 5.71883 10.2865C5.18893 9.91835 4.75595 9.42746 4.45684 8.85573C4.15774 8.28401 4.00138 7.6484 4.00112 7.00316C4.00085 6.35792 4.15668 5.72219 4.45532 5.15021C4.75395 4.57824 5.18653 4.08699 5.71613 3.7184C6.24573 3.34981 6.85664 3.1148 7.49673 3.03344C8.13683 2.95208 8.7871 3.02677 9.39208 3.25115C9.99705 3.47553 10.5388 3.84293 10.971 4.322L14.293 1H11.5ZM10.503 5.346C10.0642 4.68216 9.37966 4.21983 8.59998 4.0607C7.8203 3.90158 7.00934 4.0587 6.3455 4.4975C5.68166 4.9363 5.21933 5.62084 5.0602 6.40052C4.90108 7.1802 5.0582 7.99116 5.497 8.655C5.93593 9.31884 6.6206 9.78112 7.40037 9.94015C8.18015 10.0992 8.99116 9.94193 9.655 9.503C10.3188 9.06407 10.7811 8.3794 10.9402 7.59963C11.0992 6.81985 10.9419 6.00884 10.503 5.345V5.346Z" fill-rule="evenodd" clip-rule="evenodd" fill="#55566E"/>
                        </svg>
                        <span>{{ $murid->jenis_kelamin ?? 'Laki-Laki' }}</span>
                    </div>
                    <div class="mu-pk-user-meta">
                        {{-- basil:gmail-outline (Figma 459:3167) --}}
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.904 4.56C3.82784 4.51893 3.74232 4.49832 3.65581 4.5002C3.5693 4.50208 3.48475 4.52638 3.41045 4.57072C3.33614 4.61506 3.27461 4.67793 3.23188 4.75318C3.18915 4.82842 3.16668 4.91347 3.16667 5V11.3333C3.16667 11.4659 3.21935 11.5931 3.31311 11.6869C3.40688 11.7807 3.53406 11.8333 3.66667 11.8333C3.79927 11.8333 3.92645 11.7807 4.02022 11.6869C4.11399 11.5931 4.16667 11.4659 4.16667 11.3333V5.83733L7.76267 7.77333C7.91067 7.85333 8.08933 7.85333 8.23733 7.77333L11.8333 5.83733V11.3333C11.8333 11.4659 11.886 11.5931 11.9798 11.6869C12.0735 11.7807 12.2007 11.8333 12.3333 11.8333C12.4659 11.8333 12.5931 11.7807 12.6869 11.6869C12.7807 11.5931 12.8333 11.4659 12.8333 11.3333V5C12.8333 4.91347 12.8108 4.82842 12.7681 4.75318C12.7254 4.67793 12.6639 4.61506 12.5896 4.57072C12.5152 4.52638 12.4307 4.50208 12.3442 4.5002C12.2577 4.49832 12.1722 4.51893 12.096 4.56L8 6.76533L3.904 4.56Z" fill="#55566E"/>
                            <path d="M11.54 2.48133C9.1847 2.27065 6.8153 2.27065 4.46 2.48133L3.448 2.57133C2.92086 2.6184 2.42497 2.84179 2.04043 3.20541C1.65588 3.56902 1.40513 4.05165 1.32867 4.57533C0.996543 6.84637 0.996543 9.15363 1.32867 11.4247C1.4053 11.9483 1.65621 12.4309 2.04087 12.7944C2.42554 13.1579 2.9215 13.3811 3.44867 13.428L4.45933 13.5187C6.81533 13.7293 9.18467 13.7293 11.5407 13.5187L12.552 13.428C13.079 13.381 13.5749 13.1577 13.9594 12.7942C14.3439 12.4307 14.5947 11.9482 14.6713 11.4247C15.0035 9.15363 15.0035 6.84637 14.6713 4.57533C14.5948 4.05155 14.344 3.56884 13.9593 3.20521C13.5746 2.84158 13.0786 2.61826 12.5513 2.57133L11.54 2.48133ZM4.54933 3.47733C6.84562 3.27155 9.15571 3.27155 11.452 3.47733L12.4633 3.568C12.7665 3.59493 13.0518 3.72327 13.273 3.9323C13.4943 4.14133 13.6386 4.41884 13.6827 4.72C14.0008 6.8951 14.0008 9.1049 13.6827 11.28C13.6386 11.5812 13.4943 11.8587 13.273 12.0677C13.0518 12.2767 12.7665 12.4051 12.4633 12.432L11.452 12.5227C9.156 12.728 6.84533 12.728 4.54933 12.5227L3.538 12.432C3.23482 12.4051 2.94957 12.2767 2.72832 12.0677C2.50708 11.8587 2.36275 11.5812 2.31867 11.28C2.00057 9.1049 2.00057 6.8951 2.31867 4.72C2.36275 4.41884 2.50708 4.14133 2.72832 3.9323C2.94957 3.72327 3.23482 3.59493 3.538 3.568L4.54933 3.47733Z" fill-rule="evenodd" clip-rule="evenodd" fill="#55566E"/>
                        </svg>
                        <span>{{ $murid->user->email ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Vertical Divider (Figma 459:3171) --}}
            <div class="mu-pk-vdivider"></div>

            {{-- Col 2: Guru Pengampu, Kelas, Waktu (Figma 459:3172) --}}
            <div class="mu-pk-meta-col">
                <div class="mu-pk-meta-row">
                    <span class="mu-pk-meta-label">Guru Pengampu</span>
                    <span class="mu-pk-meta-val">: {{ $murid->kelas->guru->user->nama ?? '-' }}</span>
                </div>
                <div class="mu-pk-meta-row">
                    <span class="mu-pk-meta-label">Kelas</span>
                    <span class="mu-pk-meta-val">: {{ $murid->kelas->nama_kelas ?? '-' }}</span>
                </div>
                <div class="mu-pk-meta-row">
                    <span class="mu-pk-meta-label">Waktu</span>
                    <span class="mu-pk-meta-val">
                        : {{ $murid->kelas ? ($murid->kelas->hari . ' ' . substr($murid->kelas->jam_mulai,0,5) . ' - ' . substr($murid->kelas->jam_selesai,0,5)) : '-' }}
                    </span>
                </div>
            </div>

            {{-- Vertical Divider (Figma 459:3182) --}}
            <div class="mu-pk-vdivider"></div>

            {{-- Col 3: Donut Chart Progress (Figma 459:3183) --}}
            <div class="mu-pk-chart-col">
                <div class="mu-pk-chart-wrap">
                    @php
                        $r = 41;
                        $circ = 2 * 3.14159 * $r; // 257.6
                        $dashOffset = $circ - ($circ * $persen / 100);
                    @endphp
                    <svg class="mu-pk-chart-svg" viewBox="0 0 100 100">
                        {{-- Background Track circle: orange / light gray #FDBA74 --}}
                        <circle cx="50" cy="50" r="{{ $r }}"
                            stroke="#FDBA74" stroke-width="10" fill="none" opacity="0.35"/>
                        {{-- Completed Fill circle: green #0E7651 --}}
                        <circle cx="50" cy="50" r="{{ $r }}"
                            stroke="#0E7651" stroke-width="10" fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $dashOffset }}"/>
                    </svg>
                    <div class="mu-pk-chart-pct">{{ $persen }}%</div>
                </div>
                <p class="mu-pk-chart-sub">
                    <span style="color:#FDBA74;font-weight:700;">{{ $selesaiCount }}</span>
                    <span> Dari </span>
                    <span style="color:#0E7651;font-weight:700;">{{ $totalSurat }}</span>
                    <span> Surat Selesai</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ---- Daftar Surat (Figma Node 459:3190) ---- --}}
    <div class="mu-pk-section">
        <p class="mu-pk-section-title">Daftar Surat</p>
        @if(count($suratProgress) > 0)
        <div class="mu-pk-surat-grid">
            @foreach($suratProgress as $sp)
            @php
                $st = $sp['status'];
                $badgeCls = $st === 'Selesai' ? 'mu-pk-badge--selesai' : ($st === 'Progress' ? 'mu-pk-badge--progress' : 'mu-pk-badge--belum');
            @endphp
            <div class="mu-pk-surat-card">
                {{-- Islamic 8-Point Star Badge (Figma 459:3194 / Star 4) --}}
                <div class="mu-pk-star-badge">
                    <svg viewBox="0 0 50 52" fill="none" xmlns="http://www.w3.org/2000/svg" class="mu-pk-star-svg">
                        <path d="M19.5494 4.18663C20.9117 2.65366 21.5928 1.88717 22.3206 1.46016C23.9342 0.513281 25.8991 0.513281 27.5128 1.46016C28.2405 1.88717 28.9216 2.65366 30.2839 4.18663C30.8977 4.87729 31.2045 5.22261 31.5459 5.49988C32.2951 6.10849 33.185 6.49616 34.124 6.62302C34.5518 6.6808 35.001 6.66484 35.8993 6.63291C37.8933 6.56204 38.8902 6.5266 39.6919 6.76584C41.4695 7.29635 42.8589 8.75762 43.3633 10.6272C43.5908 11.4703 43.5571 12.5189 43.4897 14.616C43.4594 15.5608 43.4442 16.0332 43.4992 16.4831C43.6198 17.4706 43.9884 18.4066 44.5671 19.1946C44.8307 19.5536 45.159 19.8763 45.8157 20.5218C47.2733 21.9545 48.0021 22.6709 48.4081 23.4363C49.3084 25.1334 49.3084 27.1999 48.4081 28.8971C48.0021 29.6624 47.2733 30.3788 45.8157 31.8115C45.159 32.457 44.8307 32.7798 44.5671 33.1388C43.9884 33.9268 43.6198 34.8627 43.4992 35.8503C43.4442 36.3002 43.4594 36.7726 43.4897 37.7174C43.5571 39.8145 43.5908 40.863 43.3633 41.7061C42.8589 43.5757 41.4695 45.037 39.6919 45.5675C38.8902 45.8067 37.8933 45.7713 35.8993 45.7004C35.001 45.6685 34.5518 45.6525 34.124 45.7103C33.185 45.8372 32.2951 46.2248 31.5459 46.8335C31.2045 47.1107 30.8977 47.456 30.2839 48.1467C28.9216 49.6797 28.2405 50.4462 27.5128 50.8732C25.8991 51.8201 23.9342 51.8201 22.3206 50.8732C21.5928 50.4462 20.9117 49.6797 19.5494 48.1467C18.9357 47.456 18.6288 47.1107 18.2875 46.8335C17.5382 46.2248 16.6483 45.8372 15.7093 45.7103C15.2815 45.6525 14.8324 45.6685 13.934 45.7004C11.9401 45.7713 10.9431 45.8067 10.1414 45.5675C8.3638 45.037 6.9744 43.5757 6.46998 41.7061C6.24251 40.863 6.2762 39.8145 6.34359 37.7174C6.37395 36.7726 6.38913 36.3002 6.33418 35.8503C6.21357 34.8627 5.84496 33.9268 5.26628 33.1388C5.00265 32.7798 4.6743 32.457 4.01762 31.8115C2.56003 30.3788 1.83124 29.6624 1.42523 28.8971C0.524922 27.1999 0.524922 25.1334 1.42523 23.4363C1.83124 22.6709 2.56003 21.9545 4.01762 20.5218C4.6743 19.8763 5.00265 19.5536 5.26628 19.1946C5.84496 18.4066 6.21357 17.4706 6.33418 16.4831C6.38913 16.0332 6.37395 15.5608 6.34359 14.616C6.2762 12.5189 6.24251 11.4703 6.46998 10.6272C6.9744 8.75762 8.3638 7.29635 10.1414 6.76584C10.9431 6.5266 11.9401 6.56204 13.934 6.63291Z" fill="#0E774F"/>
                    </svg>
                    <span class="mu-pk-star-num">{{ $sp['target']->surat->id_surat ?? '' }}</span>
                </div>

                {{-- Surat Name & Setoran Terakhir --}}
                <p class="mu-pk-surat-name">{{ $sp['target']->surat->nama_surat ?? '-' }}</p>
                <p class="mu-pk-surat-last">
                    @if($sp['terakhir'])
                        Setoran Terakhir: Ayat {{ $sp['terakhir']->ayat_mulai }}-{{ $sp['terakhir']->ayat_selesai }}
                    @else
                        Setoran Terakhir: Ayat 0-0
                    @endif
                </p>

                {{-- Status Badge (top right) --}}
                <div class="mu-pk-badge {{ $badgeCls }}">{{ $st }}</div>

                {{-- Ayat Lulus Count (Figma 459:3209) --}}
                <p class="mu-pk-lulus-text">
                    @if($st === 'Selesai')
                        <span style="font-weight:700; color:#0E7651;">{{ $sp['totalSetor'] }}</span>
                        <span> dari </span>
                        <span style="font-weight:700; color:#0E7651;">{{ $sp['totalAyat'] }}</span>
                        <span> ayat lulus</span>
                    @elseif($st === 'Progress')
                        <span style="font-weight:700; color:#FDBA74;">{{ $sp['totalSetor'] }}</span>
                        <span> dari </span>
                        <span style="font-weight:700; color:#0E7651;">{{ $sp['totalAyat'] }}</span>
                        <span> ayat lulus</span>
                    @else
                        <span style="font-weight:700; color:#6C757D;">0</span>
                        <span> dari </span>
                        <span style="font-weight:700; color:#0E7651;">{{ $sp['totalAyat'] }}</span>
                        <span> ayat lulus</span>
                    @endif
                </p>

                {{-- Progress Bar (Figma 459:3204) --}}
                <div class="mu-pk-progbar-row">
                    <div class="mu-pk-progbar-track">
                        <div class="mu-pk-progbar-fill" style="width: {{ $sp['persen'] }}%;"></div>
                    </div>
                    <span class="mu-pk-progbar-label">{{ $sp['persen'] }}%</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="mu-pk-empty">Belum ada target hafalan untuk kelas ini.</div>
        @endif
    </div>

    {{-- ---- Riwayat Setoran Table (Figma Node 459:3261) ---- --}}
    <div class="mu-pk-section">
        <p class="mu-pk-section-title">Riwayat Setoran</p>
        <div class="mu-pk-table-wrap">
            <table class="mu-pk-table">
                <thead>
                    <tr>
                        <th style="width: 61px;">No</th>
                        <th style="width: 205px;">Nama Lengkap</th>
                        <th style="width: 169px;">Surat</th>
                        <th style="width: 156px;">Ayat</th>
                        <th style="width: 122px;">Tanggal Setoran</th>
                        <th style="width: 106px;">Status</th>
                        {{-- NOTE: Column 'Aksi' is intentionally excluded per user instruction --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($setoranList as $i => $setor)
                    <tr>
                        <td class="mu-pk-col-no">{{ $i + 1 }}</td>
                        <td class="mu-pk-col-nama">{{ $setor->murid->user->nama ?? ($murid->user->nama ?? '-') }}</td>
                        <td>{{ $setor->surat->nama_surat ?? '-' }}</td>
                        <td>{{ $setor->ayat_mulai }}–{{ $setor->ayat_selesai }}</td>
                        <td>{{ $setor->tanggal ? \Carbon\Carbon::parse($setor->tanggal)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @php
                                $rawStatus = strtolower(trim($setor->status ?? ''));
                                if (in_array($rawStatus, ['lancar', 'lulus', 'selesai', 'tuntas'])) {
                                    $label = 'Lulus';
                                } elseif (in_array($rawStatus, ['kurang lancar', 'progress'])) {
                                    $label = 'Perlu Perbaikan';
                                } elseif (in_array($rawStatus, ['mengulang'])) {
                                    $label = 'Perlu Perbaikan';
                                } else {
                                    $label = ucfirst($setor->status ?? 'Menunggu');
                                }
                            @endphp
                            {{ $label }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="mu-pk-empty">Belum ada riwayat setoran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
