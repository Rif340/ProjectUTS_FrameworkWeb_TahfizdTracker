<p align="center">
  <img src="docs/readme_assets/named/01_banner.png" width="100%" alt="Banner Tahfizd Tracker SDIT At-Taubah">
</p>

<h1 align="center">📖 TAHFIZD TRACKER — SDIT AT-TAUBAH</h1>

<p align="center">
  Sistem Informasi Presensi Berbasis QR Code & Geofencing GPS serta Pemantauan Setoran Hafalan Al-Qur'an Terintegrasi di Sekolah Islam Terpadu At-Taubah Karawang.
</p>

<p align="center">
  <a href="https://tahfizd-tracker.onrender.com/login">
    <img src="https://img.shields.io/badge/LIVE%20DEMO-VISIT%20WEBSITE-2ea44f?style=for-the-badge&logo=googlechrome&logoColor=white" alt="Live Demo">
  </a>
  <a href="https://www.figma.com/design/Hyl4yEbzTYDAygeNEAQL4K/UI-UX-SDIT-AT-TAUBAH?m=auto&t=AkddhqMChH5eooue-1">
    <img src="https://img.shields.io/badge/FIGMA-DESIGN%20PROTOTYPE-F24E1E?style=for-the-badge&logo=figma&logoColor=white" alt="Figma Prototype">
  </a>
</p>

---

# 👥 MEET THE TEAM

### Kelompok 2 — Framework Pemrograman Web
**Dosen Pengampu**: Bapak Dadang Yusup, ST., M.Kom.

| No | Nama | NPM |
|:---:|---|---|
| 1 | **M Arif Wicaksono** | 2410631170034 |
| 2 | **Helmi Zain F.D** | 2410631170123 |
| 3 | **Kharisma Teguh W** | 2410631170125 |

---

# 📖 LATAR BELAKANG

Sekolah Islam Terpadu (IT) At-Taubah Karawang merupakan salah satu institusi pendidikan dasar swasta yang memadukan kurikulum pendidikan nasional dengan program pembinaan keislaman yang menitikberatkan pada program **tahfizd Al-Qur'an**. Program tahfizd di At-Taubah bertujuan membimbing setiap peserta didik agar mampu membaca Al-Qur'an secara fasih sesuai kaidah tajwid serta mencapai target hafalan juz dan surat yang telah ditetapkan.

Untuk memastikan proses pembelajaran berjalan optimal, pihak sekolah memerlukan pengawasan yang ketat terhadap dua hal pokok:
1. **Kedisiplinan Kehadiran (Presensi)** guru dan murid.
2. **Evaluasi Capaian Hafalan Harian** murid secara berkesinambungan.

### Permasalahan Presensi & Pencatatan Manual

Berdasarkan observasi langsung di lapangan, proses presensi kehadiran dan evaluasi hafalan sebelumnya masih dilakukan secara konvensional menggunakan lembaran kertas fisik (*Daftar Absen Kelas* dan lembar setoran). Guru harus memanggil satu per satu nama siswa serta menuliskan capaian surat/ayat secara manual. Hal ini memakan waktu jam pelajaran efektif, berisiko kehilangan lembaran fisik, serta menyulitkan rekapitulasi nilai di akhir semester.

<p align="center">
  <img src="docs/readme_assets/named/02_rekap_absen_manual.png" width="45%" alt="Lembar Rekapitulasi Absen Manual SDIT At-Taubah">
</p>

---

## 🎯 Rumusan Masalah

1. Bagaimana merancang aplikasi pengelolaan setoran hafalan Al-Qur'an dan presensi kehadiran berbasis **Quick Response Code (QR Code)** serta **GPS Geofencing** di Sekolah Islam At-Taubah berbasis web menggunakan framework Laravel?
2. Bagaimana melakukan pengujian kelayakan aplikasi dalam membantu Guru Tahfizd dalam pencatatan setoran hafalan siswa serta rekapitulasi kehadiran maupun nilai akhir?

---

## 🚧 Batasan Masalah

1. Aplikasi yang dibangun adalah sistem pengelolaan tahfizd Al-Qur'an dan presensi kehadiran siswa serta guru di Sekolah Islam At-Taubah.
2. Aplikasi berbasis web dengan integrasi fitur pemindaian **Quick Response Code (QR Code)** dan validasi radius lokasi (**GPS Geofencing**).
3. Dibangun menggunakan bahasa pemrograman PHP 8.4, framework Laravel 12, dan relational database MySQL.
4. Metode pengembangan perangkat lunak menggunakan **System Development Life Cycle (SDLC) model Prototyping**.

---

## 💡 Tujuan & Manfaat

- **Membantu Guru Tahfizd**: Pencatatan setoran hafalan murid terdata secara terkomputerisasi, rapi, dan terhindar dari risiko kehilangan berkas fisik.
- **Efisiensi Rekapitulasi**: Pembuatan laporan rekapitulasi presensi dan nilai capaian hafalan otomatis per kelas tanpa perlu rekap manual (*double work*).
- **Kemandirian Siswa**: Siswa dapat melakukan presensi mandiri dengan memindai kode QR kartu mereka dan memantau riwayat hafalan secara transparan.

---

# 🗣️ WAWANCARA STAKEHOLDER

## 👨‍🏫 Profil Tempat & Narasumber

- **Nama Sekolah**: Sekolah Islam At-Taubah Karawang
- **Alamat**: Jl. KH Ahmad Dahlan No. E/4 Kaum 1, Kec. Karawang Barat, Kab. Karawang, Jawa Barat
- **Narasumber**: Bapak Sofyan Sorry, M.Pd. (Kepala Sekolah)
- **Waktu Observasi**: 24 September 2026

<p align="center">
  <img src="docs/readme_assets/named/03_dokumentasi_wawancara.png" width="70%" alt="Dokumentasi Wawancara dengan Kepala Sekolah SDIT At-Taubah">
</p>

### Transkrip Inti Wawancara

> **Pewawancara (Kelompok 2):**  
> *"Assalamu’alaikum, Pak. Kami berencana merancang dan mengembangkan aplikasi presensi dan pemantauan hafalan Al-Qur’an di Sekolah Islam At-Taubah. Selama ini pencatatan setoran dan presensi masih manual menggunakan kertas sehingga cukup menyulitkan guru dalam mengelola data. Menurut Bapak, bagaimana jika aplikasi seperti ini dikembangkan dan diterapkan di sekolah?"*

> **Bapak Sofyan Sorry, M.Pd. (Kepala Sekolah):**  
> *"Wa'alaikumsalam wr. wb. Menurut saya itu ide yang sangat bagus dan memang sangat dibutuhkan di sekolah saat ini. Kendala kami selama ini memang seperti itu, pencatatan setoran hafalan masih manual di kertas sehingga rawan hilang dan kami repot saat merekap nilai di akhir semester. Ditambah lagi absensi juga masih manual. Kalau ada satu aplikasi terpadu yang bisa mencatat setoran anak sekaligus mengurus presensi guru dan murid tanpa cetak kertas lagi, tentu ini sangat meringankan tugas kami."*

---

# 📊 ANALISIS KEBUTUHAN SISTEM

## 📌 Kebutuhan Fungsional

<p align="center">
  <img src="docs/readme_assets/named/04_analisis_kebutuhan_fungsional.png" width="95%" alt="Kebutuhan Fungsional Sistem">
</p>

## 🔐 Kebutuhan Non-Fungsional

<p align="center">
  <img src="docs/readme_assets/named/05_analisis_kebutuhan_nonfungsional.png" width="95%" alt="Kebutuhan Non Fungsional Sistem">
</p>

---

# 👥 IDENTIFIKASI AKTOR & HAK AKSES

| Aktor | Deskripsi Hak Akses |
|---|---|
| **Administrator** | Manajemen data master pengguna (Admin, Guru, Murid), Tahun Akademik, Hari Kerja, Hari Libur, Pengaturan Radius & Titik Koordinat Presensi GPS, serta monitoring seluruh riwayat kehadiran. |
| **Guru Tahfizd** | Melakukan absensi masuk/pulang mandiri (QR & GPS), mengelola kelas bimbingan, menentukan target hafalan juz/surat, mencatat setoran hafalan murid, serta mencetak rekapitulasi setoran & nilai. |
| **Murid** | Melakukan absensi masuk/pulang mandiri melalui pemindaian QR Code dan verifikasi GPS, melihat riwayat absensi pribadi, serta memantau progres hafalan Al-Qur'an yang telah disetorkan. |

---

# 🧩 UML DIAGRAM

## 📌 Use Case Diagram

<p align="center">
  <img src="docs/readme_assets/named/06_usecase_diagram_umum.png" width="48%" alt="Use Case Diagram Umum">
  <img src="docs/readme_assets/named/06_usecase_diagram_guru.png" width="48%" alt="Use Case Diagram Guru">
</p>
<p align="center">
  <img src="docs/readme_assets/named/06_usecase_diagram_murid.png" width="48%" alt="Use Case Diagram Murid">
  <img src="docs/readme_assets/named/06_usecase_diagram_admin.png" width="48%" alt="Use Case Diagram Admin">
</p>

---

## 🗃️ Entity Relationship Diagram (ERD)

<p align="center">
  <img src="docs/readme_assets/named/07_erd_database.png" width="95%" alt="Entity Relationship Diagram (ERD) Database">
</p>

---

# 🎨 PERBANDINGAN WIREFRAME & UI/UX DESIGN

### 1. Halaman Login
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/08_wireframe_login.png" width="100%"> | <img src="docs/readme_assets/named/08_uiux_login.png" width="100%"> |

### 2. Presensi Kehadiran (QR & GPS)
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/09_wireframe_absensi_kehadiran.png" width="100%"> | <img src="docs/readme_assets/named/09_uiux_absensi_kehadiran.png" width="100%"> |

### 3. Detail & Riwayat Presensi
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/10_wireframe_detail_absensi.png" width="100%"> | <img src="docs/readme_assets/named/10_uiux_detail_absensi.png" width="100%"> |

### 4. Pengaturan Radius & Lokasi Sekolah (Geofencing)
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/11_wireframe_setting_radius_jarak.png" width="100%"> | <img src="docs/readme_assets/named/11_uiux_setting_radius_jarak.png" width="100%"> |

### 5. Manajemen Data Murid
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/12_wireframe_detail_murid.png" width="100%"> | <img src="docs/readme_assets/named/12_uiux_detail_murid.png" width="100%"> |

### 6. Kelas Tahfizd & Target Hafalan
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/13_wireframe_kelas_tahfizd.png" width="100%"> | <img src="docs/readme_assets/named/13_uiux_kelas_tahfizd.png" width="100%"> |

### 7. Progres & Cetak Laporan Capaian Tahfizd
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/15_wireframe_progress_murid.png" width="100%"> | <img src="docs/readme_assets/named/15_uiux_progress_murid.png" width="100%"> |
| <img src="docs/readme_assets/named/16_wireframe_cetak_laporan.png" width="100%"> | <img src="docs/readme_assets/named/16_uiux_cetak_laporan.png" width="100%"> |

### 8. Pengaturan Kalender (Hari Kerja & Hari Libur)
| Wireframe | UI/UX Final |
|:---:|:---:|
| <img src="docs/readme_assets/named/17_wireframe_hari_kerja.png" width="100%"> | <img src="docs/readme_assets/named/17_uiux_hari_kerja.png" width="100%"> |
| <img src="docs/readme_assets/named/18_wireframe_hari_libur.png" width="100%"> | <img src="docs/readme_assets/named/18_uiux_hari_libur.png" width="100%"> |

### 9. Kelola Pengguna & Tahun Akademik
| Kelola Pengguna | Kelola Tahun Akademik |
|:---:|:---:|
| <img src="docs/readme_assets/named/19_uiux_kelola_pengguna.png" width="100%"> | <img src="docs/readme_assets/named/20_uiux_kelola_tahun_akademik.png" width="100%"> |

---

# 🔑 AKUN TESTING (DEMO CREDENTIALS)

Untuk mempermudah proses evaluasi, pengujian, dan penilaian aplikasi, gunakan akun resmi hasil seed database berikut:

| No | Nama Lengkap | Role | Email | NIP / NISN | Password |
|:---:|---|:---:|---|:---:|:---:|
| 1 | **Ino S.Kom** | **Admin** | `ino@gmail.com` | `1234` | `admin123` |
| 2 | **OlaOla** | **Admin** | `Ola@gmail.com` | `7890` | `admin123` |
| 3 | **Belinda Blanche, S.Kom** | **Guru** | `belindablache@gmail.com` | `0001` | `guru123` |
| 4 | **Gibran Subianto** | **Murid** | `gibransubianto@gmail.com` | `0003` | `murid123` |
| 5 | **Idrian Lionberg** | **Murid** | `idrianlionberg@gmail.com` | `0002` | `murid123` |
| 6 | **Yuri Briol** | **Murid** | `YuriBriol@gmail.com` | `0007` | `murid123` |
| 7 | **Logan Mclain** | **Murid** | `LoganMclain@gmail.com` | `0004` | `murid123` |
| 8 | **Knox Von Reinhafer** | **Murid** | `siapaaja@gmail.com` | `0006` | `murid123` |

---

# 🛠️ TECH STACK

- **Backend**: Laravel 12 (PHP 8.4)
- **Database**: MySQL 8.4 (Cloud Managed on Aiven.io)
- **Frontend**: Blade Templating, Vanilla CSS Responsive, Leaflet.js (Peta GPS Geofencing), HTML5-QRCode Scanner
- **Deployment & Cloud**: Render.com (Docker Alpine Linux Container)
- **Automation / Workflow**: n8n Webhook WhatsApp Notification

---

# 📂 STRUKTUR DIREKTORI PROYEK

```text
TahfizdTracker/
├── app/
│   ├── Http/Controllers/      # Absensi, Murid, Tahfizd, Pengguna, Profile, dll.
│   ├── Models/                # User, Admin, Guru, Murid, Kelas, TargetHafalan, dll.
│   └── Services/              # NotificationService (Integrasi WA Webhook)
├── bootstrap/                 # Application bootstrap & middleware
├── config/                    # Konfigurasi database, session, cache, app
├── database/
│   ├── migrations/            # Skema tabel database
│   └── seeders/               # Data dummy pengguna & master data
├── docs/
│   └── readme_assets/         # Aset gambar dokumentasi & diagram
├── public/
│   ├── css/                   # Stylesheet responsif per modul
│   ├── icon/                  # Aset ikon SVG & gambar
│   └── font/                  # Tipografi lokal
├── resources/
│   └── views/                 # Blade views (layouts, absensi, tahfizd, siswa, dll.)
├── routes/
│   └── web.php                # Rute web aplikasi
├── Dockerfile                 # Konfigurasi container deploy cloud
├── docker-entrypoint.sh       # Script inisialisasi & migrasi otomatis
└── render.yaml                # Spesifikasi deployment Render.com
```

---

# ▶️ CARA MENJALANKAN DI KOMPUTER LOKAL

### 1️⃣ Clone Repository
```bash
git clone https://github.com/Rif340/ProjectUTS_FrameworkWeb_TahfizdTracker.git
cd ProjectUTS_FrameworkWeb_TahfizdTracker
```

### 2️⃣ Install Dependensi Composer
```bash
composer install
```

### 3️⃣ Konfigurasi Environment File
Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi koneksi database MySQL:
```bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ Migrasi & Seeding Database
```bash
php artisan migrate --seed
php artisan storage:link
```

### 5️⃣ Menjalankan Aplikasi
```bash
php artisan serve
```
Akses aplikasi melalui browser di alamat:
```text
http://localhost:8000
```

---

# 📌 STATUS PROYEK

✅ **Production Ready & Deployed**

Aplikasi dapat diakses secara langsung di cloud:
👉 **[https://tahfizd-tracker.onrender.com/login](https://tahfizd-tracker.onrender.com/login)**

---

# 🙏 TERIMA KASIH

Proyek ini dirancang dan dibangun oleh **Kelompok 2** untuk memenuhi Tugas Proyek Ujian Tengah Semester (UTS) mata kuliah **Framework Pemrograman Web**, Program Studi Informatika, Fakultas Ilmu Komputer, Universitas Singaperbangsa Karawang.
