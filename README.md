# 📖 Tahfizd Tracker - SDIT At-Taubah

Aplikasi web manajemen tahfizd, monitoring setoran hafalan Al-Qur'an, dan presensi berbasis QR Code serta Geofencing GPS untuk SDIT At-Taubah.  
Dibangun menggunakan framework **Laravel 12 (PHP 8.4)** dengan dukungan live deployment di cloud.

---

## 🌐 Demo & Live Deployment

Aplikasi sudah dapat diakses secara publik dengan sertifikat HTTPS (mendukung fitur kamera pemindaian QR Code & GPS Geolocation di smartphone):

- **URL Aplikasi**: [https://tahfizd-tracker.onrender.com](https://tahfizd-tracker.onrender.com)

---

## 🔑 Akun Testing (Demo Credentials)

Gunakan akun-akun berikut untuk menguji masing-masing role:

| Role | Nama Pengguna | NIP / NISN | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Admin** | **Ino S.Kom** | **NIP:** `1234` | `ino@gmail.com` | `admin123` | Manajemen Master Data, Guru, Siswa, Kelas, Lokasi Presensi GPS, Tahun Akademik |
| **Guru** | **Belinda Blanche, S.Kom** | **NIP:** `0001` | `belindablache@gmail.com` | `guru123` | Input Setoran Hafalan, Monitoring Target Hafalan, Rekap & Cetak Nilai |
| **Murid** | **Budi Siregar** | **NISN:** `1001` | `budisiregar@gmail.com` | `murid123` | Presensi Mandiri (Scan QR & GPS), Lihat Progres Hafalan Pribadi, Riwayat Absensi |

---

## ✨ Fitur Unggulan

1. **Presensi QR Code Real-Time**:
   - Validasi QR Code unik per pengguna.
   - Pengecekan kepemilikan akun agar tidak dapat saling memindai milik pengguna lain.
2. **Geofencing GPS Radius Absensi**:
   - Deteksi radius koordinat otomatis menggunakan peta Leaflet.js interaktif.
   - Presensi hanya dapat dilakukan jika berada di dalam batas radius sekolah yang telah ditentukan admin.
3. **Pencatatan & Validasi Setoran Hafalan**:
   - Monitoring capaian hafalan per juz/surat/ayat secara bertahap (sequential).
   - Validasi urutan ayat agar tidak ada loncatan setoran.
4. **Multi-Role User**:
   - Antarmuka dan otorisasi terpisah untuk **Admin**, **Guru**, dan **Murid**.
5. **Manajemen Tahun Akademik & Rekap Laporan**:
   - Filter data dinamis sesuai tahun akademik aktif.
   - Cetak rekapitulasi nilai dan presensi dalam format cetak/PDF.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.4)
- **Database**: MySQL 8.4 (Cloud Hosted on Aiven.io)
- **Frontend**: Blade Templating, Tailwind CSS, Leaflet.js, HTML5-QRCode
- **Deployment**: Render.com (Docker Container Alpine Linux)

---

## 💻 Menjalankan di Lokal (Local Development)

### 1. Clone Repository
```bash
git clone https://github.com/Rif340/ProjectUTS_FrameworkWeb_TahfizdTracker.git
cd ProjectUTS_FrameworkWeb_TahfizdTracker
```

### 2. Install Dependensi
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database MySQL:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi & Seed Database
```bash
php artisan migrate --seed
php artisan storage:link
```

### 5. Jalankan Server
```bash
php artisan serve
```
Akses di browser melalui: `http://localhost:8000`

---

## 👨‍💻 Author

- **Nama**: Muhammad Arif
- **GitHub**: [@Rif340](https://github.com/Rif340)
- **Proyek**: Tugas Proyek UTS Pemrograman Framework Web
