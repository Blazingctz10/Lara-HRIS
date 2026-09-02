<?php

require_once __DIR__.'/../app/intl_compat.php';

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
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi telah kedaluwarsa. Silakan masukkan kredensial dan klik Masuk kembali.',
            ]);
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if (
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
            ) {
                return null;
            }

            return response(
                '<div style="font-family:sans-serif;padding:30px;background:#1e1e2d;color:#fff;min-height:100vh;">' .
                '<h1 style="color:#ff6b6b;margin-top:0;">⚠️ Detail Error Terdeteksi</h1>' .
                '<p style="font-size:16px;"><strong>Pesan:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
                '<p style="font-size:14px;color:#a0aec0;"><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' : <strong>Baris ' . $e->getLine() . '</strong></p>' .
                '<hr style="border-color:#2d3748;margin:20px 0;">' .
                '<h3>Stack Trace:</h3>' .
                '<pre style="background:#0f172a;color:#38bdf8;padding:15px;border-radius:8px;overflow:auto;font-size:13px;line-height:1.5;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>' .
                '</div>',
                500
            );
        });
    })->create();
