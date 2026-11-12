<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Pastikan pengguna yang sedang terautentikasi memiliki akun berstatus aktif.
     * Jika akun telah dinonaktifkan, putuskan sesi seketika.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! $request->user()->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda sedang dinonaktifkan. Hubungi Kepala K3RS.',
            ]);
        }

        return $next($request);
    }
}
