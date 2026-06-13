<?php

use App\Http\Middleware\EnsureCanAccessKelas;
use App\Http\Middleware\RememberKelasNavigation;
use App\Http\Middleware\RememberCapaianNavigation;
use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetTeamUrlDefaults::class,
            RememberKelasNavigation::class,
            RememberCapaianNavigation::class,
        ]);
        $middleware->alias([
            'kelas.access' => EnsureCanAccessKelas::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // $exceptions->render(function (Throwable $e) {
        //     return response()->view('errors.plain', [
        //         'message' => $e->getMessage(),
        //     ], 500);
        // });
    })->create();
