# Security Hardening & Best Practices

> Skill keamanan komprehensif — mencakup OWASP Top 10, autentikasi, dan proteksi berlapis.

## OWASP Top 10 — Checklist Wajib

### 1. Injection (SQL, NoSQL, OS Command)
```php
// ❌ RENTAN — user input langsung di query
$result = DB::select("SELECT * FROM users WHERE email = '$email'");

// ✅ AMAN — prepared statement / Eloquent
$result = User::where('email', $email)->first();
$result = DB::select("SELECT * FROM users WHERE email = ?", [$email]);
```

### 2. Broken Authentication
- Password hash: `bcrypt` / `argon2` (BUKAN MD5/SHA1)
- Session timeout: jangan infinite
- Rate limiting di login: `throttle:5,1` (5 percobaan per menit)
- Regenerate session ID setelah login
- Logout harus menghancurkan session

### 3. Sensitive Data Exposure
- HTTPS di production (wajib)
- Jangan simpan password/token di log
- `$hidden` di Eloquent model untuk field sensitif
- `.env` di `.gitignore` — JANGAN pernah commit
- Response API tidak boleh expose stack trace di production

### 4. XML External Entities (XXE)
- Disable external entity loading di XML parser
- Prefer JSON over XML untuk API

### 5. Broken Access Control
```php
// ❌ RENTAN — user bisa akses data orang lain
Route::get('/tiket/{id}', function ($id) {
    return Tiket::findOrFail($id); // siapapun bisa akses!
});

// ✅ AMAN — pastikan tiket milik user yang login
Route::get('/tiket/{id}', function ($id) {
    return Tiket::where('id_user', auth()->id())->findOrFail($id);
});
```

### 6. Security Misconfiguration
- `APP_DEBUG=false` di production
- `APP_ENV=production` di production
- Hapus route debug / test sebelum deploy
- Disable directory listing di web server
- Update dependency secara berkala

### 7. Cross-Site Scripting (XSS)
```php
// ❌ RENTAN — output tanpa escape
{!! $userInput !!}

// ✅ AMAN — output escaped
{{ $userInput }}

// ✅ Jika perlu HTML, sanitize dulu
{!! clean($userInput) !!} // pakai HTMLPurifier
```

### 8. Insecure Deserialization
- Jangan `unserialize()` dari user input
- Validasi dan sanitize data sebelum proses

### 9. Using Components with Known Vulnerabilities
```bash
# Cek vulnerability di dependencies
composer audit          # PHP
npm audit               # Node.js
```

### 10. Insufficient Logging & Monitoring
- Log semua login gagal
- Log perubahan data sensitif
- Monitor error rate di production
- Setup alert untuk anomali

## Header Security

```php
// SecurityHeaders Middleware — sudah ada di project
// Pastikan header berikut aktif:
'X-Content-Type-Options'  => 'nosniff'
'X-Frame-Options'         => 'SAMEORIGIN'
'X-XSS-Protection'        => '1; mode=block'
'Referrer-Policy'          => 'strict-origin-when-cross-origin'
'Permissions-Policy'       => 'camera=(), microphone=(), geolocation=()'
```

## File Upload Security

```php
// Checklist file upload:
// 1. Validasi MIME type (bukan hanya extension)
$request->validate([
    'foto' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
]);

// 2. Simpan di storage (bukan public langsung)
$path = $request->file('foto')->store('uploads', 'public');

// 3. Rename file — jangan pakai nama asli user
// Laravel sudah auto-generate nama unik

// 4. Batasi ukuran di php.ini DAN di validasi
// upload_max_filesize = 5M
// post_max_size = 8M
```

## Payment Security (Midtrans)

```
1. Server Key HANYA di backend (.env) — JANGAN expose ke frontend
2. Verifikasi notification signature sebelum update status
3. Whitelist IP Midtrans di middleware
4. Validasi order_id cocok dengan data di database
5. Cek amount di notification = amount di database (prevent tampering)
6. Idempotency: handle duplicate notification dengan aman
```

## Checklist Pre-Production

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS aktif
- [ ] CSRF protection aktif
- [ ] Rate limiting di endpoint sensitif
- [ ] Security headers aktif
- [ ] File `.env` tidak accessible via web
- [ ] Storage directory permissions benar (755 folder, 644 file)
- [ ] Composer/npm dependencies updated
- [ ] `composer audit` dan `npm audit` bersih
- [ ] Error page custom (tidak expose stack trace)
- [ ] Backup database rutin terjadwal
