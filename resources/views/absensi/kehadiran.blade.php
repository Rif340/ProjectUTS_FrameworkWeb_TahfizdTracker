@extends('layouts.app')

@section('title', 'Absensi Kehadiran')

@section('breadcrumb')
<div class="breadcrumb-nav">
    <a href="{{ route('absensi.kehadiran') }}" class="breadcrumb-link">
        Absensi
    </a>
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="transform: rotate(-90deg);">
        <path d="M2.5 3.75L5 6.25L7.5 3.75" stroke="#71717A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="breadcrumb-current">
        Absensi Kehadiran
    </span>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/css/kehadiran.css">
@endsection

@section('content')
    <!-- Card Header (Figma Node 635:1746) -->
    <div class="absensi-card-header">
        <div class="absensi-header-title">
            <img src="/icon/icon_absensi.png" alt="Absensi Kehadiran">
            <span>Absensi Kehadiran</span>
        </div>

        <!-- Tombol Detail Absensi Oranye (Figma) -->
        <a href="{{ route('absensi.detail') }}" class="btn-detail-absensi">
            <img src="/icon/icon_detail_absensi.svg" alt="List">
            <span>Detail Absensi</span>
        </a>
    </div>

    <!-- Card Main Body (Figma Node 635:1717) -->
    <div class="absensi-card-body">
        
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div style="background: #E8FFF3; border: 1px solid #1BC5BD; color: #0E7651; padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-family: 'Poppins', sans-serif; font-size: 14px; width: 100%; max-width: 820px; display: flex; justify-content: space-between;">
                <span>{{ session('success') }}</span>
                <span style="cursor: pointer;" onclick="this.parentElement.style.display='none'">&times;</span>
            </div>
        @endif

        @if(session('error'))
            <div style="background: #FFF5F8; border: 1px solid #F64E60; color: #D63042; padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-family: 'Poppins', sans-serif; font-size: 14px; width: 100%; max-width: 820px; display: flex; justify-content: space-between;">
                <span>{{ session('error') }}</span>
                <span style="cursor: pointer;" onclick="this.parentElement.style.display='none'">&times;</span>
            </div>
        @endif

        @php
            $sudahMasuk = $absensi && $absensi->waktu_masuk;
            $sudahPulang = $absensi && $absensi->waktu_pulang;
        @endphp

        <div class="absensi-columns-wrapper">
            
            <!-- ==================== KOLOM 1: ABSENSI MASUK ==================== -->
            <div class="absensi-col">
                <!-- Icon (Figma Node 635:1726) -->
                <div class="absensi-icon-box" style="cursor: pointer;" onclick="openScanModal('masuk')">
                    <img src="/icon/icon_login_masuk.svg" alt="Absensi Masuk">
                </div>

                <!-- Judul (Figma Node 635:1724) -->
                <h3 class="absensi-col-title">Absensi Masuk</h3>

                <!-- Status Text (Figma Node 635:1722 / Sesuai Screenshot User) -->
                <div class="absensi-status-badge {{ $sudahMasuk ? 'scanned-date' : '' }}">
                    @if(!$sudahMasuk)
                        [Kamu belum melakukan absensi masuk]
                    @else
                        {{ $tanggalMasukFormatted ?? \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                    @endif
                </div>

                <!-- Tombol Action (Figma Node 635:1720) -->
                <button type="button" class="btn-absen-action btn-absen-masuk" onclick="openScanModal('masuk')">
                    MASUK
                </button>

                <!-- Jadwal Resmi (Figma Node 635:1731) -->
                <p class="absensi-footer-info">
                    Absensi Masuk : {{ $jamMasukDefault }}
                </p>
            </div>

            <!-- ==================== KOLOM 2: ABSENSI PULANG ==================== -->
            <div class="absensi-col">
                <!-- Icon (Figma Node 635:1740) -->
                <div class="absensi-icon-box" style="cursor: pointer;" onclick="openScanModal('pulang')">
                    <img src="/icon/icon_logout_keluar.svg" alt="Absensi Pulang">
                </div>

                <!-- Judul (Figma Node 635:1738) -->
                <h3 class="absensi-col-title">Absensi Pulang</h3>

                <!-- Status Text (Figma Node 635:1736 / Sesuai Screenshot User) -->
                <div class="absensi-status-badge {{ $sudahPulang ? 'scanned-date' : '' }}">
                    @if(!$sudahPulang)
                        [Kamu belum melakukan absensi keluar]
                    @else
                        {{ $tanggalPulangFormatted ?? \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                    @endif
                </div>

                <!-- Tombol Action (Figma Node 635:1734) -->
                <button type="button" class="btn-absen-action btn-absen-pulang" onclick="openScanModal('pulang')">
                    KELUAR
                </button>

                <!-- Jadwal Resmi (Figma Node 635:1745) -->
                <p class="absensi-footer-info">
                    Absensi Keluar : {{ $jamPulangDefault }}
                </p>
            </div>

        </div>

    </div>

    <!-- ==================== SCANNER MODAL POPUP (Figma Node 637:2471) ==================== -->
    <div class="modal-overlay" id="formAbsensiModal">
        <div class="modal-card">
            
            <!-- Modal Header -->
            <div class="modal-header">
                <span class="modal-title" id="modalTitleText">Form Absensi</span>
                <button type="button" class="modal-close-btn" onclick="closeScanModal()">
                    <img src="/icon/icon_close_modal.svg" alt="Tutup">
                </button>
            </div>

            <!-- 1. VIEWPORT SCANNER KAMERA (STATE SCANNING) -->
            <div id="scannerSection" style="display: flex; flex-direction: column; align-items: center;">
                <div class="scanner-viewport-box">
                    <div id="reader"></div>

                    <!-- Target Corner Overlay (Figma Node 637:2606) -->
                    <div class="scan-target-overlay">
                        <div class="corner-bracket corner-top-left"></div>
                        <div class="corner-bracket corner-top-right"></div>
                        <div class="corner-bracket corner-bottom-left"></div>
                        <div class="corner-bracket corner-bottom-right"></div>
                        <div class="scan-laser-line"></div>
                    </div>
                </div>

                <div class="scanner-controls">
                    <p class="scanner-instruction" id="scanInstruction">
                        Arahkan kamera ke QR Code / Barcode kartu kehadiran Anda
                    </p>

                </div>
            </div>

            <!-- 2. HASIL SCAN: BERHASIL (SUCCESS STATE - Logo At-Taubah Sesuai Permintaan User) -->
            <div class="scan-result-card" id="resultSuccessView">
                <div style="width: 76px; height: 76px; margin: 0 auto 4px auto; display: flex; align-items: center; justify-content: center;">
                    <img src="/gambar/logo.png" alt="Logo SDIT At-Taubah" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <div>
                    <h4 class="result-title success" id="successTitle" style="color: #0E9945;">Absensi Masuk Berhasil!</h4>
                    <p class="result-subtitle" id="successSubtitle">Data kehadiran berhasil diverifikasi dan disimpan ke sistem.</p>
                </div>

                <div class="result-details-box">
                    <div class="detail-row">
                        <span class="label">Nama Pengguna</span>
                        <span class="value" id="successNama">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Peran / NIP / NISN</span>
                        <span class="value" id="successRole">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Waktu Presensi</span>
                        <span class="value" id="successWaktu">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Status Kehadiran</span>
                        <span class="value" id="successStatusBadge">
                            <span class="badge-status tepat" id="successStatusText">Tepat Waktu</span>
                        </span>
                    </div>
                </div>

                <!-- Tombol Hijau: Selesai (Figma Done Button) -->
                <button type="button" class="btn-result-action success" onclick="finishAndReload()">
                    Selesai
                </button>
            </div>

            <!-- 3. HASIL SCAN: GAGAL (FAILED STATE - Tombol Merah) -->
            <div class="scan-result-card" id="resultDangerView">
                <div style="width: 76px; height: 76px; margin: 0 auto 4px auto; display: flex; align-items: center; justify-content: center;">
                    <img src="/gambar/logo.png" alt="Logo SDIT At-Taubah" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <div>
                    <h4 class="result-title danger" id="dangerTitle">Absensi Gagal!</h4>
                    <p class="result-subtitle" id="dangerSubtitle">Barcode tidak valid atau bukan dari data resmi SDIT At-Taubah.</p>
                </div>

                <div class="result-details-box" style="background: #FFF8F8; border-color: #F64E60;">
                    <div class="detail-row">
                        <span class="label">Keterangan Kesalahan</span>
                        <span class="value" id="dangerReason" style="color: #D63042;">QR Code Tidak Dikenali</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Kode Barcode Terbaca</span>
                        <span class="value" id="dangerCode" style="font-family: monospace; font-size: 11px;">-</span>
                    </div>
                </div>

                <!-- Tombol Merah: Coba Lagi (Sesuai Permintaan User) -->
                <button type="button" class="btn-result-action danger" onclick="restartScanner()">
                    Coba Lagi
                </button>
            </div>

        </div>
    </div>

    <!-- ==================== POPUP ALERT ERROR MODAL ==================== -->
    <div class="modal-overlay" id="alertErrorModal" style="display: none;">
        <div class="modal-card" style="width: 440px; text-align: center; align-items: center; padding: 32px 28px;">
            <div style="width: 76px; height: 76px; margin: 0 auto 10px auto; display: flex; align-items: center; justify-content: center;">
                <img src="/gambar/logo.png" alt="Logo SDIT At-Taubah" style="width: 100%; height: 100%; object-fit: contain;">
            </div>

            <h4 id="alertErrorTitle" style="font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 6px;">
                Belum Bisa Absen Keluar!
            </h4>
            <p id="alertErrorMessage" style="font-family: 'Poppins', sans-serif; font-size: 13px; color: #6B7280; line-height: 1.4; margin-bottom: 20px;">
                Anda belum melakukan absensi masuk hari ini. Silakan lakukan absensi masuk terlebih dahulu sebelum absensi pulang/keluar.
            </p>

            <button type="button" class="btn-result-action danger" onclick="closeAlertErrorModal()" style="width: 100%; height: 42px; font-size: 14px;">
                Mengerti
            </button>
        </div>
    </div>
@endsection

@section('scripts')
<!-- Library Scanner HTML5 QR Code (Lokal & Cepat) -->
<script src="/js/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let currentTipe = 'masuk'; // 'masuk' atau 'pulang'
    let isProcessing = false;

    const sudahMasukHariIni = {{ $sudahMasuk ? 'true' : 'false' }};
    const isHariKerjaAktif  = {{ $isHariKerjaAktif ? 'true' : 'false' }};
    const bukaMasukDefault  = "{{ $bukaMasukDefault }}";
    const jamMasukDefault   = "{{ $jamMasukDefault }}";
    const jamPulangDefault  = "{{ $jamPulangDefault }}";

    function openAlertErrorModal(title, message) {
        if (title) document.getElementById('alertErrorTitle').textContent = title;
        if (message) document.getElementById('alertErrorMessage').textContent = message;
        document.getElementById('alertErrorModal').style.display = 'flex';
    }

    function closeAlertErrorModal() {
        document.getElementById('alertErrorModal').style.display = 'none';
    }

    function openScanModal(tipe = 'masuk') {
        if (!isHariKerjaAktif) {
            openAlertErrorModal(
                'Absensi Tidak Dapat Dilakukan!',
                'Hari ini bukan hari kerja aktif atau sekolah sedang libur. Absensi tidak dapat dilakukan.'
            );
            return;
        }

        const now = new Date();
        const currentHours = String(now.getHours()).padStart(2, '0') + ':' + 
                             String(now.getMinutes()).padStart(2, '0') + ':' + 
                             String(now.getSeconds()).padStart(2, '0');

        const isOvernight = jamPulangDefault < jamMasukDefault;

        if (tipe === 'masuk') {
            if (isOvernight) {
                // Shift malam melintasi tengah malam (misal buka 21:01, masuk 23:01, pulang 00:01)
                const isValidMasuk = (currentHours >= bukaMasukDefault || currentHours <= jamPulangDefault);
                if (!isValidMasuk) {
                    if (currentHours < '12:00:00') {
                        openAlertErrorModal(
                            'Waktu Absensi Masuk Berakhir!',
                            'Waktu absensi masuk sudah berakhir karena sudah melewati jam pulang sekolah (' + jamPulangDefault.substring(0, 5) + ' WIB).'
                        );
                    } else {
                        openAlertErrorModal(
                            'Absensi Masuk Belum Dibuka!',
                            'Absensi masuk baru dapat dilakukan mulai pukul ' + bukaMasukDefault.substring(0, 5) + ' WIB (2 jam sebelum jam masuk resmi ' + jamMasukDefault.substring(0, 5) + ' WIB).'
                        );
                    }
                    return;
                }
            } else {
                if (currentHours < bukaMasukDefault) {
                    openAlertErrorModal(
                        'Absensi Masuk Belum Dibuka!',
                        'Absensi masuk baru dapat dilakukan mulai pukul ' + bukaMasukDefault.substring(0, 5) + ' WIB (2 jam sebelum jam masuk resmi ' + jamMasukDefault.substring(0, 5) + ' WIB).'
                    );
                    return;
                }
                if (currentHours > jamPulangDefault) {
                    openAlertErrorModal(
                        'Waktu Absensi Masuk Berakhir!',
                        'Waktu absensi masuk sudah berakhir karena sudah melewati jam pulang sekolah (' + jamPulangDefault.substring(0, 5) + ' WIB).'
                    );
                    return;
                }
            }
        } else {
            // Tipe pulang
            if (!sudahMasukHariIni) {
                openAlertErrorModal(
                    'Belum Bisa Absen Keluar!',
                    'Anda belum melakukan absensi masuk hari ini. Silakan lakukan absensi masuk terlebih dahulu sebelum absensi pulang/keluar.'
                );
                return;
            }
            if (isOvernight) {
                const isBelumPulang = (currentHours >= jamMasukDefault || currentHours < jamPulangDefault);
                if (isBelumPulang) {
                    openAlertErrorModal(
                        'Belum Waktunya Absensi Pulang!',
                        'Absensi pulang baru dibuka mulai pukul ' + jamPulangDefault.substring(0, 5) + ' WIB.'
                    );
                    return;
                }
            } else {
                if (currentHours < jamPulangDefault) {
                    openAlertErrorModal(
                        'Belum Waktunya Absensi Pulang!',
                        'Absensi pulang baru dibuka mulai pukul ' + jamPulangDefault.substring(0, 5) + ' WIB.'
                    );
                    return;
                }
            }
        }

        currentTipe = tipe;
        // Judul bersih tanpa kurung sesuai permintaan user (contoh: Form Absensi Masuk)
        document.getElementById('modalTitleText').textContent = tipe === 'masuk' ? 'Form Absensi Masuk' : 'Form Absensi Pulang';
        document.getElementById('formAbsensiModal').classList.add('active');
        
        // Reset view
        document.getElementById('scannerSection').style.display = 'flex';
        document.getElementById('resultSuccessView').style.display = 'none';
        document.getElementById('resultDangerView').style.display = 'none';
        isProcessing = false;

        startScanner();
    }

    function closeScanModal() {
        stopScanner();
        document.getElementById('formAbsensiModal').classList.remove('active');
    }

    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = {
            fps: 15,
            aspectRatio: 1.0
        };

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                // Pilih kamera belakang jika ada di mobile/hp
                let cameraId = devices[0].id;
                for (let dev of devices) {
                    if (dev.label.toLowerCase().includes('back') || dev.label.toLowerCase().includes('belakang')) {
                        cameraId = dev.id;
                        break;
                    }
                }

                html5QrCode.start(
                    cameraId,
                    config,
                    (decodedText, decodedResult) => {
                        handleScanResult(decodedText);
                    },
                    (errorMessage) => {
                        // scanning frame by frame, abaikan error per-frame
                    }
                ).catch(err => {
                    console.warn("Kamera tidak dapat dimulai:", err);
                    document.getElementById('scanInstruction').textContent = 'Kamera tidak tersedia. Silakan gunakan tombol "Unggah Gambar QR Code" atau Uji Cepat di bawah.';
                });
            } else {
                document.getElementById('scanInstruction').textContent = 'Kamera tidak terdeteksi. Silakan gunakan tombol "Unggah Gambar QR Code" atau tombol Uji Cepat.';
            }
        }).catch(err => {
            console.warn("Akses kamera gagal:", err);
            document.getElementById('scanInstruction').textContent = 'Kamera tidak diizinkan atau tidak ada. Gunakan tombol "Unggah Gambar QR Code" atau Uji Cepat.';
        });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                console.log("Scanner dihentikan.");
            }).catch(err => {
                console.warn("Gagal menghentikan scanner:", err);
            });
        }
    }

    // Handle Upload File Gambar QR Code
    function handleQRFile(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                handleScanResult(decodedText);
            })
            .catch(err => {
                alert("Tidak dapat membaca QR Code dari gambar yang diunggah. Pastikan gambar jelas!");
            });
    }

    // Handle Uji Cepat Simulasi Barcode
    function simulateQR(code) {
        handleScanResult(code);
    }

    let userCurrentLat = null;
    let userCurrentLng = null;

    function refreshUserLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userCurrentLat = pos.coords.latitude;
                    userCurrentLng = pos.coords.longitude;
                },
                (err) => {
                    console.warn("Lokasi awal belum didapatkan:", err);
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }
    }
    refreshUserLocation();

    // Verifikasi QR Code ke Server dengan Validasi Radius GPS
    function handleScanResult(qrData) {
        if (isProcessing) return;
        isProcessing = true;
        stopScanner();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    sendScanVerification(qrData, pos.coords.latitude, pos.coords.longitude);
                },
                (err) => {
                    sendScanVerification(qrData, userCurrentLat, userCurrentLng);
                },
                { enableHighAccuracy: true, timeout: 5000, maximumAge: 10000 }
            );
        } else {
            sendScanVerification(qrData, null, null);
        }
    }

    function sendScanVerification(qrData, lat, lng) {
        fetch("{{ route('absensi.scan.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_data: qrData,
                tipe: currentTipe,
                latitude: lat,
                longitude: lng
            })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('scannerSection').style.display = 'none';

            if (data.success) {
                // Tampilkan Tampilan Berhasil (Figma Node 637:2616)
                document.getElementById('successTitle').textContent = data.message || 'Absensi Berhasil!';
                document.getElementById('successNama').textContent = data.user.nama;
                document.getElementById('successRole').textContent = data.user.role + ' (' + (data.user.nomor_induk || '-') + ')';
                document.getElementById('successWaktu').textContent = data.user.waktu;
                
                const statusBadge = document.getElementById('successStatusText');
                const statusVal = data.user.status || 'Tepat Waktu';
                statusBadge.textContent = statusVal;
                statusBadge.className = 'badge-status ' + (statusVal.toLowerCase() === 'tepat waktu' ? 'tepat' : 'terlambat');

                document.getElementById('resultSuccessView').style.display = 'flex';
                document.getElementById('resultDangerView').style.display = 'none';
            } else {
                // Tampilkan Tampilan Gagal (Tombol Merah)
                document.getElementById('dangerTitle').textContent = 'Absensi Gagal!';
                document.getElementById('dangerSubtitle').textContent = 'Proses verifikasi absensi tidak dapat diselesaikan.';
                document.getElementById('dangerReason').textContent = data.message || 'QR Code Tidak Valid!';
                document.getElementById('dangerCode').textContent = data.scanned_code || qrData;

                document.getElementById('resultSuccessView').style.display = 'none';
                document.getElementById('resultDangerView').style.display = 'flex';
            }
        })
        .catch(err => {
            console.error("Error scan verify:", err);
            document.getElementById('scannerSection').style.display = 'none';
            document.getElementById('dangerTitle').textContent = 'Absensi Gagal!';
            document.getElementById('dangerReason').textContent = 'Terjadi kesalahan jaringan atau server saat memproses presensi.';
            document.getElementById('dangerCode').textContent = qrData;

            document.getElementById('resultSuccessView').style.display = 'none';
            document.getElementById('resultDangerView').style.display = 'flex';
        })
        .finally(() => {
            isProcessing = false;
        });
    }

    function restartScanner() {
        document.getElementById('resultDangerView').style.display = 'none';
        document.getElementById('resultSuccessView').style.display = 'none';
        document.getElementById('scannerSection').style.display = 'flex';
        startScanner();
    }

    function finishAndReload() {
        window.location.reload();
    }
</script>
@endsection
