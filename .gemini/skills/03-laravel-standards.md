# Laravel Development Standards

> Skill untuk project Laravel (v8, v9, v10, v11+). Mencakup pattern, konvensi, dan best practices.

## Arsitektur

### Struktur Folder (Konvensi Laravel)
```
app/
├── Console/         → Artisan commands
├── Exceptions/      → Exception handlers
├── Http/
│   ├── Controllers/ → Grouped by role/feature
│   ├── Middleware/   → Custom middleware
│   └── Requests/    → Form Request validation (jika ada)
├── Models/          → Eloquent models
├── Notifications/   → Email/SMS/WA notifications
├── Providers/       → Service providers
└── Services/        → Business logic services
```

### Prinsip
- **Fat Model, Thin Controller.** Logic bisnis di Model atau Service, bukan di Controller.
- **Gunakan Eloquent.** Hindari raw SQL kecuali untuk query yang sangat kompleks.
- **Service class** untuk logic yang melibatkan multiple model atau external API.
- **Form Request** untuk validasi kompleks (jika project sudah menggunakannya).

## Model

### Konvensi
- Selalu definisikan `$table`, `$primaryKey`, `$fillable`, dan `$casts` secara eksplisit.
- Gunakan `$hidden` untuk field sensitif (password, token).
- Relationship method harus ditulis dengan nama yang deskriptif.
- Accessor/Mutator menggunakan `get{Attr}Attribute()` (Laravel <10) atau `Attribute::make()` (Laravel 10+). Ikuti versi project.

### Relasi
```php
// BelongsTo → singular
public function wisata() { return $this->belongsTo(Wisata::class, 'id_wisata'); }

// HasMany → plural
public function tikets() { return $this->hasMany(Tiket::class, 'id_wisata'); }

// HasOne → singular
public function review() { return $this->hasOne(Review::class, 'id_tiket'); }
```

### Scope
- Scope untuk query yang sering dipakai ulang.
- Naming: `scope{Name}` → dipanggil sebagai `Model::name()`.

## Controller

### Konvensi
- Satu controller per resource/fitur.
- Method standar: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
- Return type konsisten: `view()`, `redirect()`, `response()->json()`.
- Authorization check di awal method.
- Gunakan route model binding jika memungkinkan.

### Pattern yang Benar
```php
// ✅ Baik — validasi, proses, redirect
public function store(Request $request)
{
    $validated = $request->validate([...]);
    Model::create($validated);
    return redirect()->route('index')->with('success', 'Berhasil.');
}

// ❌ Buruk — logic panjang tanpa pemisahan
public function store(Request $request)
{
    // 200 baris kode di sini...
}
```

## Blade Template

### Konvensi
- Gunakan `@extends`, `@section`, `@yield` untuk layout inheritance.
- Gunakan `@push`/`@stack` untuk scripts dan styles per-halaman.
- Gunakan `@component` atau `<x-component>` untuk elemen reusable.
- Escape output: `{{ $var }}` (escaped) vs `{!! $var !!}` (raw, hanya jika yakin aman).
- CSRF: selalu `@csrf` di form. Method spoofing: `@method('PUT')`, `@method('DELETE')`.

### Struktur
```
resources/views/
├── layouts/          → Base layouts (app, dashboard, auth)
│   └── partials/     → Navbar, footer, sidebar
├── auth/             → Login, register, verify
├── admin/            → Admin pages
├── pengelola/        → Pengelola pages
├── pengunjung/       → Pengunjung pages
└── components/       → Reusable blade components
```

## Database

### Migration
- Nama file: `{timestamp}_create_{table}_table.php` atau `{timestamp}_add_{column}_to_{table}_table.php`
- Selalu buat `down()` method yang benar untuk rollback.
- Jangan edit migration yang sudah di-run di production — buat migration baru.

### Seeder
- Gunakan Factory untuk data dummy.
- Seeder untuk data master (role, status, dll).

## Security Checklist
- [ ] Semua input divalidasi (server-side)
- [ ] CSRF protection aktif di semua form
- [ ] Password di-hash menggunakan `Hash::make()`
- [ ] Query menggunakan Eloquent/Query Builder (prevent SQL injection)
- [ ] File upload divalidasi (mime type, size)
- [ ] Authorization check di setiap endpoint sensitif
- [ ] Jangan expose sensitive data di response (password, token, secret key)

## Debugging
- Gunakan `dd()`, `dump()`, atau `Log::info()` — bukan `echo` atau `var_dump`.
- Cek `storage/logs/laravel.log` untuk error.
- Gunakan `php artisan tinker` untuk test query.
- Jika config berubah: `php artisan config:clear && php artisan cache:clear`.
