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
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        /** @var Response $response */
        $response = $next($request);

        // Remove X-Powered-By from Symfony response headers
        $response->headers->remove('X-Powered-By');

        // Prevent Clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable browser XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information sent in headers
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict potentially sensitive browser features while allowing camera & geolocation
        $response->headers->set('Permissions-Policy', 'camera=*, geolocation=*, microphone=()');

        // HSTS (HTTP Strict Transport Security) - active when requested over HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
