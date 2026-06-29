<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::subscribe(\App\Listeners\LogAuthenticationEvents::class);

        // Tandai email sebagai terverifikasi (aktif) jika user berhasil mengatur password dari link reset
        \Illuminate\Support\Facades\Event::listen(function (\Illuminate\Auth\Events\PasswordReset $event) {
            if (! $event->user->hasVerifiedEmail()) {
                $event->user->markEmailAsVerified();
            }
        });

        // Paksa HTTPS di production.
        // InfinityFree menggunakan reverse proxy, sehingga scheme perlu di-force agar
        // semua URL yang di-generate Laravel (asset, redirect, CSRF) menggunakan https://.
        // Tanpa ini, akan terjadi mixed content error dan CSRF token mismatch.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
