<?php

use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', SetLocaleMiddleware::class);
        $middleware->validateCsrfTokens(except: [
            'participate/{ciNumber}',
            'validate-coupon',
            'landing-raspadita',
            'form-raspadita',
            'update-coupon',
            'check-coupons',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e) {
            \App\Services\ErrorNotificationService::notify($e);
        });
    })->create();
