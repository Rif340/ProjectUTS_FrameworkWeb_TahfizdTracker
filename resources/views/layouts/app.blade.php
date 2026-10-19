<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Tahfizd Tracker</title>

    <link rel="stylesheet" href="/css/app-layout.css">
    @yield('styles')
    @stack('styles')
</head>
<body>

    <!-- ==================== SIDEBAR (Figma Node 618:960) ==================== -->
    <aside class="sidebar">
        <!-- Sidebar Header (Figma Node 618:961) -->
        <div class="sidebar-header">
            <img src="/gambar/logo.png" alt="Logo" class="sidebar-logo">
            <div class="sidebar-header-text">
                <span class="sidebar-title">Tahfizd Tracker</span>
                <span class="sidebar-subtitle">At-Taubah Karawang</span>
            </div>
        </div>

        <!-- Sidebar Navigation Menu Items (Figma Node 618:1591 & 393:4769) -->
        <ul class="sidebar-menu">
            @php 
                $role = strtolower(Auth::user()->role); 
                $isAbsensiActive = request()->routeIs('absensi.*');
                $isTahfizdActive = request()->routeIs('tahfizd.*');
                $isSiswaActive   = request()->routeIs('siswa.*');
            @endphp

            @if($role === 'guru')
                {{-- ==================== SIDEBAR GURU (Figma Node 393:4769, 393:4525, 393:6988) ==================== --}}
                
                <!-- 1. Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_dashboard.png" alt="Dashboard">
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 2. Absensi (Submenu: Absensi Kehadiran, Riwayat Absensi) -->
                <li>
                    <button type="button" class="menu-item {{ $isAbsensiActive ? 'active' : '' }}" id="btnToggleAbsensi" onclick="toggleAbsensiMenu()">
                        <div class="menu-icon">
                            <img src="/icon/icon_absensi.png" alt="Absensi">
                        </div>
                        <span>Absensi</span>
                        <div class="menu-arrow">
                            <img src="{{ $isAbsensiActive ? '/icon/icon_arrow_terbuka_sidebar.png' : '/icon/icon_arrow_tertutup_sidebar.png' }}" id="arrowAbsensiIcon" alt="Arrow">
                        </div>
                    </button>

                    <div class="submenu-container {{ $isAbsensiActive ? 'open' : '' }}" id="submenuAbsensi">
                        <a href="{{ route('absensi.kehadiran') }}" class="submenu-item {{ request()->routeIs('absensi.kehadiran') ? 'active' : '' }}">
                            <span>Absensi Kehadiran</span>
                        </a>
                        <a href="{{ route('absensi.detail') }}" class="submenu-item {{ request()->routeIs('absensi.detail') ? 'active' : '' }}">
                            <span>Riwayat Absensi</span>
                        </a>
                    </div>
                </li>

                <!-- 3. Siswa (Figma Node 393:4561) -->
                <li>
                    <a href="{{ route('siswa.index') }}" class="menu-item {{ $isSiswaActive ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_siswa.svg" alt="Siswa" style="width: 19px; height: 19px;">
                        </div>
                        <span>Siswa</span>
                    </a>
                </li>

                <!-- 4. Tahfizd (Accordion Submenu: Daftar Tahfizd, Target Hafalan) (Figma Node 393:6988) -->
                <li>
                    <button type="button" class="menu-item {{ $isTahfizdActive ? 'active' : '' }}" id="btnToggleTahfizd" onclick="toggleTahfizdMenu()">
                        <div class="menu-icon">
                            <img src="/icon/icon_tahfizd.svg" alt="Tahfizd" style="width: 19px; height: 19px;">
                        </div>
                        <span>Tahfizd</span>
                        <div class="menu-arrow">
                            <img src="{{ $isTahfizdActive ? '/icon/icon_arrow_terbuka_sidebar.png' : '/icon/icon_arrow_tertutup_sidebar.png' }}" id="arrowTahfizdIcon" alt="Arrow">
                        </div>
                    </button>

                    <div class="submenu-container {{ $isTahfizdActive ? 'open' : '' }}" id="submenuTahfizd">
                        <a href="{{ route('tahfizd.index') }}" class="submenu-item {{ request()->routeIs('tahfizd.index') ? 'active' : '' }}">
                            <span>Daftar Tahfizd</span>
                        </a>
                        <a href="{{ route('tahfizd.target.index') }}" class="submenu-item {{ request()->routeIs('tahfizd.target.*') ? 'active' : '' }}">
                            <span>Target Hafalan</span>
                        </a>
                    </div>
                </li>

                <!-- 5. Profile (Figma Node 393:4573) -->
                <li>
                    <a href="{{ route('profile.index') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_profile.png" alt="Profile">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>

            @elseif($role === 'murid')
                {{-- ==================== SIDEBAR MURID ==================== --}}
                
                <!-- 1. Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_dashboard.png" alt="Dashboard">
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 2. Absensi (Submenu: Absensi Kehadiran, Riwayat Absensi) -->
                <li>
                    <button type="button" class="menu-item {{ $isAbsensiActive ? 'active' : '' }}" id="btnToggleAbsensi" onclick="toggleAbsensiMenu()">
                        <div class="menu-icon">
                            <img src="/icon/icon_absensi.png" alt="Absensi">
                        </div>
                        <span>Absensi</span>
                        <div class="menu-arrow">
                            <img src="{{ $isAbsensiActive ? '/icon/icon_arrow_terbuka_sidebar.png' : '/icon/icon_arrow_tertutup_sidebar.png' }}" id="arrowAbsensiIcon" alt="Arrow">
                        </div>
                    </button>

                    <div class="submenu-container {{ $isAbsensiActive ? 'open' : '' }}" id="submenuAbsensi">
                        <a href="{{ route('absensi.kehadiran') }}" class="submenu-item {{ request()->routeIs('absensi.kehadiran') ? 'active' : '' }}">
                            <span>Absensi Kehadiran</span>
                        </a>
                        <a href="{{ route('absensi.detail') }}" class="submenu-item {{ request()->routeIs('absensi.detail') ? 'active' : '' }}">
                            <span>Riwayat Absensi</span>
                        </a>
                    </div>
                </li>

                <!-- 3. Tahfizd (Single menu item tanpa submenu sesuai instruksi user) -->
                <li>
                    <a href="{{ Auth::user()->murid ? route('siswa.show', Auth::user()->murid->id_murid) : '#' }}" class="menu-item {{ request()->routeIs('siswa.show') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_tahfizd.svg" alt="Tahfizd" style="width: 19px; height: 19px;">
                        </div>
                        <span>Tahfizd</span>
                    </a>
                </li>

                <!-- 4. Profile -->
                <li>
                    <a href="{{ route('profile.index') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_profile.png" alt="Profile">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>

            @else
                {{-- ==================== SIDEBAR ADMIN (Figma Node 618:1591) ==================== --}}
                
                <!-- 1. Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_dashboard.png" alt="Dashboard">
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- 2. Absensi (Accordion Toggle: Terbuka / Tertutup) -->
                <li>
                    <button type="button" class="menu-item {{ $isAbsensiActive ? 'active' : '' }}" id="btnToggleAbsensi" onclick="toggleAbsensiMenu()">
                        <div class="menu-icon">
                            <img src="/icon/icon_absensi.png" alt="Absensi">
                        </div>
                        <span>Absensi</span>
                        <div class="menu-arrow">
                            <img src="{{ $isAbsensiActive ? '/icon/icon_arrow_terbuka_sidebar.png' : '/icon/icon_arrow_tertutup_sidebar.png' }}" id="arrowAbsensiIcon" alt="Arrow">
                        </div>
                    </button>

                    <!-- Submenu Absensi (Figma Node 618:1321 & 618:1591) -->
                    <div class="submenu-container {{ $isAbsensiActive ? 'open' : '' }}" id="submenuAbsensi">
                        <a href="{{ route('absensi.kehadiran') }}" class="submenu-item {{ request()->routeIs('absensi.kehadiran') ? 'active' : '' }}">
                            <span>Absensi Kehadiran</span>
                        </a>
                        <a href="{{ route('absensi.detail') }}" class="submenu-item {{ request()->routeIs('absensi.detail') ? 'active' : '' }}">
                            <span>Riwayat Absensi</span>
                        </a>
                        <a href="{{ route('absensi.riwayat-pengguna') }}" class="submenu-item {{ request()->routeIs('absensi.riwayat-pengguna') ? 'active' : '' }}">
                            <span>Riwayat Absensi Pengguna</span>
                        </a>
                        <a href="{{ route('absensi.pengaturan.index') }}" class="submenu-item {{ request()->routeIs('absensi.pengaturan.*') ? 'active' : '' }}">
                            <span>Pengaturan Absensi</span>
                        </a>
                    </div>
                </li>

                <!-- 3. Hari Libur -->
                <li>
                    <a href="{{ route('hari-libur.index') }}" class="menu-item {{ request()->routeIs('hari-libur.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_hariLibur.png" alt="Hari Libur">
                        </div>
                        <span>Hari Libur</span>
                    </a>
                </li>

                <!-- 4. Hari Kerja -->
                <li>
                    <a href="{{ route('hari-kerja.index') }}" class="menu-item {{ request()->routeIs('hari-kerja.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_hari_kerja.png" alt="Hari Kerja">
                        </div>
                        <span>Hari Kerja</span>
                    </a>
                </li>

                <!-- 5. Tahun Akademik -->
                <li>
                    <a href="{{ route('tahun-akademik.index') }}" class="menu-item {{ request()->routeIs('tahun-akademik.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_tahunakademik.png" alt="Tahun Akademik">
                        </div>
                        <span>Tahun Akademik</span>
                    </a>
                </li>

                <!-- 6. Kelola Pengguna -->
                <li>
                    <a href="{{ route('pengguna.index') }}" class="menu-item {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_kelolapengguna.png" alt="Kelola Pengguna">
                        </div>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>

                <!-- 7. Profile -->
                <li>
                    <a href="{{ route('profile.index') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <div class="menu-icon">
                            <img src="/icon/icon_profile.png" alt="Profile">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <!-- ==================== MAIN SECTION (Top Header + Breadcrumb + Content) ==================== -->
    <div class="main-container">
        
        <!-- Top Header (Figma Node 618:939) -->
        <header class="topbar">
            <!-- Left: Identitas Sekolah -->
            <div class="school-info">
                Sekolah Islam ATTAUBAH Karawang<br>
                Jl.KH.Ahmad Dahlan No.E/4 Kaum 1<br>
                Hj.R.Rina Noor'aeni, SE
            </div>

            <!-- Right Controls: Badge Tahun Akademik, Profil Pengguna, Tombol Keluar -->
            <div class="topbar-right">
                <!-- Dropdown Tahun Akademik (Figma Node 393:4826 & 393:4827) -->
                @php
                    $navTahunList = \App\Models\TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
                    $activeYearId = session('active_tahun_akademik_id');
                    $activeTahun = $navTahunList->firstWhere('id_tahun_akademik', $activeYearId)
                                   ?? $navTahunList->firstWhere('is_aktif', true)
                                   ?? $navTahunList->first();
                    $activeTahunNama = $activeTahun ? $activeTahun->nama_tahun : '2026/2027';
                @endphp
                <div class="academic-year-dropdown-wrap" id="academicYearDropdownWrap">
                    <button type="button" class="badge-academic-year" id="btnToggleAcademicYear" onclick="toggleAcademicYearDropdown(event)">
                        <img src="/icon/icon_kalenderakademik_navbar.png" alt="Kalender">
                        <span>{{ $activeTahunNama }}</span>
                        @if($activeTahun && !$activeTahun->is_aktif)
                            <span style="font-size: 10px; background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 6px; font-weight: 700; margin-left: 4px;">Tidak Aktif</span>
                        @endif
                    </button>

                    @if($navTahunList->count() > 0)
                        <div class="academic-year-menu" id="academicYearMenu">
                            @foreach($navTahunList as $tItem)
                                <form action="{{ route('tahun-akademik.set-active', $tItem->id_tahun_akademik) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="academic-year-item {{ ($activeTahun && $activeTahun->id_tahun_akademik == $tItem->id_tahun_akademik) ? 'is-selected' : '' }}" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <img src="/icon/icon_kalenderakademik_navbar.png" alt="Kalender">
                                            <span>{{ $tItem->nama_tahun }}</span>
                                        </div>
                                        @if($tItem->is_aktif)
                                            <span style="font-size: 10px; background: #e8f5e9; color: #2e7d32; padding: 1px 6px; border-radius: 6px; font-weight: 700;">Aktif</span>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Profil Pengguna (Figma Node 618:941) -->
                <div class="user-profile-badge">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 2)) }}
                    </div>
                    <div class="user-info-text">
                        <span class="user-name">Hai, {{ Auth::user()->nama ?? 'User' }}</span>
                        <span class="user-role-label">{{ Auth::user()->role ?? 'Guru SD' }}</span>
                    </div>
                </div>

                <!-- Tombol Keluar (Figma Node 618:947) -->
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <img src="/icon/icon_logout_navbar.png" alt="Keluar">
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Sub-Header / Breadcrumb Bar (Figma Node 618:955 & 635:1757) -->
        <div class="breadcrumb-bar">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @else
                <span class="page-title">@yield('page_title', 'Dashboard')</span>
            @endif
            <span class="current-datetime" id="liveDateTime">
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y H:i') }}
            </span>
        </div>

        <!-- Main Viewport Content Card (Figma Node 618:938) -->
        <main class="content-body">
            <div class="content-card">
                @yield('content')
            </div>
        </main>

    </div>

    <!-- SCRIPT REAL-TIME CLOCK & ACCORDION TOGGLE (Figma Node 618:955 & 618:1591) -->
    <script>
        function updateLiveDateTime() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const liveEl = document.getElementById('liveDateTime');
            if (liveEl) {
                liveEl.textContent = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}`;
            }
        }

        // Jalankan saat pertama kali dimuat & perbarui berkala
        updateLiveDateTime();
        setInterval(updateLiveDateTime, 1000);

        function toggleAbsensiMenu() {
            const submenu = document.getElementById('submenuAbsensi');
            const arrowIcon = document.getElementById('arrowAbsensiIcon');

            if (submenu) {
                if (submenu.classList.contains('open')) {
                    submenu.classList.remove('open');
                    if (arrowIcon) arrowIcon.src = '/icon/icon_arrow_tertutup_sidebar.png';
                } else {
                    submenu.classList.add('open');
                    if (arrowIcon) arrowIcon.src = '/icon/icon_arrow_terbuka_sidebar.png';
                }
            }
        }

        function toggleTahfizdMenu() {
            const submenu = document.getElementById('submenuTahfizd');
            const arrowIcon = document.getElementById('arrowTahfizdIcon');

            if (submenu) {
                if (submenu.classList.contains('open')) {
                    submenu.classList.remove('open');
                    if (arrowIcon) arrowIcon.src = '/icon/icon_arrow_tertutup_sidebar.png';
                } else {
                    submenu.classList.add('open');
                    if (arrowIcon) arrowIcon.src = '/icon/icon_arrow_terbuka_sidebar.png';
                }
            }
        }

        // Toggle Dropdown Tahun Akademik di Navbar (Figma 393:4826 & 393:4827)
        function toggleAcademicYearDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('academicYearMenu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }

        document.addEventListener('click', function(event) {
            const wrap = document.getElementById('academicYearDropdownWrap');
            const menu = document.getElementById('academicYearMenu');
            if (wrap && menu && !wrap.contains(event.target)) {
                menu.classList.remove('show');
            }
        });

        // Live Jam Digital Format Bahasa Indonesia (WIB)
        function updateLiveDateTime() {
            const el = document.getElementById('liveDateTime');
            if (!el) return;

            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            const dayName = days[now.getDay()];
            const date = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            el.textContent = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}`;
        }
        updateLiveDateTime();
        setInterval(updateLiveDateTime, 1000);

        // ==================== GLOBAL INPUT SANITIZATION & LIMITATION ====================
        document.addEventListener('input', function(e) {
            const target = e.target;
            if (!target || target.tagName !== 'INPUT') return;

            const name = (target.name || '').toLowerCase();
            const id = (target.id || '').toLowerCase();

            // 1. Input Nama (Hanya Huruf, Spasi, Titik, Koma, Apostrof, Strip - TIDAK BOLEH ANGKA)
            if (name === 'nama' || id.includes('nama') || target.dataset.type === 'nama') {
                target.value = target.value.replace(/[^a-zA-Z\s.,'’\-]/g, '');
                if (target.maxLength <= 0 || target.maxLength > 100) target.maxLength = 100;
            }

            // 2. Input NIP / NISN (Hanya Angka, maksimal 18 digit)
            if (name === 'nip' || name === 'nisn' || name === 'nip_nisn' || id.includes('nip') || id.includes('nisn')) {
                target.value = target.value.replace(/\D/g, '');
                if (target.maxLength <= 0 || target.maxLength > 18) target.maxLength = 18;
            }

            // 3. Input No Handphone (Hanya Angka, bisa diawali 0 atau 62, maksimal 15 digit)
            if (name === 'no_hp' || id.includes('nohp') || id.includes('no_hp') || name.includes('phone') || name.includes('telepon')) {
                target.value = target.value.replace(/\D/g, '');
                if (target.maxLength <= 0 || target.maxLength > 15) target.maxLength = 15;
            }

            // 4. Input Tahun Akademik (Hanya Angka dan garis miring '/', maks 9 digit: 2026/2027)
            if (name === 'nama_tahun') {
                target.value = target.value.replace(/[^0-9\/]/g, '');
                if (target.maxLength <= 0 || target.maxLength > 9) target.maxLength = 9;
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
