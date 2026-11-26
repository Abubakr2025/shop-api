<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Защита от MIME-сниффинга
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Защита от Clickjacking (запрет встраивания в iframe)
        $response->headers->set('X-Frame-Options', 'DENY');
        // Запрет передавать Referer
        $response->headers->set('Referrer-Policy', 'no-referrer');
        // Принудительный HTTPS (HSTS)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        // Запрет кэширования персональных данных
        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
