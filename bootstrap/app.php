<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\ForcePasswordChange;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register Activity Logger to web group
        $middleware->web(append: [
            \App\Http\Middleware\LogActivityMiddleware::class,
            \App\Http\Middleware\ForcePasswordChange::class,
        ]);
        
        // Daftarkan alias 'role' untuk CheckRole middleware.
        // Penggunaan di route: ->middleware('role:admin') atau ->middleware('role:admin,staff')
        $middleware->alias([
            'role'           => CheckRole::class,
            'force.password' => ForcePasswordChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
