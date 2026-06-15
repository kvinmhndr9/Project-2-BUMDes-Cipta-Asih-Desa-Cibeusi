# Database & API Design Standards

> Skill untuk perancangan database dan API — berlaku untuk SQL (MySQL, PostgreSQL), NoSQL, dan REST/GraphQL API.

## Database Design

### Naming Convention
- **Tabel:** PascalCase atau snake_case — **ikuti konvensi project yang sudah ada**
- **Kolom:** snake_case (`id_user`, `tanggal_berkunjung`, `created_at`)
- **Primary Key:** `id_{nama_tabel}` (sesuai project) atau `id` (Laravel default)
- **Foreign Key:** `id_{tabel_referensi}` — harus konsisten

### Tipe Data
| Kebutuhan | Tipe yang Tepat |
|---|---|
| ID | `BIGINT UNSIGNED AUTO_INCREMENT` |
| Nama/Teks pendek | `VARCHAR(255)` |
| Teks panjang | `TEXT` |
| Harga/Uang | `DECIMAL(12,0)` untuk Rupiah, `DECIMAL(12,2)` untuk mata uang desimal |
| Tanggal | `DATE` |
| Waktu/Timestamp | `DATETIME` atau `TIMESTAMP` |
| Boolean | `TINYINT(1)` atau `BOOLEAN` |
| Enum | `ENUM('val1','val2')` atau string + validasi di aplikasi |
| JSON | `JSON` (MySQL 5.7+) atau `TEXT` |

### Indexing
- Primary key otomatis index
- Foreign key harus di-index
- Kolom yang sering di-WHERE atau ORDER BY → tambah index
- Jangan over-index — setiap index memperlambat INSERT/UPDATE

### Migration Best Practices
```php
// ✅ Selalu nullable untuk kolom baru di tabel yang sudah ada data
$table->string('kolom_baru')->nullable()->after('kolom_sebelumnya');

// ✅ Selalu buat down() yang benar
public function down()
{
    Schema::table('tabel', function (Blueprint $table) {
        $table->dropColumn('kolom_baru');
    });
}
```

## API Design

### REST Endpoints
```
GET    /api/resources          → List (index)
GET    /api/resources/{id}     → Detail (show)
POST   /api/resources          → Create (store)
PUT    /api/resources/{id}     → Update (update)
DELETE /api/resources/{id}     → Delete (destroy)
```

### Response Format (konsisten)
```json
// Success
{
    "success": true,
    "data": { ... },
    "message": "Data berhasil diambil"
}

// Success dengan pagination
{
    "success": true,
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 48
    }
}

// Error
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "email": ["Email sudah terdaftar"]
    }
}
```

### HTTP Status Code
| Code | Gunakan untuk |
|---|---|
| `200` | Success (GET, PUT) |
| `201` | Created (POST berhasil buat data baru) |
| `204` | No Content (DELETE berhasil) |
| `400` | Bad Request (input invalid) |
| `401` | Unauthorized (belum login) |
| `403` | Forbidden (login tapi tidak punya akses) |
| `404` | Not Found |
| `422` | Validation Error |
| `500` | Server Error |

### Security API
- Selalu validasi input di server
- Rate limiting untuk endpoint publik
- Authentication token di header, bukan URL
- Jangan expose internal error details di production
- CORS: whitelist domain, jangan `*` di production

## Query Optimization

### N+1 Problem
```php
// ❌ N+1 — 1 query + N query untuk relasi
$tikets = Tiket::all();
foreach ($tikets as $tiket) {
    echo $tiket->user->name; // query per tiket!
}

// ✅ Eager loading — 2 query total
$tikets = Tiket::with('user')->get();
foreach ($tikets as $tiket) {
    echo $tiket->user->name; // sudah di-load
}
```

### Pagination
- Selalu paginate untuk list yang bisa panjang
- Jangan `Model::all()` untuk endpoint publik
- Gunakan cursor pagination untuk dataset sangat besar
