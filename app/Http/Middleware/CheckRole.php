<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini menerima satu parameter `$role` yang bisa berisi
     * satu role ('admin') atau beberapa role ('admin,staff').
     *
     * Contoh penggunaan di route:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,staff')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // Cek apakah role user termasuk dalam daftar role yang diizinkan
        if (! in_array($userRole, $roles, strict: true)) {
            // Tentukan pesan error yang kontekstual
            $message = $this->resolveErrorMessage($userRole, $roles);

            // Redirect ke dashboard yang sesuai dengan role user
            $redirectRoute = $this->resolveDashboardRoute($userRole);

            return redirect()
                ->route($redirectRoute)
                ->with('error', $message);
        }

        return $next($request);
    }

    /**
     * Menentukan rute dashboard berdasarkan role user yang sedang login.
     */
    private function resolveDashboardRoute(string $role): string
    {
        return match ($role) {
            'super_admin' => 'super_admin.dashboard',
            'admin' => 'admin.dashboard',
            'staff' => 'staff.dashboard',
            default => 'dashboard',
        };
    }

    /**
     * Menentukan pesan error yang sesuai konteks akses yang ditolak.
     *
     * @param  string[] $requiredRoles
     */
    private function resolveErrorMessage(string $userRole, array $requiredRoles): string
    {
        if ($userRole === 'staff') {
            return 'Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator.';
        }

        if ($userRole === 'admin' && in_array('super_admin', $requiredRoles, strict: true)) {
            return 'Akses ditolak. Halaman ini hanya untuk Super Admin.';
        }

        if ($userRole === 'super_admin' && in_array('admin', $requiredRoles, strict: true) && !in_array('super_admin', $requiredRoles, strict: true)) {
            return 'Halaman ini khusus untuk Admin Reviewer.';
        }

        return 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    }
}
