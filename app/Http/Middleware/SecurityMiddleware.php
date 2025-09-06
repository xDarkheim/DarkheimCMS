<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверка на подозрительные запросы
        $this->detectSuspiciousActivity($request);

        // Rate limiting для защиты от DDoS
        $this->rateLimiting($request);

        // Фильтрация вредоносных символов в URL
        $this->filterMaliciousInput($request);

        // Блокировка известных вредоносных IP
        $this->blockMaliciousIPs($request);

        $response = $next($request);

        // Удаление информации о сервере из заголовков
        $this->sanitizeHeaders($response);

        return $response;
    }

    /**
     * Обнаружение подозрительной активности
     */
    private function detectSuspiciousActivity(Request $request): void
    {
        $suspicious_patterns = [
            '/\.\./i',                    // Directory traversal
            '/<script/i',                 // XSS attempts
            '/union.*select/i',           // SQL injection
            '/javascript:/i',             // JavaScript injection
            '/vbscript:/i',              // VBScript injection
            '/onload=/i',                // Event handler injection
            '/onerror=/i',               // Error handler injection
            '/%3Cscript/i',              // Encoded script tags
            '/\<iframe/i',               // Iframe injection
            '/eval\(/i',                 // Eval function
            '/base64_decode/i',          // Base64 decode attempts
            '/system\(/i',               // System command attempts
            '/exec\(/i',                 // Exec function attempts
            '/shell_exec/i',             // Shell execution
            '/phpinfo/i',                // PHP info disclosure
        ];

        $full_url = $request->fullUrl();
        $user_agent = $request->userAgent();
        $ip = $request->ip();

        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $full_url) || preg_match($pattern, $user_agent)) {
                Log::warning('Suspicious activity detected', [
                    'ip' => $ip,
                    'url' => $full_url,
                    'user_agent' => $user_agent,
                    'pattern' => $pattern,
                    'timestamp' => now()
                ]);

                // Увеличиваем счетчик подозрительной активности для IP
                $key = 'suspicious_activity_' . $ip;
                $count = Cache::get($key, 0);
                Cache::put($key, $count + 1, now()->addHours(24));

                // Блокируем IP после 5 подозрительных запросов
                if ($count >= 5) {
                    Cache::put('blocked_ip_' . $ip, true, now()->addDays(7));
                    abort(403, 'Access denied due to suspicious activity');
                }

                abort(400, 'Malicious request detected');
            }
        }
    }

    /**
     * Rate limiting для защиты от DDoS
     */
    private function rateLimiting(Request $request): void
    {
        $ip = $request->ip();
        $key = 'rate_limit_' . $ip;

        $requests = Cache::get($key, 0);

        // Увеличиваем лимит до 1000 запросов в минуту с одного IP (более разумно)
        if ($requests >= 1000) {
            Log::warning('Rate limit exceeded', ['ip' => $ip]);
            abort(429, 'Too Many Requests');
        }

        Cache::put($key, $requests + 1, now()->addMinute());
    }

    /**
     * Фильтрация вредоносного ввода
     */
    private function filterMaliciousInput(Request $request): void
    {
        $dangerous_chars = ['<', '>', '"', "'", '&', '%00', '%27', '%22'];

        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                foreach ($dangerous_chars as $char) {
                    if (strpos($value, $char) !== false && !$this->isAllowedContext($key)) {
                        Log::warning('Malicious input detected', [
                            'ip' => $request->ip(),
                            'field' => $key,
                            'value' => substr($value, 0, 100),
                            'char' => $char
                        ]);
                        abort(400, 'Invalid input detected');
                    }
                }
            }
        }
    }

    /**
     * Проверка разрешенного контекста для специальных символов
     */
    private function isAllowedContext(string $field): bool
    {
        $allowed_fields = ['content', 'description', 'message', 'body'];
        return in_array($field, $allowed_fields);
    }

    /**
     * Блокировка известных вредоносных IP
     */
    private function blockMaliciousIPs(Request $request): void
    {
        $ip = $request->ip();

        // Проверяем, заблокирован ли IP
        if (Cache::has('blocked_ip_' . $ip)) {
            Log::info('Blocked IP attempted access', ['ip' => $ip]);
            abort(403, 'Access denied');
        }

        // Список заблокированных IP-адресов (можно вынести в базу данных)
        $blocked_ips = [
            // Добавьте сюда известные вредоносные IP
        ];

        if (in_array($ip, $blocked_ips)) {
            Cache::put('blocked_ip_' . $ip, true, now()->addDays(30));
            Log::warning('Known malicious IP blocked', ['ip' => $ip]);
            abort(403, 'Access denied');
        }
    }

    /**
     * Очистка заголовков ответа
     */
    private function sanitizeHeaders(Response $response): void
    {
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('X-AspNet-Version');
        $response->headers->remove('X-AspNetMvc-Version');
    }
}
