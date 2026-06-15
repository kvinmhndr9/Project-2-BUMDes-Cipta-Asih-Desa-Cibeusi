# UI/UX & Frontend Design Standards

> Skill untuk memastikan setiap UI yang dibuat terlihat modern, premium, dan profesional.

## Prinsip Desain

### Warna
- **Jangan pernah** pakai warna mentah (red, blue, green, black). Selalu gunakan warna yang sudah di-curate.
- Sebelum menulis CSS warna, **cek dulu** variabel/token yang sudah ada di project (`variables.scss`, `tailwind.config`, CSS custom properties).
- Jika project sudah punya color palette, **wajib** gunakan itu. Jangan buat warna baru kecuali benar-benar dibutuhkan.
- Untuk gradien, gunakan warna yang **sudah ada di project** sebagai base.

### Typography
- Gunakan font yang sudah di-import di project. Jangan tambah font baru tanpa diskusi.
- Hierarchy: h1 > h2 > h3. Satu `<h1>` per halaman.
- Font size harus responsif — gunakan `clamp()`, `rem`, atau breakpoint.
- Font weight: 400 (body), 600 (label/subtitle), 700-800 (heading).

### Spacing & Layout
- Konsisten — jika project pakai kelipatan 8px, ikuti kelipatan 8px.
- Gunakan CSS Grid atau Flexbox, hindari float.
- Mobile-first: desain untuk mobile dulu, lalu scale up.
- Hindari horizontal scroll di semua breakpoint.

### Komponen
- Gunakan komponen/class yang sudah ada sebelum membuat yang baru.
- Jika buat baru, tempatkan CSS-nya di file yang sesuai — jangan inline style berlebihan.
- Setiap interactive element harus punya `:hover`, `:focus`, dan `:active` state.
- Tambahkan `transition` halus (0.2s-0.3s) untuk state changes.

## Responsivitas

### Breakpoints (prioritas)
```
Mobile:  < 576px   → single column, stacked layout
Tablet:  576-768px → adjust spacing, maybe 2 columns  
Desktop: > 768px   → full layout
```

### Checklist Responsive
- [ ] Tombol bisa diklik di mobile (min 44x44px touch target)
- [ ] Teks terbaca tanpa zoom (min 14px body text)
- [ ] Form input tidak overflow di layar kecil
- [ ] Gambar punya max-width: 100%
- [ ] Tabel punya scroll horizontal atau layout alternatif di mobile
- [ ] Modal/drawer bisa ditutup di semua device

## Aksesibilitas Dasar
- Semua `<img>` harus punya `alt`
- Semua `<button>` harus punya teks atau `aria-label`
- Semua `<input>` harus punya `<label>` terkait
- Warna foreground vs background harus punya contrast ratio memadai
- Focus indicator harus visible (jangan `outline: none` tanpa pengganti)

## Anti-Pattern UI

1. ❌ Placeholder text sebagai label (hilang saat user mulai ketik)
2. ❌ Infinite scroll tanpa indikator loading
3. ❌ Modal di dalam modal
4. ❌ Alert/notifikasi yang menghalangi konten tanpa bisa ditutup
5. ❌ Tombol tanpa feedback visual saat diklik
6. ❌ Form yang submit saat enter tanpa konfirmasi di action berbahaya
7. ❌ Overflow tersembunyi yang menyebabkan elemen menghilang
