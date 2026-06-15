# 🌿 SIASIH (Sistem Informasi BUMDes Cipta Asih Desa Cibeusi)

[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-^8.1-777BB4?style=for-the-badge&logo=php)](https://www.php.net)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite)](https://vite.dev)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE)

**SIASIH** adalah platform e-ticketing terpadu yang dirancang untuk modernisasi sistem manajemen tiket dan keuangan di destinasi wisata Desa Cibeusi yang dikelola oleh **BUMDes Cipta Asih**. Aplikasi ini mempermudah pengunjung untuk memesan tiket secara online, menyediakan integrasi pembayaran instan (*payment gateway*), serta memberikan panel kendali yang terperinci bagi admin wisata lokal dan pengelola BUMDes untuk pemantauan pendapatan secara real-time.

---

## 🚀 Fitur Utama

- **Pemesanan Tiket Online:** Pengunjung dapat memilih destinasi wisata (seperti Curug Cibareubeuy, Puncak Pasir Ipis, Spot Foto Bukit Panineungan) dan membeli tiket secara instan.
- **Integrasi Midtrans Payment Gateway:** Mendukung berbagai metode pembayaran otomatis (Bank Transfer, E-wallet, dll.) dengan pembaruan status real-time melalui webhook.
- **E-Ticket & QR Code Generator:** Setiap tiket sukses akan mendapatkan kode unik dan QR Code otomatis yang digunakan untuk validasi di pintu masuk.
- **Validasi Tiket via Scan QR:** Pintu masuk (Gate) dikelola oleh Admin Wisata dengan fitur scan QR Code tiket pengunjung secara langsung.
- **Dasbor Multi-Role:**
  - **Pengunjung:** Memesan tiket, melihat daftar pembelian tiket, dan mengakses QR Code tiket mereka.
  - **Admin Wisata:** Mengelola tiket masuk destinasi wisata spesifik, memvalidasi tiket masuk, dan memantau pendapatan harian di lokasi wisatanya.
  - **Pengelola BUMDes:** Panel kontrol utama untuk melihat ringkasan pendapatan dari seluruh objek wisata secara terintegrasi dan laporan keuangan terperinci.
- **Autentikasi Ganda:** Login konvensional (Email & Password) dan integrasi login instan dengan **Google OAuth**.

---

## 🛠️ Tech Stack & Dependensi

* **Framework Utama:** Laravel 10.x
* **Bahasa Pemrograman:** PHP ^8.1 / 8.2
* **Database:** MySQL / MariaDB
* **Frontend:** Bootstrap 5.3, Sass, Vite, Hotwire Turbo, & NProgress
* **Paket Tambahan (Packages):**
  - `midtrans/midtrans-php` — Integrasi Payment Gateway Midtrans
  - `laravel/socialite` — Integrasi Google OAuth Login
  - `simplesoftwareio/simple-qrcode` — Pembuat QR Code otomatis
  - `barryvdh/laravel-dompdf` — Cetak PDF Laporan
  - `cloudinary-labs/cloudinary-laravel` — Penyimpanan media berbasis Cloud (Opsional)

---

## 📋 Prasyarat Sistem (Prerequisites)

Pastikan lingkungan lokal Anda sudah terpasang perangkat lunak berikut:
- **PHP** versi `8.1` atau `8.2`
- **Composer** (Manajer Dependensi PHP)
- **Node.js** (rekomendasi LTS terbaru) dan **npm**
- **MySQL / MariaDB**

---

## ⚙️ Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk memasang aplikasi SIASIH di komputer lokal Anda:

### 1. Klon Repositori
```bash
git clone https://github.com/magicvinz/Project-2-BUMDes-Cipta-Asih-Desa-Cibeusi.git siasih-app
cd siasih-app
```

### 2. Pasang Dependensi PHP & JavaScript
```bash
# Pasang paket Composer
composer install

# Pasang paket NPM
npm install
```

### 3. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` di editor teks Anda dan sesuaikan kredensial database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siasih_db
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### 4. Membuat Aplikasi Key
```bash
php artisan key:generate
```

### 5. Setup Database Otomatis
Jalankan perintah kustom bawaan SIASIH berikut untuk membuat database secara otomatis, menjalankan migrasi tabel, dan mengisi data dasar (*seeding*):
```bash
php artisan siasih:setup-database
```

### 6. Jalankan Server Development
Buka dua jendela terminal terpisah dan jalankan perintah berikut:

* **Terminal 1 (Server PHP/Laravel):**
  ```bash
  php artisan serve
  ```
* **Terminal 2 (Compiler Frontend Vite):**
  ```bash
  npm run dev
  ```

Buka browser Anda dan akses aplikasi di: `http://localhost:8000`

---

## 🔑 Akun Demo (Data Awal / Seeders)

Setelah menjalankan `siasih:setup-database`, Anda dapat masuk menggunakan akun demo berikut (semua password adalah `password`):

| Role | Nama Pengguna | E-mail |
|------|---------------|--------|
| **Pengelola BUMDes** | Pengelola BUMDes | `pengelola@siasih.com` |
| **Admin Curug Cibareubeuy** | Admin Curug | `admin.curug@siasih.com` |
| **Admin Pasir Ipis** | Admin Puncak | `admin.puncak@siasih.com` |
| **Admin Bukit Panineungan** | Admin Bukit | `admin.bukit@siasih.com` |
| **Pengunjung** | Pengunjung Demo | `pengunjung@siasih.com` |

---

## 📖 Indeks Dokumentasi Sistem

Proyek ini dilengkapi dengan dokumentasi internal lengkap untuk mempermudah pemahaman arsitektur dan pengembangan lanjutan. Klik tautan berikut untuk membaca detailnya:

* **Panduan Pengoperasian & Pengembangan:**
  - 🌐 [Panduan Deploy Online (Cloudflare Tunnel)](DEPLOYMENT_GUIDE.md) — Langkah mengonlinekan server lokal secara gratis & aman.
  - 🗄️ [Panduan Setup Database Manual](docs/DATABASE_SETUP.md) — Jika Anda ingin memigrasi database secara manual.
  - 🗺️ [Struktur Lengkap File Proyek](docs/STRUKTUR-PROYEK-SIASIH.md) — Analogi restoran dan fungsi setiap file di Laravel.
  - 🔧 [Integrasi Google Login & QR Code](docs/LOGIN_GOOGLE_DAN_QR.md) — Cara setup API Google Console dan QR Code.
  - 🚨 [Troubleshooting Validasi QR Code](docs/TROUBLESHOOTING_QR.md) — Penanganan kendala pada scanner tiket.

* **Dokumentasi Analisis & Desain (Design Documents):**
  - 📊 [Alur Proses Bisnis SIASIH](docs/PROSES_BISNIS_SIASIH.md) — Penjelasan detail use case, aturan bisnis, dan siklus tiket.
  - 🔗 [Desain Entity Relationship Diagram (ERD)](docs/ERD-SIASIH.md) — Detail relasi tabel di database.
  - 🔄 [Diagram Urutan (Sequence Diagrams)](docs/sequence-diagrams.md) — Alur interaksi objek saat Login, Transaksi, & Validasi.
