# Core Agent Behavior — Universal

> Skill ini berlaku untuk SEMUA project, semua bahasa, semua framework.

## Prinsip Utama

1. **Baca dulu, baru tulis.** Selalu baca file yang akan diedit sebelum mengubahnya. Jangan pernah menebak isi file.
2. **Jangan duplikasi.** Sebelum membuat fungsi/komponen baru, cari dulu apakah sudah ada yang serupa di codebase.
3. **Preserve yang ada.** Jangan hapus komentar, docstring, atau kode yang tidak terkait perubahan — kecuali diminta user.
4. **Satu perubahan, satu tujuan.** Setiap edit harus punya alasan jelas. Jangan refactor kode yang tidak diminta.

## Cara Kerja

### Sebelum Coding
- Pahami konteks: baca file terkait, pahami arsitektur, cek konvensi yang sudah ada.
- Jika task kompleks (multi-file, arsitektur baru), buat plan dulu dan minta approval.
- Jika task simpel (bug fix, styling, typo), langsung kerjakan tanpa plan.

### Saat Coding
- **Konsisten** dengan style yang sudah ada di project (indentasi, naming convention, struktur folder).
- **Gunakan bahasa yang sama** dengan codebase. Jika komentar ditulis Bahasa Indonesia, lanjutkan Indonesia.
- **Hindari over-engineering.** Solusi sederhana yang bekerja > arsitektur mewah yang overkill.
- **Test setelah edit.** Jika ada test suite, jalankan. Jika ada dev server, pastikan tidak error.

### Error Handling
- Jika menemukan bug saat mengerjakan task, perbaiki jika terkait langsung. Jika tidak, laporkan ke user tanpa mengubah.
- Jika command gagal, analisis error message sebelum coba ulang. Jangan spam retry tanpa perubahan.
- Jika ada konflik atau ambiguitas, tanyakan user — jangan asumsi.

## Kualitas Kode

### Naming Convention (ikuti project)
- Jika project pakai `camelCase` → ikuti `camelCase`
- Jika project pakai `snake_case` → ikuti `snake_case`
- Jika project pakai Bahasa Indonesia → ikuti Bahasa Indonesia
- **Jangan campuraduk** tanpa alasan

### Struktur File
- Ikuti pola folder yang sudah ada
- Jangan buat folder baru tanpa alasan kuat
- File baru harus mengikuti konvensi penamaan project

## Anti-Pattern yang Harus Dihindari

1. ❌ Mengganti seluruh isi file padahal hanya edit 3 baris
2. ❌ Menambah dependency baru tanpa diskusi
3. ❌ Membuat file temporary di luar workspace
4. ❌ Menjalankan command destruktif tanpa konfirmasi
5. ❌ Mengabaikan error dan melanjutkan seolah berhasil
6. ❌ Copy-paste solusi dari internet tanpa adaptasi ke codebase
7. ❌ Menghapus fitur yang sudah jalan demi "membersihkan"
