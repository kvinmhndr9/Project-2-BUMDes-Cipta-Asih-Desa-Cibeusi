# Debugging & Problem Solving Methodology

> Skill untuk mendiagnosis dan memperbaiki bug secara sistematis — berlaku untuk semua bahasa dan framework.

## Metodologi Debugging

### Langkah 1: Reproduksi
- Pahami langkah exact yang menyebabkan bug.
- Cek: apakah terjadi setiap kali, atau intermittent?
- Cek: hanya di device/browser tertentu, atau semua?

### Langkah 2: Isolasi
- Temukan **file dan baris** yang menjadi sumber masalah.
- Gunakan tools:
  - **Browser:** DevTools → Console, Network, Elements tab
  - **Laravel:** `storage/logs/laravel.log`, `dd()`, `Log::info()`
  - **Node.js:** `console.log()`, debugger, `--inspect`
  - **React:** React DevTools, useEffect deps check
  - **Mobile:** Flipper, React Native Debugger, Xcode/Android Studio logs

### Langkah 3: Analisis Root Cause
Sebelum memperbaiki, jawab: **KENAPA bug ini terjadi?**

Kategori umum:
| Kategori | Contoh |
|---|---|
| **State** | Variable belum diinisialisasi, state stale |
| **Timing** | Race condition, DOM belum ready, async belum selesai |
| **Scope** | Variabel di-overwrite, closure menangkap nilai lama |
| **Data** | Null/undefined, tipe salah, format tidak sesuai |
| **Layout** | Overflow hidden, z-index war, CSS specificity |
| **Network** | CORS, timeout, response format berubah |
| **Duplikasi** | Event listener ganda, init dipanggil 2x |

### Langkah 4: Fix dengan Percaya Diri
- Fix root cause, bukan gejala.
- Pastikan fix tidak merusak fitur lain.
- Test fix di semua kondisi yang relevan.

### Langkah 5: Verifikasi
- Reproduksi ulang: bug sudah hilang?
- Side effect: fitur lain masih berjalan?
- Edge case: bagaimana jika input kosong, null, sangat besar?

## Common Bug Patterns

### 1. Event Listener Duplikat (SPA / Turbo / HMR)
```
Gejala: klik 1x tapi handler jalan 2-3x
Penyebab: init() dipanggil setiap navigasi tanpa guard
Fix: data attribute guard ATAU removeEventListener sebelum add
```

### 2. Elemen Menghilang (CSS)
```
Gejala: tombol/elemen tidak terlihat padahal ada di DOM
Penyebab: overflow:hidden, z-index rendah, opacity:0, display:none inheritance
Fix: inspect element → cek computed styles → trace parent
```

### 3. Data Tidak Update (State)
```
Gejala: UI menampilkan data lama setelah aksi
Penyebab: cache, stale closure, tidak re-render, optimistic update gagal
Fix: cek network response → cek state update → cek render trigger
```

### 4. Form Submit Tapi Tidak Ada Efek
```
Gejala: submit form, halaman refresh tapi data tidak tersimpan
Penyebab: validasi gagal tanpa feedback, CSRF expired, route salah
Fix: cek Network tab → cek response status → cek server log
```

### 5. Layout Rusak di Mobile
```
Gejala: tampilan baik di desktop, berantakan di HP
Penyebab: fixed width, no viewport meta, no media query, table tanpa scroll
Fix: responsivitas → flexbox/grid → media query → test di 375px width
```

## Performance Debugging

### Frontend
- **Lighthouse:** performance score, LCP, CLS, FID
- **Network tab:** cek request yang lambat, file terlalu besar
- **Rendering tab:** cek layout shift, repaint berlebihan
- **Bundle size:** jangan import seluruh library jika hanya butuh 1 fungsi

### Backend
- **Slow query:** aktifkan query log, cek N+1 problem
- **Memory:** monitor penggunaan memori, cek memory leak
- **Profiling:** Laravel Debugbar, Xdebug, clinic.js (Node)

## Aturan Penting

1. **Jangan panik.** Baca error message dengan teliti — biasanya jawabannya sudah ada di situ.
2. **Jangan tebak.** Gunakan log/debug tools untuk memverifikasi hipotesis.
3. **Satu perubahan per percobaan.** Jangan ubah 5 hal sekaligus — tidak akan tahu mana yang memperbaiki.
4. **Dokumentasikan fix.** Jelaskan ke user APA root cause-nya dan KENAPA fix ini benar.
