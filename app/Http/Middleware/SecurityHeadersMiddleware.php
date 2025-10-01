<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    private string $nonce;

    public function __construct()
    {
        $this->nonce = base64_encode(random_bytes(16));
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Устанавливаем все необходимые заголовки безопасности
        $this->setSecurityHeaders($response, $request);

        // Добавляем nonce в view для использования в шаблонах
        view()->share('csp_nonce', $this->nonce);

        return $response;
    }

    /**
     * Установка всех заголовков безопасности
     */
    private function setSecurityHeaders(Response $response, Request $request): void
    {
        // Защита от кликджекинга
        $response->headers->set('X-Frame-Options', 'DENY');

        // Строгая политика безопасности контента
        $csp = $this->buildContentSecurityPolicy($request);
        $response->headers->set('Content-Security-Policy', $csp);

        // Защита от MIME-type снифинга
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Принудительное использование HTTPS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Защита от XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Контроль передачи referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Ограничение разрешений браузера (removing deprecated features)
        $response->headers->set('Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), fullscreen=(self), sync-xhr=()');

        // Дополнительные заголовки безопасности
        $response->headers->set('X-DNS-Prefetch-Control', 'off');
        $response->headers->set('X-Download-Options', 'noopen');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Удаление информации о сервере
        $this->removeServerInfo($response);
    }

    /**
     * Построение Content Security Policy
     */
    private function buildContentSecurityPolicy(Request $request): string
    {
        $isHttps = $request->isSecure();
        $protocol = $isHttps ? 'https' : 'http';

        // Для админки ослабляем CSP чтобы Font Awesome работал
        if ($request->is('admin/*') || $request->is('admin')) {
            return "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http:; " .
                   "style-src 'self' 'unsafe-inline' https: http:; " .
                   "font-src 'self' https: http: data:; " .
                   "img-src 'self' https: http: data: blob:; " .
                   "connect-src 'self' https: http:; " .
                   "media-src 'self' https: http:; " .
                   "object-src 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self'; " .
                   "frame-ancestors 'none';";
        }

        // Для остального сайта строгая CSP с правильными URL
        return "default-src 'self'; " .
               "script-src 'self' 'nonce-{$this->nonce}' 'unsafe-inline' 'unsafe-eval' {$protocol}://cdn.jsdelivr.net {$protocol}://cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' {$protocol}://fonts.googleapis.com {$protocol}://fonts.bunny.net {$protocol}://cdn.jsdelivr.net {$protocol}://cdnjs.cloudflare.com {$protocol}://use.fontawesome.com {$protocol}://pro.fontawesome.com; " .
               "font-src 'self' {$protocol}://fonts.gstatic.com {$protocol}://fonts.bunny.net {$protocol}://cdn.jsdelivr.net {$protocol}://cdnjs.cloudflare.com {$protocol}://use.fontawesome.com {$protocol}://pro.fontawesome.com; " .
               "img-src * data: blob: 'unsafe-inline'; " .
               "connect-src 'self' {$protocol}://api.darkheim.net; " .
               "media-src 'self' https: http:; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'; " .
               "frame-src 'none'; " .
               "frame-ancestors 'none'; " .
               "worker-src 'self'; " .
               "manifest-src 'self'; " .
               "upgrade-insecure-requests;";
    }

    /**
     * Удаление информации о сервере
     */
    private function removeServerInfo(Response $response): void
    {
        $headers_to_remove = [
            'Server',
            'X-Powered-By',
            'X-AspNet-Version',
            'X-AspNetMvc-Version',
            'X-Generator',
            'X-Drupal-Cache',
            'X-Varnish'
        ];

        foreach ($headers_to_remove as $header) {
            $response->headers->remove($header);
        }
    }
}
