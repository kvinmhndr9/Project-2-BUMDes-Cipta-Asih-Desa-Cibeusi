# SIASIH — Project-Specific Skill

> Skill khusus untuk project SIASIH (Sistem Informasi Administrasi Desa Cibeusi).
> Berisi arsitektur, konvensi, warna, role, dan aturan bisnis yang WAJIB diikuti.

## Tentang Sistem

**SIASIH** adalah Sistem Informasi Wisata berbasis web untuk BUMDes Cipta Asih, Desa Cibeusi.
- **Framework:** Laravel 10 (PHP 8.x)
- **Frontend:** Blade + Bootstrap 5 + Vanilla JS
- **SPA-like:** Turbo (Hotwired) — navigasi tanpa full reload
- **Payment:** Midtrans Snap API
- **Notifikasi:** WhatsApp Gateway (Node.js API terpisah)
- **Hosting:** PHP server + MySQL

## Arsitektur

### Struktur Folder Penting
```
app/
├── Http/Controllers/
│   ├── AuthController.php                     → Login, Register, Google OAuth
│   ├── EmailVerificationOtpController.php     → Verifikasi email OTP 6 digit
│   ├── ForgotPasswordController.php           → Lupa & reset password
│   ├── MidtransNotificationController.php     → Webhook pembayaran
│   ├── PublicController.php                   → Halaman publik (wisata, produk)
│   ├── ReviewController.php                   → Review/ulasan pengunjung
│   ├── Admin/
│   │   ├── AdminDashboardController.php       → Dashboard admin per wisata
│   │   ├── ValidasiTiketController.php        → Scan & validasi tiket (QR)
│   │   ├── LaporanAdminController.php         → Laporan pendapatan admin
│   │   └── AdminLaporanOfflineController.php  → Input penjualan offline
│   ├── Pengelola/
│   │   ├── PengelolaDashboardController.php   → Dashboard global pengelola
│   │   ├── WisataController.php               → CRUD wisata
│   │   ├── ProdukKhasController.php           → CRUD produk khas desa
│   │   ├── LaporanController.php              → Laporan global semua wisata
│   │   └── AkunAdminController.php            → Kelola akun admin
│   └── Pengunjung/
│       ├── TiketController.php                → Pesan, bayar, reschedule, batal tiket
│       └── ProfileController.php              → Profil & avatar pengunjung
├── Models/
│   ├── User.php              → 3 role: pengunjung, admin, pengelola_bumdes
│   ├── Wisata.php            → Destinasi wisata (harga, jam, hari buka)
│   ├── Tiket.php             → Tiket digital (status: pending→paid→used/cancelled)
│   ├── Review.php            → Ulasan & rating (1-5)
│   ├── ProdukKhas.php        → Produk khas desa
│   ├── PenjualanOffline.php  → Pencatatan tiket offline oleh admin
│   ├── WisataGallery.php     → Galeri foto wisata
│   └── ProdukKhasGallery.php → Galeri foto produk
├── Services/
│   ├── MidtransService.php   → createTransaction(), verifyNotification()
│   └── WhatsAppService.php   → sendMessage(), sendMedia(), isReady()
└── Http/Middleware/
    ├── CheckRole.php              → Cek role user (pengunjung/admin/pengelola)
    ├── EnsureHasWhatsApp.php      → Pastikan user punya no_hp sebelum pesan tiket
    ├── RedirectIfAdminOrPengelola.php → Redirect admin/pengelola ke dashboard
    ├── SecurityHeaders.php        → HTTP security headers
    └── VerifyMidtransIp.php       → Whitelist IP Midtrans untuk webhook
```

## Role & Akses

| Role | Akses | Dashboard |
|---|---|---|
| `pengunjung` | Pesan tiket, lihat wisata, review, profil | `/pengunjung` |
| `admin` | Validasi tiket (QR), laporan per wisata, penjualan offline | `/admin` |
| `pengelola_bumdes` | CRUD wisata, CRUD produk, laporan global, kelola admin | `/pengelola` |

### Middleware
- `role:pengunjung` → hanya pengunjung
- `role:admin` → hanya admin
- `role:pengelola_bumdes` → hanya pengelola
- `has_wa` → pengunjung harus punya no_hp sebelum pesan tiket
- `midtrans.ip` → whitelist IP Midtrans untuk webhook
- `pengunjung_only` → redirect admin/pengelola ke dashboard mereka

## Database — Primary Key Convention

⚠️ **PENTING:** Project ini TIDAK menggunakan `id` standar Laravel.

| Model | Primary Key | Tabel |
|---|---|---|
| User | `id_user` | `users` |
| Wisata | `id_wisata` | `wisatas` |
| Tiket | `id_tiket` | `tikets` |
| Review | `id_ulasan` | `reviews` |
| ProdukKhas | `id_produk_khas` | `produk_khas` |
| PenjualanOffline | `id_penjualan_offline` | `penjualan_offlines` |
| WisataGallery | `id` | `wisata_galleries` |
| ProdukKhasGallery | `id` | `produk_khas_galleries` |

### Relasi Kunci
```
User (1) ←→ (0..*) Tiket          via id_user
User (0..*) ←→ (1) Wisata         via id_wisata (admin terhubung ke 1 wisata)
Wisata (1) ←→ (0..*) Tiket        via id_wisata
Wisata (1) ←→ (0..*) Review       via id_wisata
Wisata (1) ←→ (0..*) ProdukKhas   via id_wisata
Tiket (1) ←→ (0..1) Review        via id_tiket
```

## Alur Bisnis Tiket

```
1. Pengunjung pilih wisata → form pesan tiket
2. Submit form → Tiket dibuat (status: pending)
3. Halaman pembayaran → Midtrans Snap popup
4. Bayar berhasil → Midtrans kirim webhook → status: paid
   → WhatsApp kirim e-tiket (QR Code) ke pengunjung
5. Pengunjung datang → Admin scan QR → validasi → status: used
```

### Status Tiket
| Status | Makna |
|---|---|
| `pending` | Belum dibayar |
| `paid` | Sudah dibayar, belum digunakan |
| `used` | Sudah digunakan (sudah di-scan admin) |
| `cancelled` | Dibatalkan oleh pengunjung |

### Aturan Bisnis
- Tiket hanya bisa digunakan pada **tanggal_berkunjung** yang dipilih
- Reschedule: maksimal 2 kali (`reschedule_count`)
- Cancel: hanya bisa cancel tiket yang status `paid` dan belum melewati tanggal
- Wisata punya **hari_buka** (array hari) dan **tanggal_tutup** (array tanggal khusus)
- Beberapa wisata punya opsi **camping** dengan harga berbeda

## Design System — Warna & Gradien

### Warna Utama (WAJIB digunakan)
```
Primary:        #04009A  (Deep Navy)
Primary Dark:   #02006B
Primary Light:  #77ACF1  (Cornflower Blue)
Secondary:      #3EDBF0  (Cyan/Teal)
Accent:         #77ACF1
Light:          #C0FEFC  (Ice Blue)
Background:     #f0f4ff
```

### Gradien (WAJIB konsisten)
```
Navbar/Footer:  linear-gradient(90deg, #04009A 0%, #3EDBF0 100%)
Sidebar:        linear-gradient(180deg, #02006B 0%, #04009A 60%, #0600c0 100%)
Hero:           linear-gradient(-45deg, #04009A, #3EDBF0, #77ACF1, #C0FEFC)
Card Top:       linear-gradient(90deg, #04009A, #77ACF1, #3EDBF0, #C0FEFC)
Button Primary: linear-gradient(45deg, #04009A, #3EDBF0)
```

### Typography
- **Font utama:** Poppins (semua elemen)
- **Font weight:** 400 (body), 600 (label), 700 (subtitle), 800 (heading/title)

### Border Radius
- Card: 20px
- Button: 50px (pill shape)
- Input: 10px
- Badge: 50px

## Styling Rules

1. **CSS utama** ada di `resources/sass/app.scss` — jangan buat file SCSS baru tanpa alasan
2. **Page-specific CSS** boleh di `@push('styles')` di blade — untuk style yang hanya dipakai 1 halaman
3. **JANGAN** override warna yang sudah ada di `app.scss` dengan warna berbeda
4. **JANGAN** ubah font dari Poppins ke font lain
5. Semua card SUDAH punya `::before` gradient bar di atas — jangan tambah lagi secara manual
6. Tombol `btn-primary` SUDAH punya gradient dan hover effect — jangan override

## JavaScript Rules (Turbo)

Karena menggunakan **Turbo** (SPA-like navigation):

1. **JANGAN** pakai `DOMContentLoaded` — gunakan `turbo:load`
2. **SELALU** gunakan guard pattern untuk mencegah duplikasi listener:
```js
function init() {
    var el = document.getElementById('target');
    if (!el || el.dataset.ready) return;
    el.dataset.ready = '1';
    // ... tambah listener
}
document.addEventListener('turbo:load', init);
```
3. **SELALU** tambahkan `setTimeout(init, 100)` sebagai fallback
4. Bootstrap modal harus di-dispose sebelum Turbo cache:
```js
document.addEventListener('turbo:before-cache', function() {
    document.querySelectorAll('.modal.show').forEach(function(m) {
        var inst = bootstrap.Modal.getInstance(m);
        if (inst) inst.hide();
    });
    document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
});
```

## File Upload Convention

- Upload path: `storage/app/public/` → diakses via `storage` symlink
- Gambar wisata: `wisata/`
- Gambar produk: `produk-khas/`
- Avatar user: `avatars/`
- Gunakan `$model->getGambarUrlAttribute()` untuk URL publik

## Environment Keys (di .env)

```
MIDTRANS_SERVER_KEY=...
MIDTRANS_CLIENT_KEY=...
MIDTRANS_IS_PRODUCTION=false

WA_GATEWAY_URL=http://localhost:3001
```
