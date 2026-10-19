@extends('layouts.app')

@section('title', 'Gagal')
@section('page_title', 'Kelola Murid')

@push('styles')
<link rel="stylesheet" href="/css/murid.css">
@endpush

@section('content')

<div class="mu-card-head">
    <div class="mu-card-head-left">
        <span class="mu-head-title">Daftar Murid</span>
    </div>
</div>

<div class="mu-alert-overlay">
    <div class="mu-alert-box">
        <a href="{{ route('siswa.index') }}" class="mu-alert-close" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </a>
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="mu-alert-logo">
        <h1 class="mu-alert-title gagal">Gagal</h1>
        <p class="mu-alert-message">
            {{ session('error') ?? 'Terjadi kesalahan. Silakan coba lagi.' }}
        </p>
        <div class="mu-alert-btn-wrap gagal">
            <a href="{{ route('siswa.index') }}">Kembali</a>
        </div>
    </div>
</div>

@endsection
