<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SQLInjectionProtectionMiddleware
{
    /**
     * Очень специфичные паттерны для обнаружения реальных SQL инъекций
     * @var array<string>
     */
    private array $criticalSqlPatterns = [
        '/(\s|^)(union\s+all\s+select|union\s+select)\s/i',
        '/(\s|^)(drop\s+table|drop\s+database)\s/i',
        '/(\s|^)(delete\s+from\s+\w+\s+where\s+1\s*=\s*1)/i',
        '/(\s|^)(insert\s+into\s+\w+.*values\s*\(.*\))/i',
        '/(\s|^)(update\s+\w+\s+set\s+.*where\s+1\s*=\s*1)/i',
        '/(\';|\"\s*;|\s+;).*drop\s/i',
        '/(\';|\"\s*;|\s+;).*delete\s/i',
        '/(\';|\"\s*;|\s+;).*insert\s/i',
        '/(\';|\"\s*;|\s+;).*update\s/i',
        '/load_file\s*\(/i',
        '/into\s+outfile\s/i',
        '/into\s+dumpfile\s/i',
        '/(\s|^)concat\s*\(\s*char\s*\(/i',
        '/0x[0-9a-f]{8,}/i', // длинные hex строки
        '/benchmark\s*\(\s*\d+\s*,/i',
        '/sleep\s*\(\s*\d+\s*\)/i',
        '/waitfor\s+delay\s/i',
        '/pg_sleep\s*\(/i'
    ];

    /**
     * Критичные XSS паттерны
     * @var array<string>
     */
    private array $criticalXssPatterns = [
        '/<script[^>]*>.*?<\/script>/is',
        '/<iframe[^>]*src\s*=\s*["\']javascript:/i',
        '/on\w+\s*=\s*["\'].*?javascript:/i',
        '/<img[^>]*src\s*=\s*["\']javascript:/i',
        '/javascript:\s*eval\s*\(/i',
        '/data:text\/html[^>]*base64/i'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Пропускаем консольные команды и API с токенами
        if ($request->is('_debugbar/*') ||
            $request->is('telescope/*') ||
            $this->isInternalRequest($request)) {
            return $next($request);
        }

        // Проверяем только пользовательский ввод, а не системные операции
        $this->validateUserInput($request);

        return $next($request);
    }

    /**
     * Проверка на внутренние запросы Laravel
     */
    private function isInternalRequest(Request $request): bool
    {
        // Пропускаем внутренние операции Laravel
        $internalPaths = [
            '_debugbar', 'telescope', '_ignition', 'livewire',
            'nova-api', 'horizon', 'pulse'
        ];

        foreach ($internalPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }

        // Пропускаем AJAX запросы с CSRF токеном
        if ($request->ajax() && $request->header('X-CSRF-TOKEN')) {
            return true;
        }

        return false;
    }

    /**
     * Валидация только пользовательского ввода
     */
    private function validateUserInput(Request $request): void
    {
        // Проверяем только форматы данных, которые могут содержать инъекции
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            if (is_string($value) && strlen($value) > 10) { // только длинные строки
                $this->checkForCriticalThreats($key, $value, $request);
            } elseif (is_array($value)) {
                $this->validateArrayInput($key, $value, $request);
            }
        }

        // Проверяем URL только на очень подозрительные паттерны
        $this->validateUrlForCriticalThreats($request);
    }

    /**
     * Валидация массивов входных данных
     * @param array<mixed> $values
     */
    private function validateArrayInput(string $key, array $values, Request $request): void
    {
        foreach ($values as $subKey => $value) {
            if (is_string($value) && strlen($value) > 10) {
                $this->checkForCriticalThreats("{$key}[{$subKey}]", $value, $request);
            } elseif (is_array($value)) {
                $this->validateArrayInput("{$key}[{$subKey}]", $value, $request);
            }
        }
    }

    /**
     * Проверка только на критичные угрозы
     */
    private function checkForCriticalThreats(string $field, string $value, Request $request): void
    {
        // Пропускаем поля с разрешенным контентом
        $allowedFields = [
            'content', 'description', 'message', 'body', 'text',
            'bio', 'about', 'summary', 'details', 'note',
            'excerpt',           // For news excerpts
            'title',             // For news titles
            'meta_title',        // For SEO meta titles
            'meta_description',  // For SEO meta descriptions
            'author',            // For author names that might contain apostrophes
            'category',          // For categories that might contain ampersands
            'short_description', // For portfolio short descriptions
            'technologies',      // For portfolio technologies
            'client',            // For portfolio client names
            'is_published',      // For toggle published status
            'is_featured',       // For toggle featured status
            'is_active',         // For toggle active status
            'status',            // For status changes
            'published_at',      // For publication dates
            'name',              // For names
            'email',             // For email fields
            'phone',             // For phone numbers
            'address',           // For addresses
            'url',               // For URLs
            'image_url',         // For image URLs
            'tags',              // For tags
            'slug'               // For slugs
        ];

        if (in_array($field, $allowedFields)) {
            // Для контентных полей проверяем только критичные XSS
            $this->checkForCriticalXss($field, $value, $request);
            return;
        }

        // Для остальных полей проверяем SQL инъекции
        foreach ($this->criticalSqlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                Log::critical('Critical SQL Injection attempt detected', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'field' => $field,
                    'value' => substr($value, 0, 200),
                    'pattern' => $pattern,
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()
                ]);

                // Блокируем IP на 24 часа
                cache()->put('blocked_ip_' . $request->ip(), true, now()->addHours(24));

                abort(403, 'Critical security violation detected');
            }
        }

        // Проверяем критичные XSS атаки
        $this->checkForCriticalXss($field, $value, $request);
    }

    /**
     * Проверка на критичные XSS атаки
     */
    private function checkForCriticalXss(string $field, string $value, Request $request): void
    {
        foreach ($this->criticalXssPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                Log::warning('Critical XSS attempt detected', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'field' => $field,
                    'value' => substr($value, 0, 200),
                    'pattern' => $pattern,
                    'user_agent' => $request->userAgent()
                ]);

                abort(400, 'Malicious content detected');
            }
        }
    }

    /**
     * Валидация URL только на критичные угрозы
     */
    private function validateUrlForCriticalThreats(Request $request): void
    {
        $url = $request->getRequestUri();

        // Проверяем только на очень опасные паттерны в URL
        $criticalUrlPatterns = [
            '/\.\.\/.*\.\.\//',  // множественный directory traversal
            '/%00/',             // null byte
            '/\?.*union.*select/i',
            '/\?.*drop.*table/i'
        ];

        foreach ($criticalUrlPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                Log::warning('Critical URL attack attempt', [
                    'ip' => $request->ip(),
                    'url' => $url,
                    'pattern' => $pattern
                ]);
                abort(403, 'Invalid request detected');
            }
        }
    }
}
