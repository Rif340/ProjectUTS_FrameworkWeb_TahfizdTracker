@extends('layouts.app')

@section('title', 'Gagal')
@section('page_title', 'Kelola Pengguna')

@push('styles')
<link rel="stylesheet" href="/css/kelola-pengguna.css">
@endpush

@section('content')

{{-- Background --}}
<div class="pg-card-head">
    <div class="pg-card-head-left">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0e774f" stroke-width="1.5"/>
            <path d="M3.41003 22C3.41003 18.13 7.26003 15 12 15" stroke="#0e774f" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18.5 22C20.433 22 22 20.433 22 18.5C22 16.567 20.433 15 18.5 15C16.567 15 15 16.567 15 18.5C15 20.433 16.567 22 18.5 22Z" stroke="#0e774f" stroke-width="1.5"/>
            <path d="M22 22L21 21" stroke="#0e774f" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="pg-head-title">Detail Pengguna</span>
    </div>
    <div class="pg-card-head-right">
        <div class="pg-show-select">
            <span>Tampilkan 10</span>
        </div>
        <button class="pg-btn-tambah" disabled>+ Tambah</button>
    </div>
</div>

{{-- Alert Modal Overlay Gagal --}}
<div class="pg-alert-overlay">
    <div class="pg-alert-box alert-gagal">
        {{-- Close --}}
        <a href="{{ route('pengguna.index') }}" class="pg-delete-close" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </a>

        {{-- Logo --}}
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="pg-alert-logo">

        {{-- Title --}}
        <h1 class="pg-alert-title">Gagal</h1>
        <p class="pg-alert-message">
            @if(session('error'))
                {{ session('error') }}
            @else
                Terjadi kesalahan saat menyimpan data pengguna. Silakan coba lagi.
            @endif
        </p>

        {{-- Button --}}
        <div class="pg-alert-btn-wrap gagal">
            <a href="{{ route('pengguna.index') }}">Kembali</a>
        </div>
    </div>
</div>

@endsection
