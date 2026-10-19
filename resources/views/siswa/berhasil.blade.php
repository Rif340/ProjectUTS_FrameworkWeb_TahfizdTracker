@extends('layouts.app')

@section('title', 'Berhasil')
@section('page_title', 'Kelola Murid')

@push('styles')
<link rel="stylesheet" href="/css/murid.css">
@endpush

@section('content')

{{-- Background dummy header --}}
<div class="mu-card-head">
    <div class="mu-card-head-left">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0E7651" stroke-width="1.5"/>
            <path d="M3.41 22C3.41 18.13 7.26 15 12 15C16.74 15 20.59 18.13 20.59 22" stroke="#0E7651" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="mu-head-title">Daftar Murid</span>
    </div>
    <div class="mu-card-head-right">
        <button class="mu-btn-tambah" disabled>+ Tambah</button>
    </div>
</div>

{{-- Alert Modal Overlay --}}
<div class="mu-alert-overlay">
    <div class="mu-alert-box">
        <a href="{{ route('siswa.index') }}" class="mu-alert-close" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </a>
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="mu-alert-logo">
        <h1 class="mu-alert-title">Berhasil</h1>
        <p class="mu-alert-message">
            @if($isDelete)
                Data Murid Berhasil Di Hapus
            @else
                Data Murid Berhasil Di Tambahkan Atau Di Perbarui
            @endif
        </p>
        <div class="mu-alert-btn-wrap berhasil">
            <a href="{{ route('siswa.index') }}">Terima</a>
        </div>
    </div>
</div>

@endsection
