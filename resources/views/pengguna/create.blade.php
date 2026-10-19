@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@push('styles')
<link rel="stylesheet" href="/css/kelola-pengguna.css">
@endpush

@section('content')

{{-- Backdrop + Modal Form Tambah --}}
<div class="pg-form-overlay">
    <div class="pg-modal">
        {{-- Logo --}}
        <img src="/gambar/logo.png" alt="Logo" class="pg-modal-logo">

        {{-- Judul --}}
        <div class="pg-modal-title-block">
            <p class="pg-modal-title">Form Tambah Pengguna</p>
            <p class="pg-modal-subtitle">Isi dengan lengkap, dan pastikan tidak ada yang salah</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('pengguna.store') }}" style="width:100%; display:flex; flex-direction:column; gap:20px;">
            @csrf

            <div class="pg-form-grid">
                {{-- Nama Lengkap --}}
                <div class="pg-form-row">
                    <label class="pg-form-label">Nama Lengkap <span>*</span></label>
                    <input type="text" name="nama" class="pg-form-input"
                           maxlength="100"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s.,'’\-]/g, '')"
                           value="{{ old('nama') }}" placeholder="Nama lengkap" required>
                </div>

                {{-- Role --}}
                <div class="pg-form-row">
                    <label class="pg-form-label">Role <span>*</span></label>
                    <select name="role" class="pg-form-select" required>
                        <option value="">Pilih Role</option>
                        <option value="Admin"  {{ old('role') === 'Admin'  ? 'selected' : '' }}>Admin</option>
                        <option value="Guru"   {{ old('role') === 'Guru'   ? 'selected' : '' }}>Guru</option>
                        <option value="Murid"  {{ old('role') === 'Murid'  ? 'selected' : '' }}>Murid</option>
                    </select>
                </div>

                {{-- Email --}}
                <div class="pg-form-row">
                    <label class="pg-form-label">Email Akun <span>*</span></label>
                    <input type="email" name="email" class="pg-form-input"
                           maxlength="100"
                           value="{{ old('email') }}" placeholder="email@contoh.com" required>
                </div>

                {{-- NIP / NISN --}}
                <div class="pg-form-row">
                    <label class="pg-form-label">NIP / NISN</label>
                    <input type="text" name="nip_nisn" class="pg-form-input"
                           inputmode="numeric"
                           maxlength="18"
                           oninput="this.value = this.value.replace(/\D/g, '')"
                           value="{{ old('nip_nisn') }}" placeholder="NIP atau NISN (angka saja)">
                </div>

                {{-- Password --}}
                <div class="pg-form-row">
                    <label class="pg-form-label">Password <span>*</span></label>
                    <div class="pg-password-wrap">
                        <input type="password" name="password" id="passwordCreate" class="pg-form-input"
                               placeholder="Minimal 6 karakter" required>
                        <img src="/icon/icon_mata_disable.png" alt="Toggle Password" id="togglePasswordCreate"
                             class="pg-toggle-pwd" title="Lihat Password" onclick="togglePasswordVisibility('passwordCreate', 'togglePasswordCreate')">
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="pg-modal-actions">
                <a href="{{ route('pengguna.index') }}" class="pg-btn-batal">Batal</a>
                <button type="submit" class="pg-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.src = '/icon/akar_mata_open.png';
        icon.title = 'Sembunyikan Password';
    } else {
        input.type = 'password';
        icon.src = '/icon/icon_mata_disable.png';
        icon.title = 'Lihat Password';
    }
}
</script>
@endsection
