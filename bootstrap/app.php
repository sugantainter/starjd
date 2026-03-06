<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'creator' => \App\Http\Middleware\EnsureUserIsCreator::class,
            'brand' => \App\Http\Middleware\EnsureUserIsBrand::class,
            'agency' => \App\Http\Middleware\EnsureUserIsAgency::class,
            'studio_owner' => \App\Http\Middleware\EnsureUserIsStudioOwner::class,
            'paid' => \App\Http\Middleware\EnsureCreatorOrBrandHasPaid::class,
        ]);
        // PayU redirects here via POST without a CSRF token; we verify with PayU hash instead
        $middleware->validateCsrfTokens(except: [
            'payment/callback/success',
            'payment/callback/failure',
            'api/*',
            'auth/*',
            'login',
            'register',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            \Illuminate\Support\Facades\Log::warning('Unauthenticated API Access Attempt', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'cookies' => $request->cookies->all(),
                'has_session' => $request->hasSession(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            ]);

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'debug_info' => [
                        'cookies_received' => $request->cookies->keys(),
                        'has_session' => $request->hasSession(),
                        'headers' => $request->headers->all(),
                    ]
                ], 401);
            }
        });
    })->create();
