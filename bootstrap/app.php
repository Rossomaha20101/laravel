<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthForest; // ← 1. Импортируем ваш middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ← 2. Регистрируем алиас для удобного использования в маршрутах
        $middleware->alias([
            'auth.forest' => AuthForest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ← 3. Здесь можно настроить обработку ошибок (пока оставляем пустым)
    })->create();