@extends('layouts.app')

@section('title', 'Gagal - Tahfizd')

@section('breadcrumb')
<div style="display: flex; align-items: center; gap: 8px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; color: #71717A;">
    <span>Tahfizd</span>
    <span style="color: #A1A1AA; font-size: 11px;">&gt;</span>
    <span>Detail Kelas</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/tahfizd.css">
@endpush

@section('content')

{{-- Background dummy header --}}
<div class="tz-card-head">
    <div class="tz-card-head-left">
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.4173 6.7288L9.5 12.6669M9.5 12.6669L4.74962 16.2298V12.5117M9.5 12.6669L1.5827 6.7288M9.5 12.6669L14.2504 16.2298V12.5117M15.5552 4.30368L15.0334 3.47788L15.0322 3.47594C14.7448 3.02039 14.6008 2.7922 14.4119 2.77164C14.2235 2.75026 14.0121 2.9672 13.5901 3.40108C12.2267 4.80248 10.8634 4.59504 9.5 7.11518C8.13664 4.59504 6.77328 4.80248 5.40992 3.40108C4.98793 2.9672 4.77654 2.75026 4.58811 2.77164C4.39915 2.7922 4.2552 3.02039 3.96782 3.47594L3.9666 3.47788L3.44564 4.30368C3.24375 4.62196 3.1432 4.78189 3.17091 4.94974C3.19783 5.11759 3.34272 5.2324 3.63328 5.46201L8.52776 9.34C8.99567 9.71053 9.23002 9.8958 9.5 9.8958C9.76998 9.8958 10.0043 9.71053 10.4722 9.34L15.3667 5.46201C15.6573 5.2324 15.8022 5.11759 15.8291 4.94974C15.8568 4.78189 15.7563 4.62196 15.5552 4.30368Z" stroke-width="1.5" stroke-linecap="round" stroke="#0E774F"/>
        </svg>
        <span class="tz-head-title">Kelas</span>
    </div>
    <div class="tz-card-head-right">
        <button class="tz-btn-tambah" disabled>+ Tambah</button>
    </div>
</div>

{{-- Alert Gagal Modal Overlay --}}
<div class="tz-del-overlay open">
    <div class="tz-del-modal" style="border-color: #E53E3E;">
        <a href="{{ route('tahfizd.index') }}" class="tz-del-close" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </a>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-del-logo">

        <div style="text-align: center; width: 100%;">
            <h2 class="tz-del-title" style="color: #E53E3E;">Gagal</h2>
            <p class="tz-del-subtitle">{{ session('error') ?? 'Terjadi kesalahan saat memproses data kelas.' }}</p>
        </div>

        <div style="width: 100%;">
            <a href="{{ route('tahfizd.index') }}" class="tz-del-btn-batal" style="text-decoration: none; width: 100%;">Kembali</a>
        </div>
    </div>
</div>

@endsection
