<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            // Mendaftarkan Rute Modul IAM
            if (file_exists($iamRoutes = base_path('app/Modules/IAM/Routes/api.php'))) {
                Route::middleware('api')->prefix('api/iam')->group($iamRoutes);
            }

            // Mendaftarkan Rute Modul HRIS
            if (file_exists($hrisRoutes = base_path('app/Modules/HRIS/Routes/api.php'))) {
                Route::middleware('api')->prefix('api/hris')->group($hrisRoutes);
            }

            // Lakukan hal yang sama untuk Communication & Operations nanti
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'block.vpn' => \App\Http\Middleware\BlockVpnAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
