<?php
// File: bootstrap/app.php - Update untuk register middleware

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register alias middleware untuk role checking
        $middleware->alias([
            'role.admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'role.dokter' => \App\Http\Middleware\EnsureDokterRole::class,
            'role.user' => \App\Http\Middleware\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();