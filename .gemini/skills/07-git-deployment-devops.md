# Git, Deployment & DevOps Standards

> Skill untuk version control, deployment, dan workflow pengembangan.

## Git

### Commit Message
Format: `type: deskripsi singkat`

| Type | Kapan digunakan |
|---|---|
| `feat` | Fitur baru |
| `fix` | Perbaikan bug |
| `refactor` | Perubahan kode tanpa ubah fungsionalitas |
| `style` | Perubahan styling/formatting |
| `docs` | Dokumentasi |
| `chore` | Build, dependency, config |
| `test` | Menambah/mengubah test |

Contoh:
```
feat: tambah fitur pemesanan tiket camping
fix: harga tidak update saat ganti jenis kunjungan
style: redesign form pemesanan tiket
refactor: pisahkan logic pembayaran ke MidtransService
```

### Branch Strategy
```
main        → production (stabil)
develop     → staging/integration
feature/*   → fitur baru (dari develop)
fix/*       → perbaikan bug
hotfix/*    → perbaikan urgent langsung ke main
```

### Aturan
- Jangan commit file yang tidak seharusnya: `.env`, `node_modules/`, `vendor/`, `storage/logs/`
- Pastikan `.gitignore` sudah benar sebelum commit pertama
- Jangan force push ke `main` atau `develop`
- Commit sering dengan pesan yang jelas — jangan 1 commit raksasa

## Deployment Checklist

### Pre-Deploy
- [ ] Semua test pass
- [ ] `.env` production sudah dikonfigurasi
- [ ] `APP_DEBUG=false` di production
- [ ] `APP_ENV=production`
- [ ] Database migration aman (backward compatible)
- [ ] File statis sudah di-build (`npm run build`)
- [ ] Tidak ada `dd()`, `console.log()`, atau debug code

### Laravel Deploy
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

### Node.js Deploy
```bash
npm ci --production
npm run build
```

### Post-Deploy
- [ ] Cek halaman utama load tanpa error
- [ ] Cek login/signup berfungsi
- [ ] Cek fitur critical (pembayaran, dll)
- [ ] Monitor error log 15 menit pertama

## Environment Variables

### Aturan Ketat
1. **JANGAN PERNAH** hardcode API key, secret, atau password di kode
2. **JANGAN PERNAH** commit file `.env` ke git
3. Gunakan `.env.example` dengan nilai placeholder
4. Validasi env vars saat boot — gagal cepat lebih baik dari error di runtime

### Contoh .env.example
```env
APP_NAME=NamaAplikasi
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db
DB_USERNAME=root
DB_PASSWORD=

MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
```

## Security Checklist Umum

- [ ] HTTPS di production
- [ ] CSRF protection aktif
- [ ] SQL Injection: gunakan parameterized queries / ORM
- [ ] XSS: escape output, gunakan CSP header
- [ ] File upload: validasi tipe, ukuran, dan rename
- [ ] Password: hash, never plain text
- [ ] Rate limiting di endpoint login/register
- [ ] Session timeout yang wajar
- [ ] Error message tidak expose informasi internal di production
