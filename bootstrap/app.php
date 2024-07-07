<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php'
    )
    ->withMiddleware(function ($middleware) {
        $middleware->validateCsrfTokens(except: [
            'auth/login',
        ]);

        // 添加 CORS 中间件
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

        // 添加自定义 JWT 中间件

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
