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
                'email' => '[AUTH003] Sesi telah kedaluwarsa. Silakan muat ulang dan masuk kembali.',
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

            $errorInfo = \App\Support\HrisErrorCode::fromThrowable($e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => $errorInfo['code'],
                    'title' => $errorInfo['title'],
                    'message' => $errorInfo['description'],
                    'action' => $errorInfo['action'],
                ], 500);
            }

            return response()->view('errors.500', [
                'exceptionInfo' => $errorInfo,
                'exception' => $e,
            ], 500);
        });
    })->create();
