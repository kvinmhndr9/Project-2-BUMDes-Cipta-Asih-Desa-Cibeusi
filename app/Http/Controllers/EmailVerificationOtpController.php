<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationOtpController extends Controller
{
    /**
     * Tampilkan halaman input kode OTP.
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email', $request->session()->get('verify_email'));

        if (! $email) {
            return redirect()->route('login');
        }

        // Simpan di session agar POST bisa mengaksesnya
        $request->session()->put('verify_email', $email);

        return view('auth.verify-email', compact('email'));
    }

    /**
     * Verifikasi kode OTP yang dimasukkan user.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Kode verifikasi wajib diisi.',
            'code.size'     => 'Kode verifikasi harus 6 angka.',
        ]);

        $email = $request->session()->get('verify_email');

        if (! $email) {
            return redirect()->route('login')->with('error', 'Sesi verifikasi tidak valid. Silakan login kembali.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
        }

        // Kode salah
        if ($user->verification_code !== $request->code) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid. Silakan periksa kembali.']);
        }

        // Kode kedaluwarsa
        if ($user->verification_code_expires_at && now()->isAfter($user->verification_code_expires_at)) {
            return back()->withErrors(['code' => 'Kode verifikasi sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Tandai email sebagai terverifikasi dan bersihkan kode
        $user->forceFill([
            'email_verified_at'             => now(),
            'verification_code'             => null,
            'verification_code_expires_at'  => null,
        ])->save();

        event(new \Illuminate\Auth\Events\Verified($user));

        // Langsung login
        Auth::login($user, false);
        $request->session()->forget('verify_email');
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Email berhasil diverifikasi! Selamat datang di SI-ASIH.');
    }

    /**
     * Kirim ulang kode OTP ke email user.
     */
    public function resend(Request $request)
    {
        $email = $request->session()->get('verify_email');

        if (! $email) {
            return redirect()->route('login');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        // Generate & kirim ulang kode baru
        $user->sendEmailVerificationNotification();

        return back()->with('resent', 'Kode verifikasi baru telah dikirimkan ke email Anda.');
    }
}
