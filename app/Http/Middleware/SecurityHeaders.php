<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and apply OWASP-recommended security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Remove X-Powered-By if set by PHP runtime
        if (function_exists('header_remove') && ! headers_sent()) {
            header_remove('X-Powered-By');
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->remove('X-Powered-By');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'camera=*, geolocation=*, microphone=()');
            $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
        }

        return $response;
    }
}
