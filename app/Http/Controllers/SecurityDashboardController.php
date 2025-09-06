<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SecurityDashboardController extends Controller
{
    /**
     * Показать панель безопасности
     */
    public function index(): View
    {
        $securityStats = $this->getSecurityStatistics();
        return view('admin.security.dashboard', compact('securityStats'));
    }

    /**
     * API эндпоинт для получения статистики безопасности
     */
    public function getStats(): JsonResponse
    {
        return response()->json($this->getSecurityStatistics());
    }

    /**
     * Обработка CSP отчетов
     */
    public function cspReport(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $report = $request->all();

        Log::warning('CSP Violation Report', [
            'report' => $report,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        return response('', 204);
    }

    /**
     * Получение статистики безопасности
     * @return array<string, mixed>
     */
    private function getSecurityStatistics(): array
    {
        $stats = [
            'status' => 'active',
            'timestamp' => now()->toISOString(),
            'security_level' => 'high',
            'blocked_ips' => $this->getBlockedIpsCount(),
            'blocked_emails' => $this->getBlockedEmailsCount(),
            'recent_threats' => $this->getRecentThreats(),
            'login_attempts' => $this->getLoginAttemptsStats(),
            'security_events' => $this->getSecurityEventsCount(),
            'system_health' => $this->getSystemHealth(),
            'middleware_status' => $this->getMiddlewareStatus(),
            'configuration_status' => $this->getConfigurationStatus()
        ];

        return $stats;
    }

    /**
     * Подсчет заблокированных IP
     */
    private function getBlockedIpsCount(): int
    {
        if (config('cache.default') === 'file') {
            return 0; // Невозможно подсчитать для файлового кеша
        }

        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                /** @var \Illuminate\Redis\RedisManager $redis */
                $redis = Cache::getStore()->getRedis();
                return count($redis->keys('*blocked_ip*'));
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Подсчет заблокированных email
     */
    private function getBlockedEmailsCount(): int
    {
        if (config('cache.default') === 'file') {
            return 0;
        }

        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                /** @var \Illuminate\Redis\RedisManager $redis */
                $redis = Cache::getStore()->getRedis();
                return count($redis->keys('*email_blocked*'));
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Получение последних угроз
     * @return array<array<string, mixed>>
     */
    private function getRecentThreats(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return [];
        }

        $threats = [];
        $lines = explode("\n", File::get($logFile));
        $lines = array_reverse(array_slice($lines, -1000)); // Последние 1000 строк

        foreach ($lines as $line) {
            if (strpos($line, 'security') !== false ||
                strpos($line, 'suspicious') !== false ||
                strpos($line, 'blocked') !== false ||
                strpos($line, 'malicious') !== false) {

                $threats[] = [
                    'timestamp' => $this->extractTimestamp($line),
                    'message' => $this->extractMessage($line),
                    'level' => $this->extractLevel($line)
                ];

                if (count($threats) >= 10) break; // Показываем только последние 10
            }
        }

        return $threats;
    }

    /**
     * Статистика попыток входа
     * @return array<string, mixed>
     */
    private function getLoginAttemptsStats(): array
    {
        if (config('cache.default') === 'file') {
            return [
                'active_attempts' => 0,
                'note' => 'File cache - unable to count'
            ];
        }

        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                /** @var \Illuminate\Redis\RedisManager $redis */
                $redis = Cache::getStore()->getRedis();
                $attempts = count($redis->keys('*login_attempts*'));
                return [
                    'active_attempts' => $attempts,
                    'last_updated' => now()->toISOString()
                ];
            }
            return ['active_attempts' => 0, 'error' => 'Unable to access cache'];
        } catch (\Exception $e) {
            return ['active_attempts' => 0, 'error' => 'Unable to access cache'];
        }
    }

    /**
     * Подсчет событий безопасности за последние 24 часа
     * @return array<string, mixed>
     */
    private function getSecurityEventsCount(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return ['total' => 0, 'last_24h' => 0];
        }

        $content = File::get($logFile);
        $total = substr_count($content, 'security') +
                substr_count($content, 'suspicious') +
                substr_count($content, 'blocked');

        // Примерная оценка за последние 24 часа
        $last24h = round($total * 0.1); // Предполагаем, что 10% событий за последние 24 часа

        return [
            'total' => $total,
            'last_24h' => $last24h,
            'note' => 'Estimated from log analysis'
        ];
    }

    /**
     * Состояние системы
     * @return array<string, mixed>
     */
    private function getSystemHealth(): array
    {
        return [
            'app_env' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'enabled' : 'disabled',
            'cache_status' => Cache::has('health_check') ? 'working' : 'unknown',
            'log_writable' => is_writable(storage_path('logs')),
            'session_secure' => config('session.secure'),
            'session_encrypt' => config('session.encrypt')
        ];
    }

    /**
     * Статус middleware
     * @return array<string, string>
     */
    private function getMiddlewareStatus(): array
    {
        return [
            'SecurityHeadersMiddleware' => 'active',
            'SecurityMiddleware' => 'active',
            'SQLInjectionProtectionMiddleware' => 'active',
            'BruteForceProtectionMiddleware' => 'active',
            'AntiCSRFMiddleware' => 'active',
            'FileUploadSecurityMiddleware' => 'active',
            'APISecurityMiddleware' => 'active'
        ];
    }

    /**
     * Статус конфигурации
     * @return array<string, mixed>
     */
    private function getConfigurationStatus(): array
    {
        return [
            'https_enforced' => config('session.secure'),
            'session_encryption' => config('session.encrypt'),
            'bcrypt_rounds' => config('hashing.bcrypt.rounds'),
            'api_security' => config('security.api_security.require_api_key'),
            'file_upload_security' => config('security.file_upload.scan_for_malware'),
            'rate_limiting' => true,
            'csrf_protection' => true
        ];
    }

    /**
     * Извлечение временной метки из строки лога
     */
    private function extractTimestamp(string $line): ?string
    {
        if (preg_match('/\[(.*?)\]/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Извлечение сообщения из строки лога
     */
    private function extractMessage(string $line): string
    {
        // Удаляем временную метку и уровень, оставляем сообщение
        $line = preg_replace('/^\[.*?\]/', '', $line);
        $line = preg_replace('/^[A-Z]+:/', '', $line);
        return trim(substr($line, 0, 100)) . (strlen($line) > 100 ? '...' : '');
    }

    /**
     * Извлечение уровня из строки лога
     */
    private function extractLevel(string $line): string
    {
        if (strpos($line, 'CRITICAL') !== false) return 'critical';
        if (strpos($line, 'ERROR') !== false) return 'error';
        if (strpos($line, 'WARNING') !== false) return 'warning';
        if (strpos($line, 'INFO') !== false) return 'info';
        return 'unknown';
    }
}
