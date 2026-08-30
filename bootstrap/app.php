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
        $middleware->web(append: [
            \App\Http\Middleware\MaintenanceMode::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\DetectCurrency::class,
        ]);
        $middleware->alias([
            'guest.custom'     => \App\Http\Middleware\RedirectIfAuthenticatedCustom::class,
            'auth.custom'      => \App\Http\Middleware\RedirectIfNotAuthenticatedCustom::class,
            'admin.auth'       => \App\Http\Middleware\AdminAuthenticated::class,
            'admin.guest'      => \App\Http\Middleware\AdminGuest::class,
            'profile.complete' => \App\Http\Middleware\RequireProfileComplete::class,
            'gate.expired'     => \App\Http\Middleware\GateExpiredSubscription::class,
        ]);

        // Paymob's server-to-server webhook can't carry our CSRF token
        // (Batch 6, audit Risk D-3). This is the ONLY route exempted.
        $middleware->validateCsrfTokens(except: [
            'paymob/webhook',
        ]);

        // No proxy/TrustProxies config existed before Batch 6. Needed for
        // local tunnel testing (ngrok/cloudflared etc. terminate TLS and
        // forward plain HTTP with X-Forwarded-Proto: https) — without this,
        // route()-generated URLs (e.g. Paymob's notification_url) come back
        // as http:// even though the public tunnel URL is https://, since
        // Laravel won't trust the forwarded-proto header from an untrusted
        // proxy. `at: '*'` is appropriate for controlled tunnel testing; for
        // a real production deploy behind a specific reverse proxy/CDN,
        // narrow this to that proxy's actual IP range instead.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
