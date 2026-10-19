@extends('layouts.app')

@section('title', 'Data Murid')
@section('page_title', 'Data Murid')

@push('styles')
<link rel="stylesheet" href="/css/murid.css">
@endpush

@section('content')

{{-- ===== CARD HEADER (Figma Node 393:4965) ===== --}}
<div class="mu-card-head">
    <div class="mu-card-head-left">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#0E774F" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.94 8.13 22 12 22C15.87 22 19 19.94 19 17.18V13.18L12 17L5 13.18Z"/>
        </svg>
        <span class="mu-head-title">Daftar Murid</span>
    </div>
    <div class="mu-card-head-right">
        {{-- Tampilkan dropdown (Figma 393:4976) --}}
        <select class="mu-select-show" onchange="location.href='{{ route('siswa.index') }}?per_page='+this.value+'&search={{ request('search') }}&nisn={{ request('nisn') }}&kelas={{ request('kelas') }}'">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>Tampilkan {{ $n }}</option>
            @endforeach
        </select>
        {{-- Tombol Tambah (Figma 393:4975) --}}
        @if($isCurrentYearActive)
            <button type="button" class="mu-btn-tambah" onclick="openTambahModal()">Tambah</button>
        @else
            <button type="button" class="mu-btn-tambah" disabled style="opacity: 0.5; cursor: not-allowed; background: #94a3b8;" title="Tahun akademik belum aktif">Tambah</button>
        @endif
    </div>
</div>

{{-- ===== FILTER BAR (Figma Node 393:4993, 393:5005, 402:7707) ===== --}}
<form method="GET" action="{{ route('siswa.index') }}" class="mu-filter-bar" id="filterMuridForm">
    <div class="mu-filter-group">
        <label class="mu-filter-label">Nama Siswa</label>
        <input type="text" name="search" class="mu-filter-input w-nama" placeholder="Nama Siswa" value="{{ request('search') }}" oninput="this.value=this.value.replace(/[^a-zA-Z\s.,'\-]/g,'')">
    </div>
    <div class="mu-filter-group">
        <label class="mu-filter-label">NISN</label>
        <input type="text" name="nisn" class="mu-filter-input w-nisn" placeholder="NISN" value="{{ request('nisn') }}" oninput="this.value=this.value.replace(/\D/g,'')" maxlength="18">
    </div>
    <div class="mu-filter-group">
        <label class="mu-filter-label">Kelas</label>
        <select name="kelas" class="mu-filter-select w-kelas" onchange="document.getElementById('filterMuridForm').submit()">
            <option value="">Semua Kelas</option>
            <option value="belum" {{ request('kelas') === 'belum' ? 'selected' : '' }}>Belum Ada Kelas</option>
            @foreach($kelasList as $kls)
                <option value="{{ $kls->id_kelas }}" {{ request('kelas') == $kls->id_kelas ? 'selected' : '' }}>{{ $kls->nama_kelas }}</option>
            @endforeach
        </select>
    </div>
    <input type="hidden" name="per_page" value="{{ $perPage }}">
    <button type="submit" style="display:none;"></button>
</form>

{{-- ===== TABLE (Figma Node 393:6663) ===== --}}
<div class="mu-content">
    <div class="mu-table-wrap">
        <table class="mu-table">
            <thead>
                <tr>
                    <th style="width: 6.8%; text-align: center;">No</th>
                    <th style="width: 31.8%; text-align: center;">Nama Lengkap</th>
                    <th style="width: 18.8%; text-align: center;">NISN</th>
                    <th style="width: 17.4%; text-align: center;">Kelas</th>
                    <th style="width: 25.2%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($muridList as $i => $murid)
                <tr>
                    <td class="mu-td-no">{{ $muridList->firstItem() + $i }}</td>
                    <td class="mu-td-nama">{{ $murid->user->nama ?? '-' }}</td>
                    <td class="mu-td-nisn">{{ $murid->nisn ?? '-' }}</td>
                    <td class="mu-td-kelas">{{ $murid->kelas->nama_kelas ?? '-' }}</td>
                    <td class="mu-td-aksi">
                        <div class="mu-table-actions">
                            {{-- Hapus (Red Trash Can) --}}
                            @if($isCurrentYearActive)
                                <button type="button" class="mu-icon-btn" title="Hapus"
                                    onclick="openDeleteModal({{ $murid->id_murid }}, '{{ addslashes($murid->user->nama ?? '') }}')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#FF0000" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                </button>
                            @else
                                <button type="button" class="mu-icon-btn" disabled style="opacity: 0.35; cursor: not-allowed;" title="Tahun akademik belum aktif">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#94a3b8" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                </button>
                            @endif
                            {{-- Edit (Green Pencil) --}}
                            @if($isCurrentYearActive)
                                <button type="button" class="mu-icon-btn" title="Edit"
                                    onclick="openEditModal(
                                        {{ $murid->id_murid }},
                                        '{{ addslashes($murid->user->nama ?? '') }}',
                                        '{{ addslashes($murid->user->email ?? '') }}',
                                        '{{ $murid->nisn ?? '' }}',
                                        '{{ $murid->no_hp ?? '' }}',
                                        {{ $murid->id_kelas ?? 'null' }},
                                        '{{ $murid->foto_url ?? '' }}'
                                    )">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 20V13" stroke="#0E7651" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10217 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="#0E7651" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            @else
                                <button type="button" class="mu-icon-btn" disabled style="opacity: 0.35; cursor: not-allowed;" title="Tahun akademik belum aktif">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 20V13" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10217 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            @endif
                            {{-- Lihat Progress (Blue Eye) --}}
                            <a href="{{ route('siswa.show', $murid->id_murid) }}" class="mu-icon-btn" title="Lihat Progress Hafalan">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="#0000FF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="3" stroke="#0000FF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="mu-empty">Belum ada data murid.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($muridList->hasPages())
    <div class="mu-pagination">
        @if($muridList->onFirstPage())
            <span>&laquo;</span>
        @else
            <a href="{{ $muridList->previousPageUrl() }}">&laquo;</a>
        @endif

        @foreach($muridList->getUrlRange(1, $muridList->lastPage()) as $page => $url)
            @if($page == $muridList->currentPage())
                <span class="current">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($muridList->hasMorePages())
            <a href="{{ $muridList->nextPageUrl() }}">&raquo;</a>
        @else
            <span>&raquo;</span>
        @endif
    </div>
    @endif
</div>

{{-- ===================================================
     MODAL TAMBAH MURID (Figma Node 408:2774)
     =================================================== --}}
<div class="mu-modal-overlay" id="modal-tambah">
    <div class="mu-modal">
        <button class="mu-modal-close" onclick="closeModal('modal-tambah')" type="button" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="mu-modal-logo">
        <h2 class="mu-modal-title">Form Tambah Murid</h2>
        <p class="mu-modal-subtitle">Isi Dengan Lengkap, dan pastikan tidak ada yang salah</p>

        <form method="POST" action="{{ route('siswa.store') }}" enctype="multipart/form-data" id="form-tambah-murid">
            @csrf
            <div class="mu-form-cols">
                {{-- Kolom Kiri --}}
                <div class="mu-form-col">
                    {{-- Nama Siswa (Dengan Live Search Murid dari Pengguna yang dibuat Admin) --}}
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Nama <span>*</span></label>
                        <div class="mu-search-murid-wrap">
                            <input type="text" name="nama" id="tambah-nama" class="mu-form-input" placeholder="Belinda Blanche" required
                                autocomplete="off" oninput="onSearchMuridInput(this.value)" onfocus="onSearchMuridInput(this.value)" maxlength="100">
                            <button type="button" class="mu-search-clear-btn" id="tambah-clear-btn" onclick="clearSelectedMurid()" title="Hapus Pilihan">&times;</button>
                            <input type="hidden" name="id_user" id="tambah-id-user">
                            <input type="hidden" name="id_murid" id="tambah-id-murid">

                            {{-- Dropdown Hasil Pencarian --}}
                            <div id="murid-search-dropdown" class="mu-murid-search-dropdown" style="display: none;"></div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Email <span>*</span></label>
                        <input type="email" name="email" id="tambah-email" class="mu-form-input" placeholder="BelindaBlanche@gmail.com" required maxlength="100">
                    </div>

                    {{-- Password --}}
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Password <span>*</span></label>
                        <div class="mu-password-wrap">
                            <input type="password" name="password" id="pass-tambah" class="mu-form-input" placeholder="••••••••" required minlength="6">
                            <img src="/icon/icon_eye_off.png" class="mu-toggle-pwd" id="eye-tambah" onclick="toggleEye('pass-tambah', 'eye-tambah')" alt="Lihat">
                        </div>
                    </div>

                    {{-- NISN --}}
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">NISN <span>*</span></label>
                        <input type="text" name="nisn" id="tambah-nisn" class="mu-form-input" placeholder="0000512345" required
                            oninput="this.value=this.value.replace(/\D/g,'')" maxlength="18">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="mu-form-col">
                    {{-- Kelas --}}
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Kelas <span>*</span></label>
                        <select name="id_kelas" id="tambah-kelas" class="mu-form-select" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id_kelas }}">{{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mu-modal-actions">
                <button type="button" class="mu-btn-batal" onclick="closeModal('modal-tambah')">Batal</button>
                <button type="submit" class="mu-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL EDIT MURID (Figma Node 408:3363)
     =================================================== --}}
<div class="mu-modal-overlay" id="modal-edit">
    <div class="mu-modal">
        <button class="mu-modal-close" onclick="closeModal('modal-edit')" type="button" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>

        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="mu-modal-logo">
        <h2 class="mu-modal-title">Form Perbarui Murid</h2>
        <p class="mu-modal-subtitle">Isi Dengan Lengkap, dan pastikan tidak ada yang salah</p>

        <form method="POST" id="form-edit" action="" enctype="multipart/form-data">
            @csrf
            <div class="mu-form-cols">
                {{-- Kolom Kiri --}}
                <div class="mu-form-col">
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Nama <span>*</span></label>
                        <input type="text" name="nama" id="edit-nama" class="mu-form-input" required
                            oninput="this.value=this.value.replace(/[^a-zA-Z\s.,'\-]/g,'')" maxlength="100">
                    </div>
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Email <span>*</span></label>
                        <input type="email" name="email" id="edit-email" class="mu-form-input" required maxlength="100">
                    </div>
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Password</label>
                        <div class="mu-password-wrap">
                            <input type="password" name="password" id="pass-edit" class="mu-form-input" placeholder="Kosongkan jika tetap" minlength="6">
                            <img src="/icon/icon_eye_off.png" class="mu-toggle-pwd" id="eye-edit" onclick="toggleEye('pass-edit','eye-edit')" alt="Lihat">
                        </div>
                    </div>
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">NISN <span>*</span></label>
                        <input type="text" name="nisn" id="edit-nisn" class="mu-form-input" required
                            oninput="this.value=this.value.replace(/\D/g,'')" maxlength="18">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="mu-form-col">
                    <div class="mu-form-row">
                        <label class="mu-form-row-label">Kelas <span>*</span></label>
                        <select name="id_kelas" id="edit-kelas" class="mu-form-select" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id_kelas }}">{{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mu-modal-actions">
                <button type="button" class="mu-btn-batal" onclick="closeModal('modal-edit')">Batal</button>
                <button type="submit" class="mu-btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL KONFIRMASI HAPUS (Figma Node 408:3611)
     =================================================== --}}
<div class="mu-delete-overlay" id="modal-hapus">
    <div class="mu-delete-box">
        <button class="mu-delete-close" onclick="closeModal('modal-hapus')" type="button" title="Tutup">
            <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
        </button>
        <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="mu-delete-logo">
        <h2 class="mu-delete-title">Menghapus</h2>
        <p class="mu-delete-msg" id="delete-msg">Apakah Anda Yakin Ingin Menghapusnya?</p>
        <div class="mu-delete-btns">
            <button class="mu-delete-btn-batal" type="button" onclick="closeModal('modal-hapus')">Batal</button>
            <form id="form-hapus" method="POST" action="" style="flex:1; display:flex;">
                @csrf
                @method('DELETE')
                <button type="submit" class="mu-delete-btn-setujui">Setujui</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Data murid dari pengguna yang dibuat admin
    const availableMurids = @json($availableMuridList ?? []);

    // Open / Close modal
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // Live search engine untuk memilih murid atau input baru
    function onSearchMuridInput(val) {
        const dropdown = document.getElementById('murid-search-dropdown');
        const clearBtn = document.getElementById('tambah-clear-btn');
        const query = (val || '').trim().toLowerCase();

        // Jika user mengetik manual, lepaskan binding id_user kecuali dipilih dari list
        const idUserInput = document.getElementById('tambah-id-user');
        if (!idUserInput.value) {
            const passInput = document.getElementById('pass-tambah');
            passInput.required = true;
            passInput.placeholder = '••••••••';
        }

        if (query.length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }

        const filtered = availableMurids.filter(m => {
            if (!query) return true;
            const nama = (m.nama || '').toLowerCase();
            const nisn = (m.nisn || '').toLowerCase();
            const email = (m.email || '').toLowerCase();
            return nama.includes(query) || nisn.includes(query) || email.includes(query);
        });

        if (filtered.length === 0) {
            dropdown.innerHTML = `
                <div class="mu-murid-no-result">
                    <div>Murid <strong>"${escapeHtml(val)}"</strong> belum ada di Pengguna.</div>
                    <div style="font-size: 11px; color: #0E7651; margin-top: 4px;">
                        Lengkapi Email, Password, NISN & Kelas untuk <strong>menambahkan murid baru langsung</strong>.
                    </div>
                </div>
            `;
            dropdown.style.display = 'block';
            return;
        }

        let html = `
            <div style="padding: 6px 14px 4px; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">
                Pilih dari Pengguna (Admin):
            </div>
        `;
        filtered.slice(0, 10).forEach(m => {
            const badge = m.nama_kelas
                ? `<span class="mu-murid-item-badge">Kelas: ${escapeHtml(m.nama_kelas)}</span>`
                : `<span class="mu-murid-item-badge empty">Belum ada kelas</span>`;

            html += `
                <div class="mu-murid-item" onclick="selectMuridUser(${m.id_user})">
                    <div class="mu-murid-item-name">${escapeHtml(m.nama)}</div>
                    <div class="mu-murid-item-meta">
                        <span>NISN: ${escapeHtml(m.nisn || '-')}</span>
                        <span>•</span>
                        <span>${escapeHtml(m.email)}</span>
                        ${badge}
                    </div>
                </div>
            `;
        });

        html += `
            <div style="padding: 8px 14px; border-top: 1px dashed #E5E7EB; background: #F9FAFB; cursor: pointer; text-align: center;" onclick="useAsNewMurid()">
                <span style="font-size: 12px; font-weight: 500; color: #0E7651;">
                    + Input sebagai Murid Baru Langsung
                </span>
            </div>
        `;

        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
    }

    // Gunakan sebagai murid baru (bukan ambil dari pengguna admin)
    function useAsNewMurid() {
        document.getElementById('tambah-id-user').value = '';
        document.getElementById('tambah-id-murid').value = '';
        const passInput = document.getElementById('pass-tambah');
        passInput.required = true;
        passInput.placeholder = '••••••••';
        document.getElementById('murid-search-dropdown').style.display = 'none';
        document.getElementById('tambah-email').focus();
    }

    // Pilih murid dari hasil pencarian
    function selectMuridUser(idUser) {
        const m = availableMurids.find(x => x.id_user == idUser);
        if (!m) return;

        document.getElementById('tambah-nama').value = m.nama;
        document.getElementById('tambah-id-user').value = m.id_user;
        document.getElementById('tambah-id-murid').value = m.id_murid || '';
        document.getElementById('tambah-email').value = m.email;
        document.getElementById('tambah-nisn').value = m.nisn || '';

        // Preselect kelas jika sudah terdaftar
        if (m.id_kelas) {
            document.getElementById('tambah-kelas').value = m.id_kelas;
        }

        // Password opsional jika akun sudah ada
        const passInput = document.getElementById('pass-tambah');
        passInput.required = false;
        passInput.placeholder = 'Kosongkan jika tetap (akun sudah ada)';

        document.getElementById('tambah-clear-btn').style.display = 'block';
        document.getElementById('murid-search-dropdown').style.display = 'none';
    }

    // Reset pilihan murid
    function clearSelectedMurid() {
        document.getElementById('tambah-nama').value = '';
        document.getElementById('tambah-id-user').value = '';
        document.getElementById('tambah-id-murid').value = '';
        document.getElementById('tambah-email').value = '';
        document.getElementById('tambah-nisn').value = '';
        document.getElementById('tambah-kelas').value = '';

        const passInput = document.getElementById('pass-tambah');
        passInput.required = true;
        passInput.placeholder = '••••••••';

        document.getElementById('tambah-clear-btn').style.display = 'none';
        document.getElementById('murid-search-dropdown').style.display = 'none';
        document.getElementById('tambah-nama').focus();
    }

    function openTambahModal() {
        clearSelectedMurid();
        openModal('modal-tambah');
    }

    // Tutup dropdown pencarian ketika klik di luar
    document.addEventListener('click', function(e) {
        const wrap = document.querySelector('.mu-search-murid-wrap');
        const dropdown = document.getElementById('murid-search-dropdown');
        if (dropdown && wrap && !wrap.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Open Edit modal
    function openEditModal(id, nama, email, nisn, nohp, kelasId, fotoUrl) {
        document.getElementById('edit-nama').value  = nama;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-nisn').value  = nisn;
        document.getElementById('pass-edit').value  = '';

        // Kelas select
        var sel = document.getElementById('edit-kelas');
        for (var i = 0; i < sel.options.length; i++) {
            sel.options[i].selected = (sel.options[i].value == kelasId);
        }

        // Form action
        document.getElementById('form-edit').action = '/siswa/' + id + '/update';

        openModal('modal-edit');
    }

    // Open Delete modal (Figma Node 408:3611)
    function openDeleteModal(id, nama) {
        document.getElementById('delete-msg').textContent = 'Apakah Anda Yakin Ingin Menghapus Murid "' + nama + '"?';
        document.getElementById('form-hapus').action = '/siswa/' + id;
        openModal('modal-hapus');
    }

    // Toggle password visibility
    function toggleEye(inputId, imgId) {
        var input = document.getElementById(inputId);
        var img   = document.getElementById(imgId);
        if (input.type === 'password') {
            input.type = 'text';
            img.src = '/icon/icon_eye_on.png';
        } else {
            input.type = 'password';
            img.src = '/icon/icon_eye_off.png';
        }
    }
</script>
@endsection
