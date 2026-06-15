<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = [
            'email'    => (string) $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->role === 'pengunjung' && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('verification.otp.show', ['email' => $user->email])
                    ->with('info', 'Silakan verifikasi email Anda terlebih dahulu.');
            }

            $request->session()->regenerate();
            // Hapus intended URL agar tidak salah diarahkan ke URL yang tersimpan sebelumnya
            $request->session()->forget('url.intended');

            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'no_hp'    => ['required', 'string', 'max:20'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:App\Models\User,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'no_hp'    => $validated['no_hp'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'pengunjung',
        ]);

        event(new Registered($user));

        // Arahkan ke halaman input kode OTP
        return redirect()->route('verification.otp.show', ['email' => $user->email])
            ->with('register_success', 'Pendaftaran berhasil! Silakan masukkan kode verifikasi yang telah dikirim ke email Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /**
     * Redirect ke Google OAuth (hanya untuk pengunjung / pendaftaran).
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google OAuth: login atau daftar sebagai pengunjung.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
                if (! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            } else {
                $user = User::create([
                    'name'      => $googleUser->getName() ?: $googleUser->getEmail(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                    'password'  => Hash::make(Str::random(32)),
                    'role'      => 'pengunjung',
                ]);
                $user->markEmailAsVerified();
            }
        }

        if (! $user->isPengunjung()) {
            return redirect()->route('login')
                ->with('error', 'Akun ini tidak terdaftar sebagai pengunjung. Gunakan login email/password.');
        }

        Auth::login($user, true);
        // Google login hanya untuk pengunjung → beranda
        return redirect()->route('home');
    }

    /**
     * Tentukan halaman tujuan setelah login berdasarkan role user.
     */
    private function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isPengelolaBumdes()) {
            return redirect()->route('pengelola.dashboard');
        }

        // Pengunjung → halaman beranda publik
        return redirect()->route('home');
    }
}
