@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Kelola Pengguna')

@push('styles')
<link rel="stylesheet" href="/css/kelola-pengguna.css">
@endpush

@section('content')

{{-- Flash Message --}}
@if(session('success'))
    <div class="pg-flash pg-flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="pg-flash pg-flash-error">{{ session('error') }}</div>
@endif

{{-- Card Header --}}
<div class="pg-card-head">
    <div class="pg-card-head-left">
        {{-- Icon hugeicons:user-round-cog (inline SVG) --}}
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#0e774f" stroke-width="1.5"/>
            <path d="M3.41003 22C3.41003 18.13 7.26003 15 12 15" stroke="#0e774f" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18.5 22C20.433 22 22 20.433 22 18.5C22 16.567 20.433 15 18.5 15C16.567 15 15 16.567 15 18.5C15 20.433 16.567 22 18.5 22Z" stroke="#0e774f" stroke-width="1.5"/>
            <path d="M22 22L21 21" stroke="#0e774f" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="pg-head-title">Detail Pengguna</span>
    </div>
    <div class="pg-card-head-right">
        <select class="pg-select-show" id="perPage"
                onchange="location.href='{{ route('pengguna.index') }}?per_page='+this.value">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>
                    Tampilkan {{ $n }}
                </option>
            @endforeach
        </select>
        <a href="{{ route('pengguna.create') }}" class="pg-btn-tambah">+ Tambah</a>
    </div>
</div>

{{-- Table --}}
<div class="pg-content">
    <div class="pg-table-wrap">
        <table class="pg-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>NIP/NISN</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penggunaList as $i => $pg)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pg->nama }}</td>
                    <td>{{ $pg->role }}</td>
                    <td>{{ $pg->email }}</td>
                    <td>
                        @if($pg->role === 'Admin' && $pg->admin)
                            {{ $pg->admin->nip ?? '-' }}
                        @elseif($pg->role === 'Guru' && $pg->guru)
                            {{ $pg->guru->nip ?? '-' }}
                        @elseif($pg->role === 'Murid' && $pg->murid)
                            {{ $pg->murid->nisn ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="pg-aksi-cell">
                            {{-- Hapus --}}
                            <button type="button" class="pg-aksi-btn" title="Hapus"
                                onclick="openDeleteModal('{{ route('pengguna.destroy', $pg->id_user) }}', '{{ addslashes($pg->nama) }}')">
                                <svg width="13" height="15" viewBox="42.5 10.5 13 15" fill="none">
                                    <path d="M53.099 24.11C53.0719 24.3549 52.9554 24.5811 52.7718 24.7453C52.5881 24.9096 52.3504 25.0003 52.104 25H45.897C45.6506 25.0003 45.4129 24.9096 45.2292 24.7453C45.0456 24.5811 44.9291 24.3549 44.902 24.11L44 16H54L53.099 24.11ZM51 11C51.2652 11 51.5196 11.1054 51.7071 11.2929C51.8946 11.4804 52 11.7348 52 12V13H54C54.2652 13 54.5196 13.1054 54.7071 13.2929C54.8946 13.4804 55 13.7348 55 14V15H43V14C43 13.7348 43.1054 13.4804 43.2929 13.2929C43.4804 13.1054 43.7348 13 44 13H46V12C46 11.7348 46.1054 11.4804 46.2929 11.2929C46.4804 11.1054 46.7348 11 47 11H51ZM51 12H47V13H51V12Z" fill="#FF0000"/>
                                </svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('pengguna.edit', $pg->id_user) }}" class="pg-aksi-btn" title="Edit">
                                <svg width="18" height="16" viewBox="40 10 18 16" fill="none">
                                    <path d="M52.5719 20.7805L53.5719 19.7805C53.7281 19.6242 54 19.7336 54 19.9586V24.5023C54 25.3305 53.3281 26.0023 52.5 26.0023H41.5C40.6719 26.0023 40 25.3305 40 24.5023V13.5023C40 12.6742 40.6719 12.0023 41.5 12.0023H50.0469C50.2688 12.0023 50.3813 12.2711 50.225 12.4305L49.225 13.4305C49.1781 13.4773 49.1156 13.5023 49.0469 13.5023H41.5V24.5023H52.5V20.9555C52.5 20.8898 52.525 20.8273 52.5719 20.7805ZM57.4656 14.4742L49.2594 22.6805L46.4344 22.993C45.6156 23.0836 44.9188 22.393 45.0094 21.568L45.3219 18.743L53.5281 10.5367C54.2437 9.82109 55.4 9.82109 56.1125 10.5367L57.4625 11.8867C58.1781 12.6023 58.1781 13.7617 57.4656 14.4742ZM54.3781 15.4398L52.5625 13.6242L46.7562 19.4336L46.5281 21.4742L48.5688 21.2461L54.3781 15.4398ZM56.4031 12.9492L55.0531 11.5992C54.925 11.4711 54.7156 11.4711 54.5906 11.5992L53.625 12.5648L55.4406 14.3805L56.4062 13.4148C56.5312 13.2836 56.5312 13.0773 56.4031 12.9492Z" fill="#0E7651"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="pg-empty">Belum ada data pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Konfirmasi Hapus (sesuai Figma 665:28738 — sama pola dengan tahun-akademik) --}}
<div id="deleteModalOverlay" class="pg-overlay" style="display:none;">
    <div class="pg-delete-modal">
        {{-- Close Button --}}
        <button type="button" class="pg-delete-close" onclick="closeDeleteModal()" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        {{-- Logo --}}
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="pg-delete-logo">

        {{-- Title & Subtitle --}}
        <div style="text-align:center;">
            <h2 class="pg-delete-title">Hapus Data Pengguna?</h2>
            <p class="pg-delete-subtitle" id="deleteNamaText">Apakah Anda yakin ingin menghapus pengguna ini?</p>
        </div>

        {{-- Buttons --}}
        <form id="deleteForm" method="POST" style="width:100%;">
            @csrf
            @method('DELETE')
            <div class="pg-delete-actions">
                <button type="button" class="pg-delete-btn-batal" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="pg-delete-btn-hapus">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal(actionUrl, nama) {
    document.getElementById('deleteNamaText').textContent = 'Yakin ingin menghapus pengguna "' + nama + '"?';
    document.getElementById('deleteForm').action = actionUrl;
    document.getElementById('deleteModalOverlay').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModalOverlay').style.display = 'none';
}
</script>
@endsection
