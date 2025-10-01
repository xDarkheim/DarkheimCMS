<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class APISecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Пропускаем админские маршруты, защищенные Sanctum
        if ($this->isAdminRoute($request)) {
            return $next($request);
        }

        // Проверяем API ключ если требуется
        if (config('security.api_security.require_api_key', true)) {
            $this->validateApiKey($request);
        }

        // Проверяем CORS
        $this->validateCors($request);

        // Rate limiting для API
        $this->apiRateLimit($request);

        // Валидация JSON для API запросов
        if ($request->isJson()) {
            $this->validateJsonPayload($request);
        }

        $response = $next($request);

        // Добавляем заголовки безопасности для API
        $this->addApiSecurityHeaders($response);

        return $response;
    }

    /**
     * Валидация API ключа
     */
    private function validateApiKey(Request $request): void
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');

        if (!$apiKey) {
            Log::warning('API request without key', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);
            abort(401, 'API key required');
        }

        // Проверяем валидность API ключа
        if (!$this->isValidApiKey($apiKey)) {
            Log::warning('Invalid API key used', [
                'ip' => $request->ip(),
                'api_key' => substr($apiKey, 0, 8) . '...',
                'url' => $request->fullUrl()
            ]);
            abort(401, 'Invalid API key');
        }
    }

    /**
     * Проверка валидности API ключа
     */
    private function isValidApiKey(string $apiKey): bool
    {
        // Здесь можно реализовать проверку через базу данных
        // Для примера используем простую проверку
        $validKeys = [
            config('app.key'),
            config('security.api_security.api_key'),
            // Добавьте ваши API ключи
        ];

        return in_array($apiKey, array_filter($validKeys));
    }

    /**
     * Валидация CORS
     */
    private function validateCors(Request $request): void
    {
        $origin = $request->header('Origin');
        $allowedOrigins = config('security.api_security.allowed_origins', []);

        if ($origin && !in_array($origin, $allowedOrigins)) {
            Log::warning('CORS violation detected', [
                'ip' => $request->ip(),
                'origin' => $origin,
                'url' => $request->fullUrl()
            ]);
            abort(403, 'CORS policy violation');
        }
    }

    /**
     * API Rate Limiting
     */
    private function apiRateLimit(Request $request): void
    {
        $ip = $request->ip();
        $apiKey = $request->header('X-API-Key') ?? 'anonymous';
        $key = "api_rate_limit_{$ip}_{$apiKey}";

        $maxRequests = config('security.api_security.rate_limit_per_minute', 60);
        $requests = Cache::get($key, 0);

        if ($requests >= $maxRequests) {
            Log::warning('API rate limit exceeded', [
                'ip' => $ip,
                'api_key' => substr($apiKey, 0, 8) . '...',
                'requests' => $requests
            ]);
            abort(429, 'API rate limit exceeded');
        }

        Cache::put($key, $requests + 1, now()->addMinute());
    }

    /**
     * Валидация JSON payload
     */
    private function validateJsonPayload(Request $request): void
    {
        $content = $request->getContent();

        // Проверка размера JSON
        if (strlen($content) > 1048576) { // 1MB
            Log::warning('Large JSON payload detected', [
                'ip' => $request->ip(),
                'size' => strlen($content)
            ]);
            abort(413, 'Payload too large');
        }

        // Проверка глубины JSON
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Invalid JSON payload', [
                'ip' => $request->ip(),
                'error' => json_last_error_msg()
            ]);
            abort(400, 'Invalid JSON');
        }

        // Проверка на подозрительные паттерны в JSON
        $this->scanJsonForThreats($content, $request);
    }

    /**
     * Сканирование JSON на угрозы
     */
    private function scanJsonForThreats(string $content, Request $request): void
    {
        $threatPatterns = [
            '/eval\s*\(/i',
            '/function\s*\(/i',
            '/javascript:/i',
            '/<script/i',
            '/base64_decode/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/file_get_contents/i',
            '/curl_exec/i',
            '/shell_exec/i'
        ];

        foreach ($threatPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::critical('Malicious JSON payload detected', [
                    'ip' => $request->ip(),
                    'pattern' => $pattern,
                    'payload_preview' => substr($content, 0, 200)
                ]);
                abort(400, 'Malicious payload detected');
            }
        }
    }

    /**
     * Добавление заголовков безопасности для API
     */
    private function addApiSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        // Удаляем информацию о сервере
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
    }

    /**
     * Проверка, является ли маршрут админским
     */
    private function isAdminRoute(Request $request): bool
    {
        return $request->is('api/admin/*') ||
               $request->is('admin/*') ||
               $request->header('Authorization'); // Если есть Bearer токен
    }
}
