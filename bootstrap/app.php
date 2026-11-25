<?php

use App\Http\Middleware\EnsureOwnerRole;
use App\Http\Middleware\SetAdminLocale;
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
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'owner'        => EnsureOwnerRole::class,
            'admin.locale' => SetAdminLocale::class,
        ]);
        $middleware->redirectTo(
            guests: '/admin/login',
            users: '/admin/bookings'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
