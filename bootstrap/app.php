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

    ->withMiddleware(function ($middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all reverse proxies (Replit proxy, nginx, etc.)
        $middleware->trustProxies(at: '*');

        // Exclude 'locale' from cookie encryption so it can be read as plain text.
        // Encrypted cookies require the browser to send them back bit-for-bit; a
        // plain locale cookie is safe to expose (it only contains 'ar', 'en', or 'fr').
        $middleware->encryptCookies(except: ['locale']);

        // SetLocale MUST run inside the web group (after StartSession).
        // append() adds to global middleware which runs before StartSession.
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
