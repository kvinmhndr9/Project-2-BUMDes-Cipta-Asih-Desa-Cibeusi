<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLaporanOfflineController;
use App\Http\Controllers\Admin\LaporanAdminController;
use App\Http\Controllers\Admin\ValidasiTiketController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationOtpController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\Pengelola\AkunAdminController;
use App\Http\Controllers\Pengelola\LaporanController as PengelolaLaporanController;
use App\Http\Controllers\Pengelola\PengelolaDashboardController;
use App\Http\Controllers\Pengelola\ProdukKhasController as PengelolaProdukKhasController;
use App\Http\Controllers\Pengelola\WisataController as PengelolaWisataController;
use App\Http\Controllers\Pengunjung\TiketController;
use App\Http\Controllers\Pengunjung\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TiketPublikController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════════════════
// HALAMAN PUBLIK — dapat diakses oleh siapa saja (tamu maupun yang sudah login)
// ══════════════════════════════════════════════════════════════════════════════

Route::get('/', function () {
    $wisata = \App\Models\Wisata::orderBy('nama')->get();
    $produk = \App\Models\ProdukKhas::with('wisata')->orderBy('urutan')->orderBy('nama')->get();
    return view('home', compact('wisata', 'produk'));
})->name('home')->middleware('pengunjung_only');

Route::get('/wisata', [PublicController::class, 'wisataIndex'])->name('public.wisata.index');
Route::get('/wisata/{wisata:slug}', [PublicController::class, 'wisataShow'])->name('public.wisata.show');

Route::get('/produk-khas', [PublicController::class, 'produkKhasIndex'])->name('public.produk-khas.index');
Route::get('/produk-khas/{produk_khas}', [PublicController::class, 'produkKhasShow'])->name('public.produk-khas.show');

Route::get('/api/cuaca-bmkg', [PublicController::class, 'getWeather'])->name('api.cuaca-bmkg');

// E-Tiket Publik — dapat diakses siapapun via URL dari QR code (tanpa login)
Route::get('/tiket/{kode}', [TiketPublikController::class, 'show'])->name('tiket.publik');

// ══════════════════════════════════════════════════════════════════════════════
// AUTENTIKASI — hanya untuk tamu (belum login)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // TODO: tambahkan throttle sebelum production
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post'); // TODO: tambahkan throttle sebelum production
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

    // ── Lupa Kata Sandi ─────────────────────────────────────────────────────
    Route::get('/lupa-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email'); // TODO: tambahkan throttle sebelum production
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update.reset');
});

// ── Verifikasi Email OTP ─────────────────────────────────────────────────────
// Menggantikan link bertanda tangan bawaan Laravel dengan kode 6 angka.
Route::get('/verifikasi-email', [EmailVerificationOtpController::class, 'showVerifyForm'])->name('verification.otp.show');
Route::post('/verifikasi-email', [EmailVerificationOtpController::class, 'verify'])->name('verification.otp.verify');
Route::post('/verifikasi-email/kirim-ulang', [EmailVerificationOtpController::class, 'resend'])->name('verification.otp.resend');

// Rute lama (link bertanda tangan) — masih dipertahankan agar tidak 404 jika ada link lama di email
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Link verifikasi tidak valid.');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')->with('register_success', 'Email Anda sudah terverifikasi sebelumnya. Silakan login.');
    }

    if ($user->markEmailAsVerified()) {
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return redirect()->route('login')->with('register_success', 'Email berhasil diverifikasi! Silakan login sekarang.');
})->name('verification.verify'); // TODO: tambahkan throttle sebelum production

// ══════════════════════════════════════════════════════════════════════════════
// ROUTE YANG MEMBUTUHKAN LOGIN
// ══════════════════════════════════════════════════════════════════════════════

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/payment/notification', MidtransNotificationController::class)
    ->name('payment.notification')
    ->middleware('midtrans.ip'); // Hanya izinkan IP resmi Midtrans

Route::middleware('auth')->group(function () {
    // Redirect ke dashboard sesuai role masing-masing
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Ubah password (semua role yang sudah login)
    Route::get('/password', [\App\Http\Controllers\PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [\App\Http\Controllers\PasswordController::class, 'update'])->name('password.update');

    // ══════════════════════════════════════════════════════════════════════════
    // PENGUNJUNG
    // ══════════════════════════════════════════════════════════════════════════

    Route::prefix('pengunjung')->name('pengunjung.')->middleware('role:pengunjung')->group(function () {
        Route::get('/', [TiketController::class, 'index'])->name('dashboard');
        Route::get('/wisata', [TiketController::class, 'index'])->name('wisata.index');

        // Profil
        Route::get('/profil', [ProfileController::class, 'index'])->name('profil.index');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
        Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profil.avatar');

        // Pesan tiket (butuh no_hp / WhatsApp) — binding eksplisit id_wisata
        Route::middleware('has_wa')->group(function () {
            Route::get('/wisata/{wisata:id_wisata}/pesan', [TiketController::class, 'create'])->name('tiket.create');
            Route::post('/tiket', [TiketController::class, 'store'])->name('tiket.store'); // TODO: tambahkan throttle sebelum production
        });

        // Review
        Route::post('/review', [ReviewController::class, 'store'])->name('review.store'); // TODO: tambahkan throttle sebelum production
        Route::put('/review/{review}', [ReviewController::class, 'update'])->name('review.update');

        // Tiket — /saya harus sebelum /{tiket} agar tidak konflik
        Route::get('/tiket/saya', [TiketController::class, 'myTickets'])->name('tiket.my');
        Route::get('/tiket/{tiket}', [TiketController::class, 'show'])->name('tiket.show');
        Route::get('/tiket/{tiket}/bayar', [TiketController::class, 'bayar'])->name('tiket.bayar');
        Route::get('/tiket/{tiket}/qrcode', [TiketController::class, 'qrcode'])->name('tiket.qrcode');
        Route::put('/tiket/{tiket}/reschedule', [TiketController::class, 'reschedule'])->name('tiket.reschedule'); // TODO: tambahkan throttle sebelum production
        Route::delete('/tiket/{tiket}/batal', [TiketController::class, 'cancel'])->name('tiket.cancel'); // TODO: tambahkan throttle sebelum production
    });

    // ══════════════════════════════════════════════════════════════════════════
    // ADMIN (per wisata)
    // ══════════════════════════════════════════════════════════════════════════

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Validasi tiket
        Route::get('/validasi', [ValidasiTiketController::class, 'index'])->name('validasi.index');
        Route::match(['get', 'post'], '/validasi/cari', [ValidasiTiketController::class, 'cari'])->name('validasi.cari'); // TODO: tambahkan throttle sebelum production
        Route::post('/validasi/tiket/{tiket}/validasi', [ValidasiTiketController::class, 'validasi'])->name('validasi.validasi'); // TODO: tambahkan throttle sebelum production

        // History validasi
        Route::get('/history-validasi', [ValidasiTiketController::class, 'history'])->name('history-validasi');

        // Laporan
        Route::get('/laporan', [LaporanAdminController::class, 'index'])->name('laporan');
        Route::get('/laporan/print', [LaporanAdminController::class, 'print'])->name('laporan.print');


        // Penjualan offline
        Route::resource('penjualan-offline', AdminLaporanOfflineController::class)->except(['show'])->names([
            'index'   => 'penjualan-offline.index',
            'create'  => 'penjualan-offline.create',
            'store'   => 'penjualan-offline.store',
            'edit'    => 'penjualan-offline.edit',
            'update'  => 'penjualan-offline.update',
            'destroy' => 'penjualan-offline.destroy',
        ]);
    });

    // ══════════════════════════════════════════════════════════════════════════
    // PENGELOLA BUMDes
    // ══════════════════════════════════════════════════════════════════════════

    Route::prefix('pengelola')->name('pengelola.')->middleware('role:pengelola_bumdes')->group(function () {
        Route::get('/', [PengelolaDashboardController::class, 'index'])->name('dashboard');


        // Laporan
        Route::get('/laporan', [PengelolaLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/print', [PengelolaLaporanController::class, 'print'])->name('laporan.print');


        // Wisata — gallery routes HARUS sebelum resource
        Route::post('wisata/{wisata}/gallery', [PengelolaWisataController::class, 'storeGallery'])->name('wisata.gallery.store');
        Route::delete('wisata/{wisata}/gallery/{index}', [PengelolaWisataController::class, 'destroyGallery'])->name('wisata.gallery.destroy');
        Route::resource('wisata', PengelolaWisataController::class)
            ->parameters(['wisata' => 'wisata']);

        // Produk Khas — gallery routes HARUS sebelum resource
        Route::post('produk-khas/{produkKhas}/gallery', [PengelolaProdukKhasController::class, 'storeGallery'])->name('produk-khas.gallery.store');
        Route::delete('produk-khas/{produkKhas}/gallery/{index}', [PengelolaProdukKhasController::class, 'destroyGallery'])->name('produk-khas.gallery.destroy');
        Route::resource('produk-khas', PengelolaProdukKhasController::class)
            ->parameters(['produk-khas' => 'produkKhas']);

        // Kelola Akun Admin
        Route::resource('akun-admin', AkunAdminController::class)
            ->except(['show'])
            ->parameters(['akun-admin' => 'akunAdmin']);
    });
});
