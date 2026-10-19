@extends('layouts.app')

@section('title', 'Detail Target - Berhasil')

@section('breadcrumb')
<div style="display: flex; align-items: center; gap: 8px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; color: #71717A;">
    <span>Tahfidz</span>
    <span style="color: #A1A1AA; font-size: 11px;">&gt;</span>
    <span>Target Hafalan</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/tahfizd.css">
@endpush

@section('content')

{{-- ===== CARD HEADER (Figma Node 603:1919) ===== --}}
<div class="tz-card-head">
    <div class="tz-card-head-left">
        {{-- Quran Icon --}}
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.4173 6.7288L9.5 12.6669M9.5 12.6669L4.74962 16.2298V12.5117M9.5 12.6669L1.5827 6.7288M9.5 12.6669L14.2504 16.2298V12.5117M15.5552 4.30368L15.0334 3.47788L15.0322 3.47594C14.7448 3.02039 14.6008 2.7922 14.4119 2.77164C14.2235 2.75026 14.0121 2.9672 13.5901 3.40108C12.2267 4.80248 10.8634 4.59504 9.5 7.11518C8.13664 4.59504 6.77328 4.80248 5.40992 3.40108C4.98793 2.9672 4.77654 2.75026 4.58811 2.77164C4.39915 2.7922 4.2552 3.02039 3.96782 3.47594L3.9666 3.47788L3.44564 4.30368C3.24375 4.62196 3.1432 4.78189 3.17091 4.94974C3.19783 5.11759 3.34272 5.2324 3.63328 5.46201L8.52776 9.34C8.99567 9.71053 9.23002 9.8958 9.5 9.8958C9.76998 9.8958 10.0043 9.71053 10.4722 9.34L15.3667 5.46201C15.6573 5.2324 15.8022 5.11759 15.8291 4.94974C15.8568 4.78189 15.7563 4.62196 15.5552 4.30368Z" stroke-width="1.5" stroke-linecap="round" stroke="#0E774F"/>
        </svg>
        <span class="tz-head-title">Detail Target</span>
    </div>
    <div class="tz-card-head-right">
        {{-- Tampilkan 10 Dropdown --}}
        <select class="tz-select-show" onchange="location.href='{{ route('tahfizd.target.index') }}?per_page='+this.value+'&surat={{ request('surat') }}&kelas={{ request('kelas') }}'">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>Tampilkan {{ $n }}</option>
            @endforeach
        </select>
        {{-- Tombol + Tambah --}}
        <a href="{{ route('tahfizd.target.index') }}" class="tz-btn-tambah" style="text-decoration: none;">+ Tambah</a>
    </div>
</div>

{{-- ===== CONTENT (Figma Node 603:1932) ===== --}}
<div class="tz-tg-container">
    {{-- Filter Bar (Figma Node 603:1642 & 402:7707) --}}
    <form method="GET" action="{{ route('tahfizd.target.index') }}" class="tz-tg-filters" id="form-filter-target">
        <input type="hidden" name="per_page" value="{{ $perPage }}">

        {{-- Filter Nama Surat --}}
        <div class="tz-tg-filter-group">
            <label class="tz-tg-filter-label">Nama Surat</label>
            <select name="surat" class="tz-tg-select surat" onchange="document.getElementById('form-filter-target').submit()">
                <option value="">Nama Surat</option>
                @foreach($suratList as $surat)
                    <option value="{{ $surat->id_surat }}" {{ request('surat') == $surat->id_surat ? 'selected' : '' }}>
                        {{ $surat->nama_surat }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Kelas --}}
        <div class="tz-tg-filter-group">
            <label class="tz-tg-filter-label">Kelas</label>
            <select name="kelas" class="tz-tg-select kelas" onchange="document.getElementById('form-filter-target').submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kls)
                    <option value="{{ $kls->id_kelas }}" {{ request('kelas') == $kls->id_kelas ? 'selected' : '' }}>
                        {{ $kls->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Title (Figma 603:1933) --}}
    <h3 class="tz-tg-heading">Daftar Target Hafalan</h3>

    {{-- Table (Figma Node 603:1935) --}}
    <div class="tz-tg-table-wrap">
        <table class="tz-tg-table">
            <thead>
                <tr>
                    <th style="width: 61px;">No</th>
                    <th style="width: 113px;">Nama Kelas</th>
                    <th style="width: 123px;">No Surat</th>
                    <th style="width: 117px;">Nama Surat</th>
                    <th style="width: 117px;">Juz</th>
                    <th style="width: 137px;">Jumlah Ayat</th>
                    <th style="width: 238px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($targetList as $i => $target)
                <tr>
                    <td style="font-weight: 600;">{{ $targetList->firstItem() + $i }}</td>
                    <td style="padding-left: 20px; text-align: left;">{{ $target->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $target->surat->id_surat ?? '-' }}</td>
                    <td>{{ $target->surat->nama_surat ?? '-' }}</td>
                    <td>{{ $target->surat->juz ?? '-' }}</td>
                    <td>{{ $target->surat->jumlah_ayat ? $target->surat->jumlah_ayat . ' Ayat' : '-' }}</td>
                    <td>
                        <a href="{{ route('tahfizd.target.index') }}" class="tz-tg-btn-hapus" title="Hapus Target">
                            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.099 13.11C12.0719 13.3549 11.9554 13.5811 11.7718 13.7453C11.5881 13.9096 11.3504 14.0003 11.104 14H4.897C4.6506 14.0003 4.4129 13.9096 4.2292 13.7453C4.0456 13.5811 3.9291 13.3549 3.902 13.11L3 5H13L12.099 13.11ZM10 0C10.2652 0 10.5196 0.1054 10.7071 0.2929C10.8946 0.4804 11 0.7348 11 1V2H13C13.2652 2 13.5196 2.1054 13.7071 2.2929C13.8946 2.4804 14 2.7348 14 3V4H2V3C2 2.7348 2.1054 2.4804 2.2929 2.2929C2.4804 2.1054 2.7348 2 3 2H5V1C5 0.7348 5.1054 0.4804 5.2929 0.2929C5.4804 0.1054 5.7348 0 6 0H10ZM10 1H6V2H10V1Z" fill="#FF0000"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; color: #71717A;">Belum ada target hafalan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($targetList->hasPages())
    <div class="tz-pagination" style="margin-top: 24px;">
        @if($targetList->onFirstPage())
            <span>&laquo;</span>
        @else
            <a href="{{ $targetList->previousPageUrl() }}">&laquo;</a>
        @endif
        @foreach($targetList->getUrlRange(1, $targetList->lastPage()) as $page => $url)
            @if($page == $targetList->currentPage())
                <span class="current">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach
        @if($targetList->hasMorePages())
            <a href="{{ $targetList->nextPageUrl() }}">&raquo;</a>
        @else
            <span>&raquo;</span>
        @endif
    </div>
    @endif
</div>

{{-- ===================================================
     MODAL ALERT BERHASIL (Figma Node 603:2185)
     =================================================== --}}
<div class="tz-del-overlay open">
    <div class="tz-del-modal">
        <a href="{{ route('tahfizd.target.index') }}" class="tz-del-close" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </a>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-del-logo">

        <div style="text-align: center; width: 100%;">
            <h2 class="tz-del-title">Berhasil</h2>
            <p class="tz-del-subtitle">
                @if(!empty($isDelete))
                    Data Berhasil Di Hapus
                @else
                    Data Berhasil Di Tambahkan Atau Di Perbarui
                @endif
            </p>
        </div>

        <div style="width: 100%;">
            <a href="{{ route('tahfizd.target.index') }}" class="tz-del-btn-setujui" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">Terima</a>
        </div>
    </div>
</div>

@endsection
