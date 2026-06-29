<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Jika user flagged 'must_change_password', redirect paksa ke halaman ganti password.
     * Pengecualian diberikan untuk route: profile.edit, profile.update, logout.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Abaikan jika user belum login atau tidak perlu ganti password
        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        // Route yang diizinkan meski must_change_password=true
        $allowedRoutes = [
            'profile.edit',
            'profile.update',   // Update data profil (nama, email, foto)
            'password.update',  // ← WAJIB: route yang memproses POST form ganti password
            'logout',
            'password.confirm',
        ];

        if (in_array($request->route()?->getName(), $allowedRoutes, true)) {
            return $next($request);
        }

        // Paksa redirect ke halaman profil dengan pesan peringatan
        return redirect()
            ->route('profile.edit')
            ->with('warning', 'Anda menggunakan password bawaan sistem. Demi keamanan, harap ganti password Anda sekarang sebelum melanjutkan.');
    }
}
