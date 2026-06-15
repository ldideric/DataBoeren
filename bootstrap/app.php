<?php

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
        // Traefik terminates TLS and forwards to the app over plain HTTP on the
        // internal overlay network. Trust its X-Forwarded-* headers so the request
        // scheme is seen as HTTPS. Without this, signed-URL validation rebuilds the
        // URL as http:// and every magic link fails with "Invalid signature".
        $middleware->trustProxies(at: '*');
        $middleware->encryptCookies(except: ['functional_consent',]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
