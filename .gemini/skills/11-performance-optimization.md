# Performance & Optimization

> Skill untuk membuat aplikasi cepat, ringan, dan efisien — frontend dan backend.

## Frontend Performance

### Critical Rendering Path
1. **CSS** harus dimuat di `<head>` (render-blocking, tapi harus cepat)
2. **JS** harus dimuat di akhir `<body>` atau pakai `defer`/`async`
3. **Font** gunakan `font-display: swap` agar teks tampil dulu sebelum font selesai load
4. **Image** gunakan lazy loading: `loading="lazy"`

### Image Optimization
```html
<!-- ✅ Lazy loading + dimensi eksplisit (prevent layout shift) -->
<img src="foto.webp" width="400" height="300" loading="lazy" alt="Deskripsi">
```
- Format: prefer **WebP** (50-70% lebih kecil dari JPEG)
- Resize: jangan serve gambar 4000px untuk tampilan 400px
- Compression: quality 75-85% sudah cukup untuk web

### CSS Optimization
- Hapus CSS yang tidak digunakan (tree-shaking)
- Hindari selector yang terlalu spesifik (4+ level nesting)
- Gunakan `will-change` hanya pada elemen yang benar-benar di-animasi
- Batch DOM read/write — jangan bergantian (layout thrashing)

### JavaScript Optimization
- Code splitting: jangan load semua JS di satu bundle
- Debounce: untuk event yang terlalu sering (scroll, resize, input)
- Throttle: untuk event yang butuh rate limit (API call saat ketik)
```js
// ✅ Debounce — tunggu user berhenti ketik 300ms baru eksekusi
function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}
input.addEventListener('input', debounce(search, 300));
```

## Backend Performance

### Database
```php
// ✅ Eager loading — hindari N+1
Tiket::with(['user', 'wisata'])->paginate(15);

// ✅ Select hanya kolom yang dibutuhkan
User::select('id_user', 'name', 'email')->get();

// ✅ Chunking untuk proses data besar
User::chunk(200, function ($users) {
    foreach ($users as $user) { /* proses */ }
});

// ✅ Index pada kolom yang sering di-query
// Di migration: $table->index('status');
```

### Caching
```php
// ✅ Cache query yang jarang berubah
$wisata = Cache::remember('wisata_all', 3600, function () {
    return Wisata::orderBy('nama')->get();
});

// ✅ Invalidate cache saat data berubah
Cache::forget('wisata_all');

// ✅ Laravel config/route/view cache di production
// php artisan config:cache
// php artisan route:cache
// php artisan view:cache
```

### API Response
- Paginate list endpoint (jangan return ribuan record sekaligus)
- Compress response (gzip/brotli)
- Return hanya field yang dibutuhkan
- Cache response yang static (header Cache-Control)

## Monitoring

### Key Metrics
| Metric | Target | Tools |
|---|---|---|
| **LCP** (Largest Contentful Paint) | < 2.5s | Lighthouse |
| **FID** (First Input Delay) | < 100ms | Lighthouse |
| **CLS** (Cumulative Layout Shift) | < 0.1 | Lighthouse |
| **TTFB** (Time to First Byte) | < 600ms | DevTools |
| **Page Size** | < 3MB total | DevTools Network |

### Laravel Monitoring
```bash
# Query yang lambat — aktifkan di AppServiceProvider
DB::listen(function ($query) {
    if ($query->time > 100) { // > 100ms
        Log::warning('Slow Query', [
            'sql' => $query->sql,
            'time' => $query->time . 'ms'
        ]);
    }
});
```

## Anti-Pattern Performance

1. ❌ `Model::all()` tanpa paginate di API publik
2. ❌ Query dalam loop — selalu eager load atau batch
3. ❌ Gambar 5MB tanpa compress untuk thumbnail 100px
4. ❌ Load seluruh library untuk 1 fungsi kecil
5. ❌ `setTimeout` sebagai "fix" race condition — perbaiki root cause
6. ❌ Infinite AJAX polling setiap 1 detik — gunakan interval wajar (30s+)
7. ❌ CSS `*` selector — sangat mahal di skala besar
