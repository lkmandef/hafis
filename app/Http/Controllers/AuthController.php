<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir masuk sistem HAFIS
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses otentikasi kredensial pengguna dengan proteksi rate limit
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Identitas login (username atau email) wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($validated['login']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => "Terlalu banyak percobaan masuk yang gagal. Silakan coba kembali dalam {$seconds} detik.",
                ]);
        }

        $loginInput = trim($validated['login']);
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Verifikasi keberadaan dan status keaktifan pengguna secara terkontrol
        $user = User::where($field, $loginInput)->first();

        if ($user && ! $user->isActive()) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => 'Kredensial yang dimasukkan tidak cocok dengan catatan kami, atau akun Anda sedang dinonaktifkan.',
                ]);
        }

        if (Auth::attempt([$field => $loginInput, 'password' => $validated['password'], 'is_active' => true])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali di sistem HAFIS, '.Auth::user()->name.'.');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withInput($request->only('login'))
            ->withErrors([
                'login' => 'Kredensial yang dimasukkan tidak cocok dengan catatan kami, atau akun Anda sedang dinonaktifkan.',
            ]);
    }

    /**
     * Keluar dari sistem, batalkan sesi, dan regenerasi token CSRF
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem HAFIS.');
    }
}
