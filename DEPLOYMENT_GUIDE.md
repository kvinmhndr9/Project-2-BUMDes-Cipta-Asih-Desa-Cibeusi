# Panduan Deploy Online Menggunakan Cloudflare Tunnel (Windows)

Dokumen ini berisi panduan langkah-demi-langkah untuk membuat aplikasi **SI-ASIH** yang berjalan di server Windows lokal Anda dapat diakses secara online menggunakan domain dari **Domainesia** dan **Cloudflare Tunnel** (gratis, aman, & otomatis SSL HTTPS).

---

## 📋 Prasyarat
1. Domain aktif yang terdaftar di **Domainesia** (misal: `domainkamu.com`).
2. Akun **Cloudflare** gratis.
3. Aplikasi SI-ASIH sudah berjalan normal secara lokal di port `8000` (`php artisan serve`).

---

## 🛠 Langkah 1: Hubungkan Domain Domainesia ke Cloudflare

1. Masuk ke dashboard [Cloudflare](https://dash.cloudflare.com/) Anda.
2. Klik **Add a Site** > ketikkan nama domain Anda (`domainkamu.com`) > Pilih paket **Free**.
3. Cloudflare akan melakukan pemindaian DNS otomatis dan memberikan **2 Nameservers** baru. Contoh:
   - `dave.ns.cloudflare.com`
   - `lola.ns.cloudflare.com`
4. Login ke [Client Area Domainesia](https://my.domainesia.com/).
5. Buka menu **Domain** > Klik domain Anda > Pilih tab **Nameservers**.
6. Pilih **Use custom nameservers** dan ubah Nameserver bawaan Domainesia menjadi Nameserver baru dari Cloudflare.
7. Klik **Save / Change Nameservers**.
   > **Catatan:** Proses propagasi pergantian DNS ini memerlukan waktu sekitar 15 menit hingga 24 jam (biasanya aktif dalam kurang dari 1 jam).

---

## 💻 Langkah 2: Install Cloudflared di Windows

1. Unduh installer `cloudflared-windows-amd64.msi` melalui link rilis resmi [Cloudflare GitHub Release](https://github.com/cloudflare/cloudflared/releases).
2. Jalankan installer dan selesaikan proses instalasinya.
3. Untuk memastikan `cloudflared` terpasang dengan benar, buka **PowerShell** sebagai Administrator dan jalankan:
   ```powershell
   cloudflared --version
   ```

---

## ⚙ Langkah 3: Konfigurasi Cloudflare Tunnel

1. **Autentikasi Cloudflared**:
   Jalankan perintah ini di PowerShell/CMD:
   ```powershell
   cloudflared tunnel login
   ```
   Browser akan terbuka secara otomatis. Login ke akun Cloudflare Anda, pilih domain yang Anda tambahkan tadi, lalu klik **Authorize**.

2. **Buat Tunnel Baru**:
   Buat terowongan aman (tunnel) bernama `siasih-tunnel`:
   ```powershell
   cloudflared tunnel create siasih-tunnel
   ```
   *Salin dan catat **Tunnel ID** (UUID) yang dihasilkan pada output terminal.*

3. **Buat File Konfigurasi (`config.yml`)**:
   - Buka direktori `.cloudflared` di folder user Windows Anda (biasanya di `C:\Users\<NamaUser>\.cloudflared\`).
   - Buat file baru bernama `config.yml` di dalam direktori tersebut dan isi dengan kode berikut:
     ```yaml
     tunnel: <TUNNEL_ID_ANDA>
     credentials-file: C:\Users\<NamaUser>\.cloudflared\<TUNNEL_ID_ANDA>.json

     ingress:
       - hostname: domainkamu.com
         service: http://localhost:8000
       - service: http_status:404
     ```
     *(Ganti `<NamaUser>`, `<TUNNEL_ID_ANDA>`, dan `domainkamu.com` sesuai dengan komputer dan domain Anda).*

4. **Hubungkan DNS Domain ke Tunnel**:
   Jalankan perintah ini untuk membuat record CNAME otomatis di Cloudflare yang mengarah ke tunnel:
   ```powershell
   cloudflared tunnel route dns siasih-tunnel domainkamu.com
   ```

---

## 🚀 Langkah 4: Jalankan dan Daftarkan sebagai Service Windows

Agar terowongan koneksi ini selalu berjalan di latar belakang (background) dan otomatis menyala kembali ketika komputer di-restart:

1. **Pasang sebagai Windows Service**:
   ```powershell
   cloudflared service install
   ```
2. **Jalankan Service**:
   ```powershell
   Start-Service "Cloudflared"
   ```
3. **Pastikan Web Server Lokal Menyala**:
   Pastikan Anda selalu menjalankan command Laravel ini saat server lokal aktif:
   ```powershell
   php artisan serve --port=8000
   ```

---

## 🔒 Langkah 5: Penyesuaian Berkas `.env` Laravel

Buka file `.env` di folder proyek SI-ASIH Anda dan sesuaikan variabel berikut agar sistem verifikasi email dan pembayaran berfungsi secara online:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domainkamu.com

# URL Notifikasi Transaksi Midtrans
MIDTRANS_NOTIFICATION_URL=https://domainkamu.com/payment/notification
```

Setelah mengubah file `.env`, lakukan pembersihan cache konfigurasi Laravel:
```powershell
php artisan config:clear
php artisan route:clear
```
