@extends('layouts.app')

@section('title', 'Pengaturan Absensi')
@section('page_title', 'Pengaturan Absensi')

@section('styles')
    <link rel="stylesheet" href="/css/pengaturan-absensi.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="pa-flash pa-flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="pa-flash pa-flash-error">{{ session('error') }}</div>
    @endif

    <div class="pa-card-container">
        {{-- Card Header (Figma Node 709:73030) --}}
        <div class="pa-card-head">
            <div class="pa-card-head-left">
                {{-- Akar Icons Gear --}}
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="pa-head-icon" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.82 2.05C11.16 2 11.58 2 12 2C12.42 2 12.84 2 13.18 2.05C13.56 2.11 13.88 2.37 14.02 2.73L14.47 3.88C14.77 4.65 15.61 5.09 16.41 4.88L17.58 4.58C17.96 4.48 18.37 4.61 18.63 4.91C18.91 5.23 19.22 5.58 19.5 5.95C19.74 6.27 19.78 6.69 19.59 7.04L18.99 8.12C18.59 8.84 18.8 9.75 19.46 10.23L20.44 10.95C20.76 11.18 20.93 11.57 20.89 11.96C20.85 12.38 20.77 12.79 20.67 13.19C20.57 13.58 20.26 13.87 19.86 13.94L18.65 14.15C17.83 14.3 17.24 15.06 17.27 15.89L17.31 17.12C17.33 17.52 17.11 17.89 16.76 18.09C16.4 18.29 16.01 18.47 15.61 18.62C15.22 18.77 14.78 18.67 14.49 18.38L13.6 17.48C13 16.88 12 16.88 11.4 17.48L10.51 18.38C10.22 18.67 9.78 18.77 9.39 18.62C8.99 18.47 8.6 18.29 8.24 18.09C7.89 17.89 7.67 17.52 7.69 17.12L7.73 15.89C7.76 15.06 7.17 14.3 6.35 14.15L5.14 13.94C4.74 13.87 4.43 13.58 4.33 13.19C4.23 12.79 4.15 12.38 4.11 11.96C4.07 11.57 4.24 11.18 4.56 10.95L5.54 10.23C6.2 9.75 6.41 8.84 6.01 8.12L5.41 7.04C5.22 6.69 5.26 6.27 5.5 5.95C5.78 5.58 6.09 5.23 6.37 4.91C6.63 4.61 7.04 4.48 7.42 4.58L8.59 4.88C9.39 5.09 10.23 4.65 10.53 3.88L10.98 2.73C11.12 2.37 11.44 2.11 11.82 2.05H10.82ZM12 8C9.79 8 8 9.79 8 12C8 14.21 9.79 16 12 16C14.21 16 16 14.21 16 12C16 9.79 14.21 8 12 8Z" fill="#0E774F"/>
                </svg>
                <span class="pa-head-title">Detail Absensi</span>
            </div>
            <div class="pa-card-head-right">
                <select class="pa-select-show" id="perPage"
                        onchange="location.href='{{ route('absensi.pengaturan.index') }}?per_page='+this.value">
                    @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>
                            Tampilkan {{ $n }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="pa-btn-add" onclick="openTambahModal()">+ Tambah</button>
            </div>
        </div>

        {{-- Content Body (Figma Node 709:73050) --}}
        <div class="pa-body">
            <h2 class="pa-section-title">Jarak Batas Absensi</h2>

            <div class="pa-table-wrap">
                <table class="pa-tbl">
                    <thead>
                        <tr>
                            <th class="pa-th pa-col-no">No</th>
                            <th class="pa-th pa-col-nama">Nama Lokasi</th>
                            <th class="pa-th pa-col-lat">Latitude</th>
                            <th class="pa-th pa-col-lng">Longitude</th>
                            <th class="pa-th pa-col-radius">Radius Meter</th>
                            <th class="pa-th pa-col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lokasiList as $i => $item)
                            <tr>
                                <td class="pa-td pa-col-no">{{ ($lokasiList->currentPage() - 1) * $lokasiList->perPage() + $i + 1 }}</td>
                                <td class="pa-td pa-col-nama pa-td-nama">{{ $item->nama_lokasi }}</td>
                                <td class="pa-td pa-col-lat">{{ $item->latitude }}</td>
                                <td class="pa-td pa-col-lng">{{ $item->longitude }}</td>
                                <td class="pa-td pa-col-radius">{{ number_format($item->radius_meter, 0, ',', '.') }} Meter</td>
                                <td class="pa-td pa-col-aksi">
                                    <div class="pa-aksi-container">
                                        {{-- Hapus (Merah) --}}
                                        <button type="button" class="pa-btn-icon" title="Hapus Lokasi"
                                                onclick="openDeleteModal('{{ route('absensi.pengaturan.destroy', $item->id_pengaturan) }}')">
                                            <svg width="15" height="17" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.099 13.11C10.0719 13.3549 9.9554 13.5811 9.7718 13.7453C9.5881 13.9096 9.3504 14.0003 9.104 14H2.897C2.6506 14.0003 2.4129 13.9096 2.2292 13.7453C2.0456 13.5811 1.9291 13.3549 1.902 13.11L1 5H11L10.099 13.11ZM8 0C8.2652 0 8.5196 0.105357 8.7071 0.292893C8.8946 0.48043 9 0.734784 9 1V2H11C11.2652 2 11.5196 2.10536 11.7071 2.29289C11.8946 2.48043 12 2.73478 12 3V4H0V3C0 2.73478 0.105357 2.48043 0.292893 2.29289C0.48043 2.10536 0.734784 2 1 2H3V1C3 0.734784 3.10536 0.48043 3.29289 0.292893C3.48043 0.105357 3.73478 0 4 0H8ZM8 1H4V2H8V1Z" fill="#FF0000"/>
                                            </svg>
                                        </button>

                                        {{-- Edit (Hijau) --}}
                                        <button type="button" class="pa-btn-icon" title="Perbarui Lokasi"
                                                onclick="openEditModal({{ $item->id_pengaturan }}, '{{ addslashes($item->nama_lokasi) }}', '{{ $item->latitude }}', '{{ $item->longitude }}', '{{ $item->radius_meter }}')">
                                            <svg width="17" height="17" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.5719 10.7805L13.5719 9.7805C13.7281 9.6242 14 9.7336 14 9.9586V14.5023C14 15.3305 13.3281 16.0023 12.5 16.0023H1.5C0.6719 16.0023 0 15.3305 0 14.5023V3.5023C0 2.6742 0.6719 2.0023 1.5 2.0023H10.0469C10.2688 2.0023 10.3813 2.2711 10.225 2.4305L9.225 3.4305C9.1781 3.4773 9.1156 3.5023 9.0469 3.5023H1.5V14.5023H12.5V10.9555C12.5 10.8898 12.525 10.8273 12.5719 10.7805ZM17.4656 4.4742L9.2594 12.6805L6.4344 12.993C5.6156 13.0836 4.9188 12.393 5.0094 11.568L5.3219 8.743L13.5281 0.536719C14.2437 -0.178906 15.4 -0.178906 16.1125 0.536719L17.4625 1.88672C18.1781 2.6023 18.1781 3.7617 17.4656 4.4742ZM14.3781 5.4398L12.5625 3.6242L6.7562 9.4336L6.5281 11.4742L8.5688 11.2461L14.3781 5.4398ZM16.4031 2.9492L15.0531 1.5992C14.925 1.4711 14.7156 1.4711 14.5906 1.5992L13.625 2.5648L15.4406 4.3805L16.4062 3.4148C16.5312 3.2836 16.5312 3.0773 16.4031 2.9492Z" fill="#0E7651"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="pa-td pa-empty">Belum ada pengaturan jarak batas absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $lokasiList->links() }}
            </div>
        </div>
    </div>

    {{-- ==================== MODAL TAMBAH (Form Lokasi GPS) ==================== --}}
    <div id="modalTambah" class="pa-overlay" style="display: none;">
        <div class="pa-modal-card">
            <div class="pa-modal-header">
                <h3 class="pa-modal-title">Form Lokasi GPS</h3>
                <button type="button" class="pa-modal-close" onclick="closeTambahModal()">&times;</button>
            </div>
            <form action="{{ route('absensi.pengaturan.store') }}" method="POST">
                @csrf
                {{-- Baris Nama Lokasi --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Nama Lokasi</label>
                    <div class="pa-input-col">
                        <input type="text" name="nama_lokasi" class="pa-form-input" placeholder="Nama Lokasi" required>
                    </div>
                </div>

                {{-- Interactive Leaflet Map dengan Floating Button --}}
                <div class="pa-map-box">
                    <div id="mapTambah" class="pa-map-inner"></div>
                    <button type="button" class="pa-btn-locate-float" onclick="locateUser('tambah')" title="Gunakan Lokasi GPS Saya Saat Ini">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>📍 Lokasi Saya</span>
                    </button>
                </div>

                {{-- Baris Latitude --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Latitude</label>
                    <div class="pa-input-col">
                        <input type="text" name="latitude" id="tambahLat" class="pa-form-input" placeholder="Latitude" required>
                    </div>
                </div>

                {{-- Baris Longitude --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Longitude</label>
                    <div class="pa-input-col">
                        <input type="text" name="longitude" id="tambahLng" class="pa-form-input" placeholder="Longitude" required>
                    </div>
                </div>

                {{-- Baris Radius --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Radius (Meter)</label>
                    <div class="pa-input-col">
                        <input type="number" name="radius_meter" id="tambahRadius" class="pa-form-input" placeholder="Radius (Meter)" min="1" value="100" required>
                    </div>
                </div>

                {{-- Tombol Aksi Batal & Simpan --}}
                <div class="pa-modal-actions">
                    <button type="button" class="pa-btn-cancel" onclick="closeTambahModal()">Batal</button>
                    <button type="submit" class="pa-btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL EDIT (Form Lokasi GPS) ==================== --}}
    <div id="modalEdit" class="pa-overlay" style="display: none;">
        <div class="pa-modal-card">
            <div class="pa-modal-header">
                <h3 class="pa-modal-title">Form Lokasi GPS</h3>
                <button type="button" class="pa-modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                {{-- Baris Nama Lokasi --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Nama Lokasi</label>
                    <div class="pa-input-col">
                        <input type="text" name="nama_lokasi" id="editNama" class="pa-form-input" placeholder="Nama Lokasi" required>
                    </div>
                </div>

                {{-- Interactive Leaflet Map Edit dengan Floating Button --}}
                <div class="pa-map-box">
                    <div id="mapEdit" class="pa-map-inner"></div>
                    <button type="button" class="pa-btn-locate-float" onclick="locateUser('edit')" title="Gunakan Lokasi GPS Saya Saat Ini">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>📍 Lokasi Saya</span>
                    </button>
                </div>

                {{-- Baris Latitude --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Latitude</label>
                    <div class="pa-input-col">
                        <input type="text" name="latitude" id="editLat" class="pa-form-input" placeholder="Latitude" required>
                    </div>
                </div>

                {{-- Baris Longitude --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Longitude</label>
                    <div class="pa-input-col">
                        <input type="text" name="longitude" id="editLng" class="pa-form-input" placeholder="Longitude" required>
                    </div>
                </div>

                {{-- Baris Radius --}}
                <div class="pa-form-row">
                    <label class="pa-label-col">Radius (Meter)</label>
                    <div class="pa-input-col">
                        <input type="number" name="radius_meter" id="editRadius" class="pa-form-input" placeholder="Radius (Meter)" min="1" required>
                    </div>
                </div>

                {{-- Tombol Aksi Batal & Simpan --}}
                <div class="pa-modal-actions">
                    <button type="button" class="pa-btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="pa-btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL KONFIRMASI HAPUS ==================== --}}
    <div id="deleteModalOverlay" class="pa-overlay" style="display: none;">
        <div class="pa-alert-modal">
            <button type="button" class="pa-alert-close" onclick="closeDeleteModal()" title="Tutup">
                <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup" width="16" height="16">
            </button>
            <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="pa-alert-logo">
            <h1 class="pa-alert-title">Menghapus</h1>
            <p class="pa-alert-subtitle">Apakah Anda Yakin Ingin Menghapusnya?</p>
            <form id="deleteForm" method="POST" style="width: 100%;">
                @csrf
                @method('DELETE')
                <div class="pa-confirm-btns">
                    <button type="button" class="pa-btn-confirm-batal" onclick="closeDeleteModal()">Batal</button>
                    <button type="submit" class="pa-btn-confirm-setujui">Setujui</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Leaflet.js Library --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const DEFAULT_LAT = -6.3045200;
        const DEFAULT_LNG = 107.3031000;
        const DEFAULT_RADIUS = 100;

        let mapTambah = null;
        let markerTambah = null;
        let circleTambah = null;

        let mapEdit = null;
        let markerEdit = null;
        let circleEdit = null;

        // Inisialisasi Peta Modal Tambah
        function initMapTambah() {
            if (mapTambah) return;

            mapTambah = L.map('mapTambah').setView([DEFAULT_LAT, DEFAULT_LNG], 16);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapTambah);

            markerTambah = L.marker([DEFAULT_LAT, DEFAULT_LNG], { draggable: true }).addTo(mapTambah);
            circleTambah = L.circle([DEFAULT_LAT, DEFAULT_LNG], {
                radius: DEFAULT_RADIUS,
                color: '#0e7651',
                fillColor: '#10b981',
                fillOpacity: 0.25,
                weight: 2
            }).addTo(mapTambah);

            // Set nilai input default
            document.getElementById('tambahLat').value = DEFAULT_LAT.toFixed(7);
            document.getElementById('tambahLng').value = DEFAULT_LNG.toFixed(7);
            document.getElementById('tambahRadius').value = DEFAULT_RADIUS;

            // Klik peta untuk pindah posisi
            mapTambah.on('click', function(e) {
                updateTambahLocation(e.latlng.lat, e.latlng.lng);
            });

            // Drag marker untuk pindah posisi
            markerTambah.on('dragend', function() {
                const pos = markerTambah.getLatLng();
                updateTambahLocation(pos.lat, pos.lng);
            });

            // Input manual latitude & longitude
            const syncTambahInputs = () => {
                const lat = parseFloat(document.getElementById('tambahLat').value);
                const lng = parseFloat(document.getElementById('tambahLng').value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    markerTambah.setLatLng([lat, lng]);
                    circleTambah.setLatLng([lat, lng]);
                    mapTambah.panTo([lat, lng]);
                }
            };
            document.getElementById('tambahLat').addEventListener('input', syncTambahInputs);
            document.getElementById('tambahLng').addEventListener('input', syncTambahInputs);

            // Input manual radius
            document.getElementById('tambahRadius').addEventListener('input', function() {
                const r = parseFloat(this.value);
                if (!isNaN(r) && r > 0) {
                    circleTambah.setRadius(r);
                }
            });
        }

        function updateTambahLocation(lat, lng) {
            markerTambah.setLatLng([lat, lng]);
            circleTambah.setLatLng([lat, lng]);
            document.getElementById('tambahLat').value = lat.toFixed(7);
            document.getElementById('tambahLng').value = lng.toFixed(7);
        }

        // Inisialisasi Peta Modal Edit
        function initMapEdit(lat, lng, radius) {
            if (!mapEdit) {
                mapEdit = L.map('mapEdit').setView([lat, lng], 16);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapEdit);

                markerEdit = L.marker([lat, lng], { draggable: true }).addTo(mapEdit);
                circleEdit = L.circle([lat, lng], {
                    radius: radius,
                    color: '#0e7651',
                    fillColor: '#10b981',
                    fillOpacity: 0.25,
                    weight: 2
                }).addTo(mapEdit);

                mapEdit.on('click', function(e) {
                    updateEditLocation(e.latlng.lat, e.latlng.lng);
                });

                markerEdit.on('dragend', function() {
                    const pos = markerEdit.getLatLng();
                    updateEditLocation(pos.lat, pos.lng);
                });

                const syncEditInputs = () => {
                    const l = parseFloat(document.getElementById('editLat').value);
                    const g = parseFloat(document.getElementById('editLng').value);
                    if (!isNaN(l) && !isNaN(g)) {
                        markerEdit.setLatLng([l, g]);
                        circleEdit.setLatLng([l, g]);
                        mapEdit.panTo([l, g]);
                    }
                };
                document.getElementById('editLat').addEventListener('input', syncEditInputs);
                document.getElementById('editLng').addEventListener('input', syncEditInputs);

                document.getElementById('editRadius').addEventListener('input', function() {
                    const r = parseFloat(this.value);
                    if (!isNaN(r) && r > 0) {
                        circleEdit.setRadius(r);
                    }
                });
            } else {
                updateEditLocation(lat, lng);
                circleEdit.setRadius(radius);
                mapEdit.setView([lat, lng], 16);
            }
        }

        function updateEditLocation(lat, lng) {
            markerEdit.setLatLng([lat, lng]);
            circleEdit.setLatLng([lat, lng]);
            document.getElementById('editLat').value = lat.toFixed(7);
            document.getElementById('editLng').value = lng.toFixed(7);
        }

        // Buka / Tutup Modal
        function openTambahModal() {
            document.getElementById('modalTambah').style.display = 'flex';
            setTimeout(() => {
                initMapTambah();
                if (mapTambah) mapTambah.invalidateSize();
            }, 150);
        }

        function closeTambahModal() {
            document.getElementById('modalTambah').style.display = 'none';
        }

        function openEditModal(id, nama, lat, lng, radius) {
            const form = document.getElementById('formEdit');
            form.action = `/absensi/pengaturan/${id}`;
            document.getElementById('editNama').value = nama;
            document.getElementById('editLat').value = lat;
            document.getElementById('editLng').value = lng;
            document.getElementById('editRadius').value = radius;

            document.getElementById('modalEdit').style.display = 'flex';

            const numLat = parseFloat(lat) || DEFAULT_LAT;
            const numLng = parseFloat(lng) || DEFAULT_LNG;
            const numRadius = parseFloat(radius) || DEFAULT_RADIUS;

            setTimeout(() => {
                initMapEdit(numLat, numLng, numRadius);
                if (mapEdit) mapEdit.invalidateSize();
            }, 150);
        }

        function closeEditModal() {
            document.getElementById('modalEdit').style.display = 'none';
        }

        function openDeleteModal(actionUrl) {
            const form = document.getElementById('deleteForm');
            if (form) form.action = actionUrl;
            document.getElementById('deleteModalOverlay').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModalOverlay').style.display = 'none';
        }

        // Dapatkan lokasi GPS perangkat user
        function locateUser(mode) {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    if (mode === 'tambah') {
                        updateTambahLocation(lat, lng);
                        if (mapTambah) mapTambah.setView([lat, lng], 17);
                    } else {
                        updateEditLocation(lat, lng);
                        if (mapEdit) mapEdit.setView([lat, lng], 17);
                    }
                },
                (err) => {
                    alert('Gagal mendeteksi lokasi GPS perangkat. Pastikan izin lokasi telah diaktifkan pada browser Anda.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }
    </script>
@endsection
