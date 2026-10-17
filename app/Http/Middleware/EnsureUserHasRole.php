<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Tangani permintaan masuk dan pastikan pengguna memiliki peran yang diizinkan
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Pastikan akun berstatus aktif
        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda sedang dinonaktifkan. Hubungi Kepala K3RS.',
            ]);
        }

        // Periksa kesesuaian peran pengguna
        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak. Tindakan ini memerlukan hak akses administratif Kepala K3RS.');
        }

        return $next($request);
    }
}
