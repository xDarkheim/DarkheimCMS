<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BruteForceProtectionMiddleware
{
    /**
     * Максимальное количество попыток входа
     */
    private const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Время блокировки в минутах
     */
    private const LOCKOUT_TIME = 30;

    /**
     * Время окна для подсчета попыток в минутах
     */
    private const ATTEMPT_WINDOW = 15;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем только запросы на авторизацию
        if ($this->isLoginAttempt($request)) {
            $this->checkBruteForceAttempts($request);
        }

        $response = $next($request);

        // Обрабатываем результат авторизации
        if ($this->isLoginAttempt($request)) {
            $this->handleLoginResponse($request, $response);
        }

        return $response;
    }

    /**
     * Проверка, является ли запрос попыткой входа
     */
    private function isLoginAttempt(Request $request): bool
    {
        return $request->isMethod('POST') && (
            $request->is('login') ||
            $request->is('api/login') ||
            $request->is('admin/login') ||
            str_contains($request->path(), 'auth')
        );
    }

    /**
     * Проверка на брутфорс атаки
     */
    private function checkBruteForceAttempts(Request $request): void
    {
        $ip = $request->ip();
        $email = $request->input('email', '');

        // Проверяем блокировку по IP
        if ($this->isIpBlocked($ip)) {
            $this->logBruteForceAttempt($request, 'IP blocked');
            abort(429, 'Too many login attempts. Please try again later.');
        }

        // Проверяем блокировку по email
        if ($email && $this->isEmailBlocked($email)) {
            $this->logBruteForceAttempt($request, 'Email blocked');
            abort(429, 'Account temporarily locked due to multiple failed login attempts.');
        }

        // Проверяем общее количество попыток с IP
        if ($this->getTotalIpAttempts($ip) >= self::MAX_LOGIN_ATTEMPTS) {
            $this->blockIp($ip);
            $this->logBruteForceAttempt($request, 'IP auto-blocked');
            abort(429, 'Too many login attempts from this IP address.');
        }
    }

    /**
     * Обработка ответа после попытки входа
     */
    private function handleLoginResponse(Request $request, Response $response): void
    {
        $ip = $request->ip();
        $email = $request->input('email', '');

        // Если вход неуспешный (статус 401, 422 или редирект с ошибкой)
        if ($this->isFailedLogin($response)) {
            $this->recordFailedAttempt($ip, $email);

            // Проверяем, нужно ли заблокировать IP или email
            $ipAttempts = $this->getIpAttempts($ip);
            $emailAttempts = $email ? $this->getEmailAttempts($email) : 0;

            if ($ipAttempts >= self::MAX_LOGIN_ATTEMPTS) {
                $this->blockIp($ip);
            }

            if ($email && $emailAttempts >= self::MAX_LOGIN_ATTEMPTS) {
                $this->blockEmail($email);
            }

            $this->logFailedLogin($request, $ipAttempts, $emailAttempts);
        } else {
            // Успешный вход - очищаем счетчики
            $this->clearAttempts($ip, $email);
        }
    }

    /**
     * Проверка, является ли ответ неуспешным входом
     */
    private function isFailedLogin(Response $response): bool
    {
        return in_array($response->getStatusCode(), [401, 422]) ||
               ($response->isRedirection() && str_contains($response->headers->get('Location', ''), 'error'));
    }

    /**
     * Проверка блокировки IP
     */
    private function isIpBlocked(string $ip): bool
    {
        return Cache::has("ip_blocked_{$ip}");
    }

    /**
     * Проверка блокировки email
     */
    private function isEmailBlocked(string $email): bool
    {
        return Cache::has("email_blocked_{$email}");
    }

    /**
     * Получение количества попыток с IP
     */
    private function getIpAttempts(string $ip): int
    {
        return Cache::get("login_attempts_ip_{$ip}", 0);
    }

    /**
     * Получение общего количества попыток с IP
     */
    private function getTotalIpAttempts(string $ip): int
    {
        return Cache::get("total_attempts_ip_{$ip}", 0);
    }

    /**
     * Получение количества попыток для email
     */
    private function getEmailAttempts(string $email): int
    {
        return Cache::get("login_attempts_email_{$email}", 0);
    }

    /**
     * Запись неудачной попытки
     */
    private function recordFailedAttempt(string $ip, string $email): void
    {
        // Увеличиваем счетчик для IP
        $ipKey = "login_attempts_ip_{$ip}";
        $ipAttempts = Cache::get($ipKey, 0) + 1;
        Cache::put($ipKey, $ipAttempts, now()->addMinutes(self::ATTEMPT_WINDOW));

        // Увеличиваем общий счетчик для IP
        $totalIpKey = "total_attempts_ip_{$ip}";
        $totalIpAttempts = Cache::get($totalIpKey, 0) + 1;
        Cache::put($totalIpKey, $totalIpAttempts, now()->addMinutes(self::ATTEMPT_WINDOW));

        // Увеличиваем счетчик для email
        if ($email) {
            $emailKey = "login_attempts_email_{$email}";
            $emailAttempts = Cache::get($emailKey, 0) + 1;
            Cache::put($emailKey, $emailAttempts, now()->addMinutes(self::ATTEMPT_WINDOW));
        }
    }

    /**
     * Блокировка IP
     */
    private function blockIp(string $ip): void
    {
        Cache::put("ip_blocked_{$ip}", true, now()->addMinutes(self::LOCKOUT_TIME));

        Log::warning('IP blocked due to brute force attempts', [
            'ip' => $ip,
            'blocked_until' => now()->addMinutes(self::LOCKOUT_TIME)
        ]);
    }

    /**
     * Блокировка email
     */
    private function blockEmail(string $email): void
    {
        Cache::put("email_blocked_{$email}", true, now()->addMinutes(self::LOCKOUT_TIME));

        Log::warning('Email blocked due to brute force attempts', [
            'email' => $email,
            'blocked_until' => now()->addMinutes(self::LOCKOUT_TIME)
        ]);
    }

    /**
     * Очистка счетчиков после успешного входа
     */
    private function clearAttempts(string $ip, string $email): void
    {
        Cache::forget("login_attempts_ip_{$ip}");
        Cache::forget("total_attempts_ip_{$ip}");

        if ($email) {
            Cache::forget("login_attempts_email_{$email}");
        }
    }

    /**
     * Логирование неудачной попытки входа
     */
    private function logFailedLogin(Request $request, int $ipAttempts, int $emailAttempts): void
    {
        Log::info('Failed login attempt', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'user_agent' => $request->userAgent(),
            'ip_attempts' => $ipAttempts,
            'email_attempts' => $emailAttempts,
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ]);
    }

    /**
     * Логирование попытки брутфорса
     */
    private function logBruteForceAttempt(Request $request, string $reason): void
    {
        Log::critical('Brute force attempt blocked', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'reason' => $reason,
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ]);
    }
}
