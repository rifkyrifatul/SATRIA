<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pembersihan log secara otomatis setiap hari (menghapus yang usianya > 6 bulan sesuai config)
Schedule::command('activitylog:clean')->daily();

// Jadwalkan penghapusan otomatis (prune) untuk model yang memiliki trait Prunable
// Dalam hal ini, Letter yang sudah dihapus sementara (soft delete) > 30 hari
Schedule::command('model:prune', [
    '--model' => [App\Models\Letter::class],
])->daily();
