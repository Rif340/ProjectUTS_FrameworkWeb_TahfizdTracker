@extends('layouts.app')

@section('title', 'Detail Kelas - Tahfizd')

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

{{-- ===== CARD HEADER (Figma Node 595:6242) ===== --}}
<div class="tz-card-head">
    <div class="tz-card-head-left">
        {{-- Exact hugeicons:quran-01 from Figma (Node 595:6257) --}}
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.4173 6.7288L9.5 12.6669M9.5 12.6669L4.74962 16.2298V12.5117M9.5 12.6669L1.5827 6.7288M9.5 12.6669L14.2504 16.2298V12.5117M15.5552 4.30368L15.0334 3.47788L15.0322 3.47594C14.7448 3.02039 14.6008 2.7922 14.4119 2.77164C14.2235 2.75026 14.0121 2.9672 13.5901 3.40108C12.2267 4.80248 10.8634 4.59504 9.5 7.11518C8.13664 4.59504 6.77328 4.80248 5.40992 3.40108C4.98793 2.9672 4.77654 2.75026 4.58811 2.77164C4.39915 2.7922 4.2552 3.02039 3.96782 3.47594L3.9666 3.47788L3.44564 4.30368C3.24375 4.62196 3.1432 4.78189 3.17091 4.94974C3.19783 5.11759 3.34272 5.2324 3.63328 5.46201L8.52776 9.34C8.99567 9.71053 9.23002 9.8958 9.5 9.8958C9.76998 9.8958 10.0043 9.71053 10.4722 9.34L15.3667 5.46201C15.6573 5.2324 15.8022 5.11759 15.8291 4.94974C15.8568 4.78189 15.7563 4.62196 15.5552 4.30368Z" stroke-width="1.5" stroke-linecap="round" stroke="#0E774F"/>
        </svg>
        <span class="tz-head-title">Daftar Kelas</span>
    </div>
    <div class="tz-card-head-right">
        {{-- Tampilkan dropdown (Figma 595:6249) --}}
        <select class="tz-select-show" id="select-per-page-kelas" data-base-url="{{ route('tahfizd.index') }}">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>Tampilkan {{ $n }}</option>
            @endforeach
        </select>
        {{-- Tombol + Tambah (Figma 595:6253) --}}
        @if($isCurrentYearActive)
            <button type="button" class="tz-btn-tambah" onclick="openModal('modal-tambah-kelas')">+ Tambah</button>
        @else
            <button type="button" class="tz-btn-tambah" disabled style="opacity: 0.5; cursor: not-allowed; background: #94a3b8;" title="Tahun akademik belum aktif">+ Tambah</button>
        @endif
    </div>
</div>

{{-- ===== LIST CARD KELAS TAHFIZD (Figma Node 616:949 & 597:6528) ===== --}}
<div class="tz-content">
    @forelse($kelasList as $kelas)
    <div class="tz-card">
        {{-- Bagian Atas Hijau (Figma 597:6530) --}}
        <div class="tz-card-top">
            <div class="tz-card-top-info">
                <p class="tz-card-title">Tahfizd</p>
                <p class="tz-card-kelas">{{ str_starts_with(strtolower($kelas->nama_kelas), 'kelas') ? $kelas->nama_kelas : 'Kelas ' . $kelas->nama_kelas }}</p>
                <p class="tz-card-jadwal">{{ $kelas->hari }}, {{ date('H.i', strtotime($kelas->jam_mulai)) }} – {{ date('H.i', strtotime($kelas->jam_selesai)) }}</p>
            </div>
            {{-- Topi Toga Wisuda / Graduation Cap (Figma 597:6531) --}}
            <div class="tz-card-cap">
                <img src="{{ asset('gambar/gambar_untuk_card_kelas.png') }}" alt="Icon Kelas" class="tz-card-cap-img">
            </div>
        </div>

        {{-- Bagian Bawah Putih (Figma 597:6547) --}}
        <div class="tz-card-bottom">
            {{-- Info Guru Pengampu (Figma 597:6548) --}}
            <div class="tz-teacher">
                @php
                    $fotoGuru = null;
                    if ($kelas->guru && $kelas->guru->foto) {
                        $clean = ltrim($kelas->guru->foto, '/');
                        $fotoGuru = str_starts_with($clean, 'http') ? $clean : asset($clean);
                    }
                @endphp

                @if($fotoGuru)
                    <img src="{{ $fotoGuru }}" alt="Avatar" class="tz-teacher-avatar" onerror="this.onerror=null; this.src='/gambar/logo.png';">
                @else
                    <div class="tz-teacher-initials">
                        {{ $kelas->guru && $kelas->guru->user && $kelas->guru->user->nama ? strtoupper(substr($kelas->guru->user->nama, 0, 2)) : 'GP' }}
                    </div>
                @endif

                <div class="tz-teacher-text">
                    <p class="tz-teacher-label">Guru Pengampu</p>
                    <p class="tz-teacher-name">{{ $kelas->guru->user->nama ?? 'Belum Ditentukan' }}</p>
                </div>
            </div>

            {{-- Action Buttons (Figma 597:6553) --}}
            <div class="tz-card-actions">
                @if($isCurrentYearActive)
                    {{-- Tombol Hapus: Exact Figma Node 599:7284 (at-icons:trash-can) --}}
                    <button type="button" class="tz-btn-icon btn-hapus-kelas" title="Hapus Kelas"
                        data-id="{{ $kelas->id_kelas }}"
                        data-nama="{{ $kelas->nama_kelas }}">
                        <svg width="42" height="33" viewBox="0 0 42 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="42" height="33" rx="10" fill="#0E774F"/>
                            <path d="M25.099 23.11C25.0719 23.3549 24.9554 23.5811 24.7718 23.7453C24.5881 23.9096 24.3504 24.0003 24.104 24H17.897C17.6506 24.0003 17.4129 23.9096 17.2292 23.7453C17.0456 23.5811 16.9291 23.3549 16.902 23.11L16 15H26L25.099 23.11ZM23 10C23.2652 10 23.5196 10.1054 23.7071 10.2929C23.8946 10.4804 24 10.7348 24 11V12H26C26.2652 12 26.5196 12.1054 26.7071 12.2929C26.8946 12.4804 27 12.7348 27 13V14H15V13C15 12.7348 15.1054 12.4804 15.2929 12.2929C15.4804 12.1054 15.7348 12 16 12H18V11C18 10.7348 18.1054 10.4804 18.2929 10.2929C18.4804 10.1054 18.7348 10 19 10H23ZM23 11H19V12H23V11Z" fill="white"/>
                        </svg>
                    </button>

                    {{-- Tombol Edit: Exact Figma Node 599:7286 (fa-regular:edit) --}}
                    <button type="button" class="tz-btn-icon btn-edit-kelas" title="Perbarui Kelas"
                        data-id="{{ $kelas->id_kelas }}"
                        data-nama="{{ $kelas->nama_kelas }}"
                        data-id-guru="{{ $kelas->id_guru ?? '' }}"
                        data-hari="{{ $kelas->hari }}"
                        data-jam-mulai="{{ substr($kelas->jam_mulai, 0, 5) }}"
                        data-jam-selesai="{{ substr($kelas->jam_selesai, 0, 5) }}">
                        <svg width="42" height="33" viewBox="0 0 42 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="42" height="33" rx="10" fill="#0E774F"/>
                            <path d="M25.5719 18.7805L26.5719 17.7805C26.7281 17.6242 27 17.7336 27 17.9586V22.5023C27 23.3305 26.3281 24.0023 25.5 24.0023H14.5C13.6719 24.0023 13 23.3305 13 22.5023V11.5023C13 10.6742 13.6719 10.0023 14.5 10.0023H23.0469C23.2688 10.0023 23.3813 10.2711 23.225 10.4305L22.225 11.4305C22.1781 11.4773 22.1156 11.5023 22.0469 11.5023H14.5V22.5023H25.5V18.9555C25.5 18.8898 25.525 18.8273 25.5719 18.7805ZM30.4656 12.4742L22.2594 20.6805L19.4344 20.993C18.6156 21.0836 17.9188 20.393 18.0094 19.568L18.3219 16.743L26.5281 8.53672C27.2437 7.82109 28.4 7.82109 29.1125 8.53672L30.4625 9.88672C31.1781 10.6023 31.1781 11.7617 30.4656 12.4742ZM27.3781 13.4398L25.5625 11.6242L19.7562 17.4336L19.5281 19.4742L21.5688 19.2461L27.3781 13.4398ZM29.4031 10.9492L28.0531 9.59922C27.925 9.47109 27.7156 9.47109 27.5906 9.59922L26.625 10.5648L28.4406 12.3805L29.4062 11.4148C29.5312 11.2836 29.5312 11.0773 29.4031 10.9492Z" fill="white"/>
                        </svg>
                    </button>
                @else
                    {{-- Tombol Hapus Disabled --}}
                    <button type="button" class="tz-btn-icon" disabled style="opacity: 0.35; cursor: not-allowed;" title="Tahun akademik belum aktif">
                        <svg width="42" height="33" viewBox="0 0 42 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="42" height="33" rx="10" fill="#94a3b8"/>
                            <path d="M25.099 23.11C25.0719 23.3549 24.9554 23.5811 24.7718 23.7453C24.5881 23.9096 24.3504 24.0003 24.104 24H17.897C17.6506 24.0003 17.4129 23.9096 17.2292 23.7453C17.0456 23.5811 16.9291 23.3549 16.902 23.11L16 15H26L25.099 23.11ZM23 10C23.2652 10 23.5196 10.1054 23.7071 10.2929C23.8946 10.4804 24 10.7348 24 11V12H26C26.2652 12 26.5196 12.1054 26.7071 12.2929C26.8946 12.4804 27 12.7348 27 13V14H15V13C15 12.7348 15.1054 12.4804 15.2929 12.2929C15.4804 12.1054 15.7348 12 16 12H18V11C18 10.7348 18.1054 10.4804 18.2929 10.2929C18.4804 10.1054 18.7348 10 19 10H23ZM23 11H19V12H23V11Z" fill="white"/>
                        </svg>
                    </button>

                    {{-- Tombol Edit Disabled --}}
                    <button type="button" class="tz-btn-icon" disabled style="opacity: 0.35; cursor: not-allowed;" title="Tahun akademik belum aktif">
                        <svg width="42" height="33" viewBox="0 0 42 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="42" height="33" rx="10" fill="#94a3b8"/>
                            <path d="M25.5719 18.7805L26.5719 17.7805C26.7281 17.6242 27 17.7336 27 17.9586V22.5023C27 23.3305 26.3281 24.0023 25.5 24.0023H14.5C13.6719 24.0023 13 23.3305 13 22.5023V11.5023C13 10.6742 13.6719 10.0023 14.5 10.0023H23.0469C23.2688 10.0023 23.3813 10.2711 23.225 10.4305L22.225 11.4305C22.1781 11.4773 22.1156 11.5023 22.0469 11.5023H14.5V22.5023H25.5V18.9555C25.5 18.8898 25.525 18.8273 25.5719 18.7805ZM30.4656 12.4742L22.2594 20.6805L19.4344 20.993C18.6156 21.0836 17.9188 20.393 18.0094 19.568L18.3219 16.743L26.5281 8.53672C27.2437 7.82109 28.4 7.82109 29.1125 8.53672L30.4625 9.88672C31.1781 10.6023 31.1781 11.7617 30.4656 12.4742ZM27.3781 13.4398L25.5625 11.6242L19.7562 17.4336L19.5281 19.4742L21.5688 19.2461L27.3781 13.4398ZM29.4031 10.9492L28.0531 9.59922C27.925 9.47109 27.7156 9.47109 27.5906 9.59922L26.625 10.5648L28.4406 12.3805L29.4062 11.4148C29.5312 11.2836 29.5312 11.0773 29.4031 10.9492Z" fill="white"/>
                        </svg>
                    </button>
                @endif

                {{-- Tombol Pergi: Exact Figma Node 599:7278 (Masuk ke Kelas / Progres Kelas Figma 603:2185) --}}
                <a href="{{ route('tahfizd.show', $kelas->id_kelas) }}" class="tz-btn-pergi" title="Masuk ke Kelas">
                    Pergi
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="tz-empty">
        <p>Belum ada data kelas tahfizd.</p>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($kelasList->hasPages())
    <div class="tz-pagination">
        @if($kelasList->onFirstPage())
            <span>&laquo;</span>
        @else
            <a href="{{ $kelasList->previousPageUrl() }}">&laquo;</a>
        @endif

        @foreach($kelasList->getUrlRange(1, $kelasList->lastPage()) as $page => $url)
            @if($page == $kelasList->currentPage())
                <span class="current">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($kelasList->hasMorePages())
            <a href="{{ $kelasList->nextPageUrl() }}">&raquo;</a>
        @else
            <span>&raquo;</span>
        @endif
    </div>
    @endif
</div>

{{-- ===================================================
     MODAL TAMBAH KELAS TAHFIZD (Figma Node 597:6618)
     =================================================== --}}
<div class="tz-overlay" id="modal-tambah-kelas">
    <div class="tz-form-modal">

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-fm-logo">
        <h2 class="tz-fm-title">Form Tambah Kelas</h2>
        <p class="tz-fm-subtitle">Isi Dengan Lengkap, dan pastikan tidak ada yang salah</p>

        <form method="POST" action="{{ route('tahfizd.store') }}">
            @csrf

            {{-- 2-column form fields --}}
            <div class="tz-fm-cols">
                {{-- LEFT COLUMN --}}
                <div class="tz-fm-col">
                    {{-- Nama Kelas --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Nama Kelas <span>*</span></label>
                        <input type="text" name="nama_kelas" class="tz-fm-input"
                               placeholder="Tahfizd" required maxlength="100">
                    </div>

                    {{-- Kelas (suffix/level) --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Kelas <span>*</span></label>
                        <select name="kelas_level" class="tz-fm-select">
                            <option value="">Pilih Kelas</option>
                            @foreach(['1A','1B','2A','2B','3A','3B','4A','4B','5A','5B','6A','6B'] as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hari Belajar --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Hari Belajar <span>*</span></label>
                        <select name="hari" class="tz-fm-select" required>
                            <option value="">Pilih Hari</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tahun Akademik --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Tahun Akademik <span>*</span></label>
                        <select name="id_tahun_akademik" class="tz-fm-select" required>
                            <option value="">Pilih Tahun Akademik</option>
                            @foreach($tahunAkademikList as $ta)
                                <option value="{{ $ta->id_tahun_akademik }}"
                                    {{ $tahunAktif && $tahunAktif->id_tahun_akademik == $ta->id_tahun_akademik ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun }} – {{ $ta->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- RIGHT COLUMN (Figma 597:6825) --}}
                <div class="tz-fm-col">
                    <input type="hidden" name="id_guru" value="{{ Auth::user()->guru->id_guru ?? ($guruList->first()->id_guru ?? 1) }}">

                    {{-- Jam Mulai --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Jam Mulai <span>*</span></label>
                        <div class="tz-fm-time-wrap">
                            <input type="time" name="jam_mulai" class="tz-fm-input tz-fm-time"
                                   value="07:00" required>
                        </div>
                    </div>

                    {{-- Jam Selesai --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Jam Selesai <span>*</span></label>
                        <div class="tz-fm-time-wrap">
                            <input type="time" name="jam_selesai" class="tz-fm-input tz-fm-time"
                                   value="10:00" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Full-width Batal & Simpan --}}
            <div class="tz-fm-actions">
                <button type="button" class="tz-fm-btn-batal"
                        onclick="closeModal('modal-tambah-kelas')">Batal</button>
                <button type="submit" class="tz-fm-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL EDIT KELAS TAHFIZD
     =================================================== --}}
<div class="tz-overlay" id="modal-edit-kelas">
    <div class="tz-form-modal">
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-fm-logo">
        <h2 class="tz-fm-title">Form Perbarui Kelas</h2>
        <p class="tz-fm-subtitle">Isi Dengan Lengkap, dan pastikan tidak ada yang salah</p>

        <form method="POST" id="form-edit-kelas" action="">
            @csrf
            {{-- 2-column form fields --}}
            <div class="tz-fm-cols">
                {{-- LEFT COLUMN --}}
                <div class="tz-fm-col">
                    {{-- Nama Kelas (Dropdown) --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Nama Kelas <span>*</span></label>
                        <select name="nama_kelas" id="edit-nama-kelas" class="tz-fm-select" required>
                            <option value="">Pilih Kelas</option>
                            @foreach(['1A','1B','2A','2B','3A','3B','4A','4B','5A','5B','6A','6B'] as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Guru Pengampu --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Guru Pengampu <span>*</span></label>
                        <select name="id_guru" id="edit-id-guru" class="tz-fm-select" required>
                            <option value="">Pilih Guru</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id_guru }}">{{ $guru->user->nama ?? 'Guru' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hari Belajar --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Hari Belajar <span>*</span></label>
                        <select name="hari" id="edit-hari" class="tz-fm-select" required>
                            <option value="">Pilih Hari</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="tz-fm-col">
                    {{-- Jam Mulai --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Jam Mulai <span>*</span></label>
                        <div class="tz-fm-time-wrap">
                            <input type="time" name="jam_mulai" id="edit-jam-mulai" class="tz-fm-input tz-fm-time" required>
                        </div>
                    </div>

                    {{-- Jam Selesai --}}
                    <div class="tz-fm-field">
                        <label class="tz-fm-label">Jam Selesai <span>*</span></label>
                        <div class="tz-fm-time-wrap">
                            <input type="time" name="jam_selesai" id="edit-jam-selesai" class="tz-fm-input tz-fm-time" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Full-width Batal & Simpan --}}
            <div class="tz-fm-actions">
                <button type="button" class="tz-fm-btn-batal" onclick="closeModal('modal-edit-kelas')">Batal</button>
                <button type="submit" class="tz-fm-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL HAPUS KELAS TAHFIZD (Figma Node 599:7804)
     =================================================== --}}
<div class="tz-del-overlay" id="modal-hapus-kelas">
    <div class="tz-del-modal">
        <button class="tz-del-close" onclick="closeModal('modal-hapus-kelas')" type="button" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="tz-del-logo">

        <div style="text-align: center; width: 100%;">
            <h2 class="tz-del-title">Menghapus</h2>
            <p class="tz-del-subtitle" id="delete-kelas-msg">Apakah Anda Yakin Ingin Menghapusnya?</p>
        </div>

        <div class="tz-del-actions">
            <button class="tz-del-btn-batal" type="button" onclick="closeModal('modal-hapus-kelas')">Batal</button>
            <form id="form-hapus-kelas" method="POST" action="" class="tz-del-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="tz-del-btn-setujui">Setujui</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function openEditModal(id, nama, idGuru, hari, jamMulai, jamSelesai) {
        var selNama = document.getElementById('edit-nama-kelas');
        if (selNama) {
            var cleanNama = String(nama).replace(/^(Tahfi[zd]\s*|Kelas\s*)/i, '').trim();
            var found = false;
            for (var i = 0; i < selNama.options.length; i++) {
                if (selNama.options[i].value == nama || selNama.options[i].value == cleanNama) {
                    selNama.options[i].selected = true;
                    found = true;
                    break;
                }
            }
            if (!found && nama) {
                var opt = new Option(nama, nama, true, true);
                selNama.add(opt);
            }
        }

        var selHari = document.getElementById('edit-hari');
        if (selHari) selHari.value = hari;

        var selJamMulai = document.getElementById('edit-jam-mulai');
        if (selJamMulai) selJamMulai.value = jamMulai;

        var selJamSelesai = document.getElementById('edit-jam-selesai');
        if (selJamSelesai) selJamSelesai.value = jamSelesai;

        var selGuru = document.getElementById('edit-id-guru');
        if (selGuru) {
            for (var i = 0; i < selGuru.options.length; i++) {
                selGuru.options[i].selected = (selGuru.options[i].value == idGuru);
            }
        }

        document.getElementById('form-edit-kelas').action = '/tahfizd/' + id + '/update';
        openModal('modal-edit-kelas');
    }

    function openDeleteModal(id, nama) {
        document.getElementById('form-hapus-kelas').action = '/tahfizd/' + id;
        openModal('modal-hapus-kelas');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var perPageSelect = document.getElementById('select-per-page-kelas');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                var url = this.getAttribute('data-base-url') || '/tahfizd';
                location.href = url + '?per_page=' + this.value;
            });
        }

        document.addEventListener('click', function(e) {
            var hapusBtn = e.target.closest('.btn-hapus-kelas');
            if (hapusBtn) {
                var d = hapusBtn.dataset;
                openDeleteModal(d.id, d.nama);
                return;
            }

            var editBtn = e.target.closest('.btn-edit-kelas');
            if (editBtn) {
                var d = editBtn.dataset;
                openEditModal(d.id, d.nama, d.idGuru, d.hari, d.jamMulai, d.jamSelesai);
                return;
            }
        });
    });
</script>
@endsection
