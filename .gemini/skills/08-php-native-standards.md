# PHP Native Standards

> Skill untuk project PHP murni tanpa framework — berlaku untuk PHP 7.4+.

## Struktur Project

### Rekomendasi Folder
```
project/
├── config/          → File konfigurasi (database.php, app.php)
├── public/          → Entry point (index.php), assets
│   ├── css/
│   ├── js/
│   └── images/
├── src/             → Logic aplikasi (classes, functions)
│   ├── Models/      → Class model / data access
│   ├── Controllers/ → Handler request
│   └── Helpers/     → Fungsi helper
├── templates/       → File HTML/PHP template
├── vendor/          → Composer dependencies (jangan edit manual)
├── .env             → Environment variables (jangan commit)
├── .env.example     → Template env
└── composer.json    → Dependencies
```

## Koneksi Database

### Gunakan PDO (bukan mysql_* atau mysqli_*)
```php
// ✅ PDO dengan prepared statement
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

// ✅ Prepared statement — aman dari SQL injection
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// ❌ JANGAN PERNAH — SQL injection
$result = $pdo->query("SELECT * FROM users WHERE email = '$email'");
```

## Security

### Input Validation
```php
// ✅ Filter dan validasi
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$id    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$name  = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');

// ✅ Password
$hash = password_hash($password, PASSWORD_DEFAULT);
$valid = password_verify($inputPassword, $storedHash);
```

### Session
```php
// ✅ Session yang aman
session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => true, // HTTPS only
    'cookie_samesite' => 'Strict',
]);

// Regenerate session ID setelah login
session_regenerate_id(true);
```

### File Upload
```php
// ✅ Validasi file upload
$allowed = ['image/jpeg', 'image/png', 'image/webp'];
$maxSize = 2 * 1024 * 1024; // 2MB

if (!in_array($_FILES['foto']['type'], $allowed)) {
    die('Tipe file tidak diizinkan');
}
if ($_FILES['foto']['size'] > $maxSize) {
    die('Ukuran file terlalu besar');
}

// Rename file — jangan pakai nama asli
$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$newName = uniqid('upload_', true) . '.' . $ext;
move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/$newName");
```

## Coding Style
- Indentasi: 4 spasi (bukan tab)
- Brace: K&R style (`{` di baris yang sama)
- Nama class: PascalCase
- Nama method/function: camelCase
- Nama variabel: camelCase atau snake_case (konsisten per project)
- Konstanta: UPPER_SNAKE_CASE

## Anti-Pattern PHP

1. ❌ `extract($_POST)` — membuat variabel dari input tanpa kontrol
2. ❌ `eval()` — eksekusi kode dinamis, security nightmare
3. ❌ `@` error suppression operator — sembunyikan error
4. ❌ Echo HTML di dalam logic — pisahkan template dan logic
5. ❌ Global variables berlebihan — gunakan dependency injection atau parameter
6. ❌ `include` tanpa validasi path — bisa dieksploitasi untuk LFI
