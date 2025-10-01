<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\BruteForceProtectionMiddleware;
use Symfony\Component\HttpFoundation\Response;

class BruteForceProtectionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Тест пропуска не-login запросов
     */
    public function test_allows_non_login_requests(): void
    {
        $request = Request::create('/api/users', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new BruteForceProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('User data');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест обработки первой попытки логина
     */
    public function test_allows_first_login_attempt(): void
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        $middleware = new BruteForceProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('', 401); // Неудачный логин
        });

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Тест блокировки IP после превышения лимита попыток
     */
    public function test_blocks_ip_after_max_attempts(): void
    {
        $ip = '192.168.1.101';

        // Симулируем 5 неудачных попыток
        Cache::put("login_attempts_ip_{$ip}", 5, now()->addMinutes(15));
        Cache::put("total_attempts_ip_{$ip}", 5, now()->addMinutes(15));

        $request = Request::create('/api/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);
        $request->server->set('REMOTE_ADDR', $ip);

        $middleware = new BruteForceProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Too many login attempts from this IP address');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест блокировки заблокированного IP
     */
    public function test_blocks_already_blocked_ip(): void
    {
        $ip = '192.168.1.102';
        Cache::put("ip_blocked_{$ip}", true, now()->addMinutes(30));

        $request = Request::create('/api/login', 'POST');
        $request->server->set('REMOTE_ADDR', $ip);

        $middleware = new BruteForceProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Too many login attempts. Please try again later.');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест блокировки email после превышения лимита
     */
    public function test_blocks_email_after_max_attempts(): void
    {
        $email = 'test@example.com';
        Cache::put("email_blocked_{$email}", true, now()->addMinutes(30));

        $request = Request::create('/api/login', 'POST', [
            'email' => $email,
            'password' => 'password'
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.103');

        $middleware = new BruteForceProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Account temporarily locked');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест очистки попыток после успешного логина
     */
    public function test_clears_attempts_after_successful_login(): void
    {
        $ip = '192.168.1.104';
        $email = 'test@example.com';

        // Устанавливаем некоторые попытки
        Cache::put("login_attempts_ip_{$ip}", 3, now()->addMinutes(15));
        Cache::put("login_attempts_email_{$email}", 2, now()->addMinutes(15));

        $request = Request::create('/api/login', 'POST', [
            'email' => $email,
            'password' => 'correct-password'
        ]);
        $request->server->set('REMOTE_ADDR', $ip);

        $middleware = new BruteForceProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Login successful', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());

        // Проверяем, что попытки очищены (нужно добавить метод для проверки)
        $this->assertFalse(Cache::has("login_attempts_ip_{$ip}"));
        $this->assertFalse(Cache::has("login_attempts_email_{$email}"));
    }

    /**
     * Тест увеличения счетчика после неудачного логина
     */
    public function test_increments_counter_after_failed_login(): void
    {
        $ip = '192.168.1.105';
        $email = 'test@example.com';

        $request = Request::create('/api/login', 'POST', [
            'email' => $email,
            'password' => 'wrong-password'
        ]);
        $request->server->set('REMOTE_ADDR', $ip);

        $middleware = new BruteForceProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Login failed', 401);
        });

        $this->assertEquals(401, $response->getStatusCode());

        // Проверяем, что счетчики увеличились
        $this->assertEquals(1, Cache::get("login_attempts_ip_{$ip}"));
        $this->assertEquals(1, Cache::get("login_attempts_email_{$email}"));
    }

    /**
     * Тест пропуска auth маршрутов без POST
     */
    public function test_skips_non_post_auth_routes(): void
    {
        $request = Request::create('/api/login', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.106');

        $middleware = new BruteForceProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Login form');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест определения login попыток по URL
     */
    public function test_detects_login_attempts_by_url(): void
    {
        $authUrls = [
            '/api/login',
            '/admin/login',
            '/auth/login'
        ];

        foreach ($authUrls as $url) {
            $request = Request::create($url, 'POST');
            $request->server->set('REMOTE_ADDR', '127.0.0.1');

            $middleware = new BruteForceProtectionMiddleware();

            $response = $middleware->handle($request, function ($req) {
                return new Response('', 401);
            });

            $this->assertEquals(401, $response->getStatusCode());
        }
    }
}
