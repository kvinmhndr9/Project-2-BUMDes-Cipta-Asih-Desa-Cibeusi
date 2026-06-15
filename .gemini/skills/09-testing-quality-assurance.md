# Testing & Quality Assurance

> Skill untuk memastikan kode berkualitas — testing, code review, dan validasi sebelum deploy.

## Prinsip Testing

### Kapan Harus Test
- Setelah **setiap perubahan** yang menyentuh logic (bukan hanya styling)
- Setelah fix bug — pastikan bug benar-benar hilang
- Sebelum commit/push ke branch utama
- Sebelum deploy ke production

### Jenis Test

| Jenis | Apa yang ditest | Tools |
|---|---|---|
| **Unit Test** | Fungsi/method individual | PHPUnit, Jest, pytest |
| **Feature/Integration** | Alur lengkap (request→response) | PHPUnit Feature, Supertest |
| **Browser/E2E** | Interaksi user di browser | Cypress, Playwright, browser tool |
| **Manual** | Klik di browser, cek visual | Browser DevTools |

### Prioritas Testing (80/20 Rule)
1. **Test yang paling penting dulu:** flow kritis (login, pembayaran, submit form)
2. **Test edge case:** input kosong, input sangat panjang, karakter khusus
3. **Test error path:** apa yang terjadi jika server error, network mati, input invalid

## Laravel Testing

### Test Command
```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=NamaTest

# Jalankan dengan coverage
php artisan test --coverage
```

### Contoh Feature Test
```php
public function test_pengunjung_bisa_pesan_tiket()
{
    $user = User::factory()->create(['role' => 'pengunjung']);
    $wisata = Wisata::factory()->create();

    $response = $this->actingAs($user)->post(route('pengunjung.tiket.store'), [
        'id_wisata' => $wisata->id_wisata,
        'jumlah' => 2,
        'tanggal_berkunjung' => now()->addDays(3)->format('Y-m-d'),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tikets', ['id_user' => $user->id_user, 'jumlah' => 2]);
}
```

## Frontend Testing

### Browser Checklist (Manual)
Sebelum menganggap UI selesai:
- [ ] Buka di Chrome, Firefox, Safari (jika bisa)
- [ ] Buka di mobile mode (DevTools → Toggle Device Toolbar → 375px)
- [ ] Klik semua tombol — pastikan ada feedback
- [ ] Submit form tanpa isi — pastikan validasi muncul
- [ ] Submit form dengan data benar — pastikan redirect/response benar
- [ ] Refresh halaman — pastikan state tidak rusak
- [ ] Tekan Back browser — pastikan tidak blank/error
- [ ] Test di slow network (DevTools → Network → Slow 3G)

### JavaScript Testing
```bash
# Jest
npx jest

# Vitest
npx vitest run
```

## Code Review Checklist

Sebelum menganggap kode "selesai", cek:

### Fungsionalitas
- [ ] Fitur berfungsi sesuai requirement
- [ ] Edge case tertangani (null, empty, max value)
- [ ] Error message jelas dan membantu user

### Keamanan
- [ ] Input divalidasi (server-side)
- [ ] Tidak ada SQL injection risk
- [ ] Tidak ada XSS risk
- [ ] Authorization/permission benar (user A tidak bisa akses data user B)

### Performance
- [ ] Tidak ada query dalam loop (N+1)
- [ ] Data besar di-paginate
- [ ] Asset di-compress (JS/CSS minify di production)
- [ ] Gambar dioptimasi (format, ukuran)

### Maintainability
- [ ] Nama variabel/fungsi deskriptif
- [ ] Tidak ada magic number/string — gunakan konstanta
- [ ] Duplikasi kode diminimalisir
- [ ] Kode mudah dibaca oleh developer lain

## Anti-Pattern Testing

1. ❌ "Saya sudah test di browser saya, pasti jalan" — test di device/browser lain
2. ❌ Test hanya happy path — test juga error path
3. ❌ Skip test karena "fitur kecil" — bug kecil bisa berefek besar
4. ❌ Test yang bergantung pada urutan eksekusi — setiap test harus independen
5. ❌ Hardcode tanggal/waktu di test — gunakan Carbon::now() atau mock
