@extends('layouts.app')

@section('title', 'User Profile')

@section('breadcrumb')
<div class="breadcrumb-nav">
    <span class="breadcrumb-link" style="cursor: default;">
        User Profile
    </span>
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="transform: rotate(-90deg);">
        <path d="M2.5 3.75L5 6.25L7.5 3.75" stroke="#71717A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="breadcrumb-current">
        {{ $user->nama }}
    </span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/profile.css">
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printAreaQR, #printAreaQR * {
        visibility: visible !important;
    }
    #printAreaQR {
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
    #printAreaQR img {
        width: 200px !important;
        height: 200px !important;
        margin: 0 auto !important;
        display: block !important;
    }
}
</style>
@endpush

@section('content')
<div class="pf-container">

    {{-- Alert Modal Berhasil (Sama persis seperti halaman sebelumnya) --}}
    @if(session('success_modal'))
    <div class="pf-alert-overlay" id="alertModalBerhasil">
        <div class="pf-alert-box">
            {{-- Close Button --}}
            <button type="button" class="pf-alert-close" onclick="document.getElementById('alertModalBerhasil').style.display='none'" title="Tutup">
                <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
            </button>

            {{-- Logo --}}
            <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="pf-alert-logo">

            {{-- Title & Message --}}
            <h1 class="pf-alert-title">Berhasil</h1>
            <p class="pf-alert-message">{{ session('success_modal') }}</p>

            {{-- Button --}}
            <div class="pf-alert-btn-wrap berhasil">
                <button type="button" onclick="document.getElementById('alertModalBerhasil').style.display='none'">
                    Terima
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Alert Modal Gagal --}}
    @if(session('error_modal'))
    <div class="pf-alert-overlay" id="alertModalGagal">
        <div class="pf-alert-box alert-gagal">
            {{-- Close Button --}}
            <button type="button" class="pf-alert-close" onclick="document.getElementById('alertModalGagal').style.display='none'" title="Tutup">
                <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
            </button>

            {{-- Logo --}}
            <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="pf-alert-logo">

            {{-- Title & Message --}}
            <h1 class="pf-alert-title" style="color: #ef4444;">Gagal</h1>
            <p class="pf-alert-message">{{ session('error_modal') }}</p>

            {{-- Button --}}
            <div class="pf-alert-btn-wrap gagal">
                <button type="button" onclick="document.getElementById('alertModalGagal').style.display='none'">
                    Kembali
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Card Header (Figma Node 621:2897) --}}
    <div class="pf-card-head">
        <div class="pf-card-head-left">
            {{-- Icon Profile Green --}}
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0e774f" stroke-width="1.6"/>
                <path d="M20.59 22C20.59 18.13 16.74 15 12 15C7.26 15 3.41 18.13 3.41 22" stroke="#0e774f" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="pf-head-title">Profile</span>
        </div>
        <div class="pf-card-head-right">
            <button type="button" class="pf-btn-qr" onclick="openQRModal()">
                Cetak QR code
            </button>
        </div>
    </div>

    @php
        $hasCustomPhoto = false;
        $photoSrc = null;
        if (!empty($detail->foto)) {
            // Cek path apakah ada di public atau storage
            if (file_exists(public_path($detail->foto))) {
                $hasCustomPhoto = true;
                $photoSrc = asset($detail->foto);
            }
        }

        // Generate inisial 2 huruf dari nama pengguna (contoh: "Belinda Blanche" => "BB", "Ino" => "IN")
        $words = preg_split('/\s+/', trim($user->nama));
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($user->nama, 0, 2));
        }

        $nipNisnValue = '-';
        $roleLabel = ucfirst($user->role);
        $roleLower = strtolower($user->role);

        if ($roleLower === 'admin' && !empty($detail->nip)) {
            $nipNisnValue = $detail->nip;
        } elseif ($roleLower === 'guru' && !empty($detail->nip)) {
            $nipNisnValue = $detail->nip;
        } elseif ($roleLower === 'murid' && !empty($detail->nisn)) {
            $nipNisnValue = $detail->nisn;
        }

        $noHpValue = !empty($detail->no_hp) ? $detail->no_hp : '-';
        $statusValue = !empty($user->status) ? $user->status : 'Aktif';
    @endphp

    <div class="pf-content">

        {{-- Top Bar: Avatar Bulat + Ucapan Selamat Datang + Tombol Edit (Figma 621:2905) --}}
        <div class="pf-top-bar">
            <div class="pf-user-summary">
                <div id="topAvatarWrapper">
                    @if($hasCustomPhoto)
                        <img src="{{ $photoSrc }}" alt="{{ $user->nama }}" class="pf-avatar-thumb" id="topAvatarImg">
                    @else
                        <div class="pf-avatar-initials" id="topAvatarInitials">
                            {{ $initials }}
                        </div>
                    @endif
                </div>
                <div class="pf-user-text">
                    <p class="pf-welcome-label">Selamat Datang,</p>
                    <h2 class="pf-user-fullname">{{ $user->nama }}</h2>
                </div>
            </div>

            <button type="button" class="pf-btn-edit-badge" id="btnSwitchToEdit" onclick="showEditForm()">
                {{-- Icon bytesize:edit --}}
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.5 3.5C16.8978 3.10217 17.4374 2.87868 18 2.87868C18.5626 2.87868 19.1022 3.10217 19.5 3.5C19.8978 3.89783 20.1213 4.43739 20.1213 5C20.1213 5.56261 19.8978 6.10217 19.5 6.5L7 19L3 20L4 16L16.5 3.5Z" stroke="#0e7651" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Edit</span>
            </button>
        </div>

        {{-- ==================== VIEW 1: DATA PROFILE (Figma Node 621:2785) ==================== --}}
        <div class="pf-detail-card" id="profileViewCard">
            <div class="pf-info-grid">
                {{-- 1. Nama Lengkap --}}
                <div class="pf-info-item">
                    <span class="pf-info-label">Nama Lengkap</span>
                    <span class="pf-info-value">{{ $user->nama }}</span>
                </div>

                {{-- 2. NIP / NISN --}}
                <div class="pf-info-item">
                    <span class="pf-info-label">{{ $roleLower === 'murid' ? 'NISN' : 'NIP' }}</span>
                    <span class="pf-info-value">{{ $nipNisnValue }}</span>
                </div>

                {{-- 3. Role --}}
                <div class="pf-info-item">
                    <span class="pf-info-label">Role</span>
                    <span class="pf-info-value">{{ $roleLabel }}</span>
                </div>

                {{-- 4. Email --}}
                <div class="pf-info-item">
                    <span class="pf-info-label">Email</span>
                    <span class="pf-info-value">{{ $user->email }}</span>
                </div>

                {{-- 5. No Handphone --}}
                <div class="pf-info-item">
                    <span class="pf-info-label">No Handphone</span>
                    <span class="pf-info-value">{{ $noHpValue }}</span>
                </div>

                {{-- 6. Status --}}
                <div class="pf-info-item">
                    <span class="pf-info-label">Status</span>
                    <div class="pf-status-badge">
                        <span class="pf-status-icon">&#10003;</span>
                        <span>{{ $statusValue }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== VIEW 2: FORM PERBARUI PROFILE (Figma Node 621:2862) ==================== --}}
        <div class="pf-edit-card" id="profileEditCard" style="display: none;">
            {{-- Header Form Edit (Data Pribadi + Close X) --}}
            <div class="pf-edit-header">
                <h3 class="pf-edit-title">Data Pribadi</h3>
                <button type="button" class="pf-btn-close-edit" onclick="showProfileView()" title="Batal / Tutup">
                    <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup" style="width: 20px; height: 20px;">
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Tengah: Foto Formal + Tombol Ubah Foto --}}
                <div class="pf-edit-photo-wrap">
                    <div id="editPhotoWrapper">
                        @if($hasCustomPhoto)
                            <img src="{{ $photoSrc }}" alt="Preview Foto" class="pf-photo-circle" id="previewPhotoCircle">
                        @else
                            <div class="pf-photo-circle-initials" id="previewPhotoInitials">
                                {{ $initials }}
                            </div>
                            <img src="" alt="Preview Foto" class="pf-photo-circle" id="previewPhotoCircle" style="display: none;">
                        @endif
                    </div>
                    <label for="fotoInput" class="pf-btn-upload-label">
                        Ubah Foto Formal
                    </label>
                    <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="previewImage(this)">
                </div>

                {{-- Input 2 Kolom --}}
                <div class="pf-form-grid">
                    {{-- Nama Lengkap --}}
                    <div class="pf-form-group">
                        <label class="pf-form-label" for="inputNama">
                            Nama Lengkap <span class="pf-required">*</span>
                        </label>
                        <input type="text" name="nama" id="inputNama" class="pf-form-input"
                               placeholder="Masukan Nama Dengan Gelar"
                               maxlength="100"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s.,'’\-]/g, '')"
                               value="{{ old('nama', $user->nama) }}" required>
                    </div>

                    {{-- NIP / NISN --}}
                    <div class="pf-form-group">
                        <label class="pf-form-label" for="inputNipNisn">
                            {{ $roleLower === 'murid' ? 'NISN' : 'NIP' }} <span class="pf-required">*</span>
                        </label>
                        <input type="text" name="nip_nisn" id="inputNipNisn" class="pf-form-input"
                               placeholder="Masukan {{ $roleLower === 'murid' ? 'NISN' : 'NIP' }}"
                               inputmode="numeric"
                               maxlength="18"
                               oninput="this.value = this.value.replace(/\D/g, '')"
                               value="{{ old('nip_nisn', ($nipNisnValue !== '-' ? $nipNisnValue : '')) }}">
                    </div>

                    {{-- Email --}}
                    <div class="pf-form-group">
                        <label class="pf-form-label" for="inputEmail">
                            Email <span class="pf-required">*</span>
                        </label>
                        <input type="email" name="email" id="inputEmail" class="pf-form-input"
                               placeholder="contoh@gmail.com"
                               maxlength="100"
                               value="{{ old('email', $user->email) }}" required>
                    </div>

                    {{-- No Handphone --}}
                    <div class="pf-form-group">
                        <label class="pf-form-label" for="inputNoHp">
                            No Handphone <span class="pf-required">*</span>
                        </label>
                        <input type="text" name="no_hp" id="inputNoHp" class="pf-form-input"
                               placeholder="08xxxxxxxxxx"
                               inputmode="numeric"
                               maxlength="15"
                               oninput="this.value = this.value.replace(/\D/g, '')"
                               value="{{ old('no_hp', ($noHpValue !== '-' ? $noHpValue : '')) }}">
                    </div>
                </div>

                {{-- Action Buttons: BATAL & SIMPAN (Figma Node 621:2942) --}}
                <div class="pf-form-actions">
                    <button type="button" class="pf-btn-batal-form" onclick="showProfileView()">
                        BATAL
                    </button>
                    <button type="submit" class="pf-btn-simpan-form">
                        SIMPAN
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

{{-- Modal Cetak QR Code (Sama pola dengan modul absensi) --}}
<div class="modal-overlay" id="qrModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(2px);">
    <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #6FBE8E; padding: 28px; width: 420px; max-width: 90vw; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <h4 style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; margin: 0;">Kartu QR Code Profil</h4>
            <button type="button" onclick="closeQRModal()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: #666;">&times;</button>
        </div>

        @php
            $qr = $qrData ?? \App\Services\QRCodeService::getOrGenerateUserQR($user, $detail);
        @endphp

        <div id="printAreaQR" style="background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 10px; padding: 18px; width: 100%; box-sizing: border-box;">
            <img src="{{ $qr['url'] }}" alt="QR Code {{ $user->nama }}" style="width: 200px; height: 200px; object-fit: contain; display: block; margin: 0 auto;">
            <div style="margin-top: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 700; color: #111;">{{ $user->nama }}</div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 12px; color: #71717A;">{{ $roleLabel }} • SDIT At-Taubah</div>
            <div style="font-family: 'Poppins', sans-serif; font-size: 11px; color: #0E7651; font-weight: 600; margin-top: 4px;">{{ $roleLower === 'murid' ? 'NISN' : 'NIP' }}: {{ $nipNisnValue }}</div>
        </div>

        <div style="display: flex; gap: 10px; width: 100%;">
            <a href="{{ $qr['url'] }}" download="{{ $qr['filename'] }}" class="pf-btn-qr" style="flex: 1; justify-content: center; height: 40px; text-decoration: none;">
                Unduh Gambar
            </a>
            <button type="button" class="pf-btn-qr" style="flex: 1; justify-content: center; height: 40px; background: #0E7651;" onclick="window.print()">
                Cetak
            </button>
        </div>
    </div>
</div>

<script>
function showEditForm() {
    document.getElementById('profileViewCard').style.display = 'none';
    document.getElementById('profileEditCard').style.display = 'block';
    document.getElementById('btnSwitchToEdit').style.display = 'none';
}

function showProfileView() {
    document.getElementById('profileEditCard').style.display = 'none';
    document.getElementById('profileViewCard').style.display = 'block';
    document.getElementById('btnSwitchToEdit').style.display = 'inline-flex';
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Preview di Form Edit
            const imgPreview = document.getElementById('previewPhotoCircle');
            const initialsPreview = document.getElementById('previewPhotoInitials');
            if (imgPreview) {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
            }
            if (initialsPreview) {
                initialsPreview.style.display = 'none';
            }

            // Preview di Top Avatar Banner
            const topAvatar = document.getElementById('topAvatarImg');
            const topInitials = document.getElementById('topAvatarInitials');
            const topWrapper = document.getElementById('topAvatarWrapper');
            if (topAvatar) {
                topAvatar.src = e.target.result;
            } else if (topWrapper) {
                if (topInitials) topInitials.style.display = 'none';
                topWrapper.innerHTML = `<img src="${e.target.result}" alt="Preview" class="pf-avatar-thumb" id="topAvatarImg">`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openQRModal() {
    document.getElementById('qrModal').style.display = 'flex';
}

function closeQRModal() {
    document.getElementById('qrModal').style.display = 'none';
}

// Buka form edit jika ada error validasi setelah submit
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    showEditForm();
});
@endif
</script>
@endsection
