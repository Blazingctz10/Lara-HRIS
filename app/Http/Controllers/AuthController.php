<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login utama HRIS.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('welcome');
    }

    /**
     * Proses autentikasi login pengguna dengan proteksi Anti Brute-force & Anti Bot.
     */
    public function login(Request $request)
    {
        // 1. Anti-Bot Honeypot Trap Check
        if ($request->filled('website_hp')) {
            Log::warning('Security: Bot honeypot triggered during login attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => __('Permintaan tidak valid terdeteksi.'),
            ]);
        }

        // 2. Rate Limiting Check (Anti Brute-Force & Credential Stuffing)
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::warning('Security: Login rate limit exceeded (lockout)', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'seconds_remaining' => $seconds,
            ]);

            throw ValidationException::withMessages([
                'email' => __('Terlalu banyak percobaan masuk yang gagal. Silakan coba lagi dalam :seconds detik demi keamanan akun Anda.', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();
            Log::info('Security: User authenticated successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
            ]);

            return $this->redirectUserByRole($user);
        }

        // Increment failed attempt counter (60 seconds decay)
        RateLimiter::hit($throttleKey, 60);

        Log::warning('Security: Failed login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'attempts' => RateLimiter::attempts($throttleKey),
        ]);

        throw ValidationException::withMessages([
            'email' => __('Email atau kata sandi yang Anda masukkan salah.'),
        ]);
    }

    /**
     * Hitung throttle key unik berdasarkan email & IP penyerang/pengguna.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Arahkan pengguna ke panel yang sesuai dengan role-nya.
     */
    protected function redirectUserByRole($user)
    {
        if (in_array($user->role, ['super_admin', 'hr_admin', 'branch_manager'])) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/portal');
    }
}
