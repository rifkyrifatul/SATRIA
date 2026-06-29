<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Eksekusi request terlebih dahulu untuk memastikan tidak ada error / gagal validasi
        $response = $next($request);

        // Abaikan jika user belum login, kita hanya melacak user terautentikasi
        if (!auth()->check()) {
            return $response;
        }

        $method = $request->method();
        $routeName = $request->route() ? $request->route()->getName() : null;
        
        // Aturan 1: Tindakan Manipulasi Data (Write)
        $isWriteAction = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']);

        // Aturan 2: Aktivitas Khusus (Download File)
        // Cek nama route apakah mengandung kata 'download' atau 'print' (karena cetak juga mengambil file/resi)
        $isDownloadAction = ($method === 'GET') && $routeName && (str_contains($routeName, 'download') || str_contains($routeName, 'print'));

        // Aturan 3: Login & Logout
        $isAuthAction = in_array($routeName, ['login', 'logout']);

        // Jika tidak memenuhi kriteria di atas (misal GET biasa), abaikan pencatatan log
        if (!$isWriteAction && !$isDownloadAction && !$isAuthAction) {
            return $response;
        }

        // Tentukan deskripsi human-readable
        $description = "Melakukan request {$method} ke /{$request->path()}";

        if ($routeName === 'login') {
            $description = "User berhasil Login ke sistem.";
        } elseif ($routeName === 'logout') {
            $description = "User melakukan Logout dari sistem.";
        } elseif ($isDownloadAction) {
            $description = "User mengunduh file / mencetak dokumen.";
        }

        // Simpan ke tabel 'activity_logs' melalui Spatie Activitylog
        activity('system_traffic')
            ->causedBy(auth()->user())
            ->withProperties([
                'method'     => $method,
                'ip_address' => $request->ip(),
                'route_name' => $routeName,
                'url'        => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ])
            ->log($description);

        return $response;
    }
}
