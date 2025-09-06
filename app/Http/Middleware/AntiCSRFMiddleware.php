<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AntiCSRFMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем CSRF токен для POST, PUT, PATCH, DELETE запросов
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->validateCSRFToken($request);
        }

        // Проверяем Origin и Referer заголовки
        $this->validateOrigin($request);

        $response = $next($request);

        // Добавляем дополнительные заголовки защиты
        $this->addCSRFHeaders($response);

        return $response;
    }

    /**
     * Валидация CSRF токена
     */
    private function validateCSRFToken(Request $request): void
    {
        // Пропускаем GET запросы (они безопасны для CSRF)
        if ($request->isMethod('GET')) {
            return;
        }

        // Пропускаем API маршруты с авторизацией через токен
        if ($request->is('api/*') && $request->bearerToken()) {
            return;
        }

        // Пропускаем статические ресурсы
        if ($this->isStaticResource($request)) {
            return;
        }

        // Проверяем наличие сессии
        if (!$request->hasSession()) {
            abort(419, 'Session not available');
        }

        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
        $sessionToken = $request->session()->token();

        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            Log::warning('CSRF token mismatch', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'provided_token' => $token ? substr($token, 0, 10) . '...' : 'none'
            ]);

            abort(419, 'CSRF token mismatch');
        }
    }

    /**
     * Проверка на статические ресурсы
     */
    private function isStaticResource(Request $request): bool
    {
        $staticExtensions = [
            'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2',
            'ttf', 'eot', 'otf', 'webp', 'bmp', 'tiff', 'pdf', 'mp3', 'mp4', 'avi'
        ];

        $path = $request->getPathInfo();
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return in_array(strtolower($extension), $staticExtensions);
    }

    /**
     * Валидация Origin заголовка
     */
    private function validateOrigin(Request $request): void
    {
        $allowedHosts = [
            'darkheim.net',
            'www.darkheim.net',
            'localhost',
            '127.0.0.1'
        ];

        $origin = $request->header('Origin');
        $referer = $request->header('Referer');

        // Проверяем Origin заголовок
        if ($origin) {
            $originHost = parse_url($origin, PHP_URL_HOST);
            if (!in_array($originHost, $allowedHosts)) {
                Log::warning('Suspicious origin detected', [
                    'ip' => $request->ip(),
                    'origin' => $origin,
                    'url' => $request->fullUrl()
                ]);
                abort(403, 'Invalid origin');
            }
        }

        // Проверяем Referer для критических операций
        if ($request->isMethod('POST') && $referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            if (!in_array($refererHost, $allowedHosts)) {
                Log::warning('Suspicious referer detected', [
                    'ip' => $request->ip(),
                    'referer' => $referer,
                    'url' => $request->fullUrl()
                ]);
                abort(403, 'Invalid referer');
            }
        }
    }

    /**
     * Добавление заголовков защиты от CSRF
     */
    private function addCSRFHeaders(Response $response): void
    {
        // Устанавливаем SameSite для cookies
        $response->headers->set('Set-Cookie', 'SameSite=Strict; Secure; HttpOnly', false);

        // Добавляем заголовок для защиты от CSRF
        $response->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
