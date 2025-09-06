<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\SecurityMiddleware;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // Очищаем кеш перед каждым тестом
    }

    /**
     * Тест обычного безопасного запроса
     */
    public function test_allows_normal_requests(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new SecurityMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Тест обнаружения SQL инъекций в URL
     */
    public function test_detects_sql_injection_in_url(): void
    {
        $request = Request::create('/test?id=1 UNION SELECT * FROM users', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        $middleware = new SecurityMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Malicious request detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест обнаружения XSS попыток
     */
    public function test_detects_xss_attempts(): void
    {
        $request = Request::create('/test?search=<script>alert("xss")</script>', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.101');

        $middleware = new SecurityMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Malicious request detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест rate limiting
     */
    public function test_rate_limiting_blocks_excessive_requests(): void
    {
        $ip = '192.168.1.102';
        $middleware = new SecurityMiddleware();

        // Симулируем 1000 запросов (лимит)
        Cache::put('rate_limit_' . $ip, 1000, now()->addMinute());

        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', $ip);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Too Many Requests');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест блокировки заблокированных IP
     */
    public function test_blocks_banned_ips(): void
    {
        $ip = '192.168.1.103';
        Cache::put('blocked_ip_' . $ip, true, now()->addDay());

        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', $ip);

        $middleware = new SecurityMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Access denied');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест фильтрации вредоносного ввода
     */
    public function test_filters_malicious_input(): void
    {
        $request = Request::create('/test', 'POST', [
            'name' => 'John',
            'email' => 'test@example.com',
            'comment' => 'Hello <script>alert("hack")</script> world'
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.104');

        $middleware = new SecurityMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Invalid input detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест разрешенных полей контента
     */
    public function test_allows_content_fields_with_html(): void
    {
        $request = Request::create('/test', 'POST', [
            'content' => 'This is <b>bold</b> text',
            'description' => 'Description with <em>emphasis</em>'
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new SecurityMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Content allowed');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест удаления заголовков сервера
     */
    public function test_removes_server_headers(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new SecurityMiddleware();

        $response = $middleware->handle($request, function ($req) {
            $resp = new Response('Test');
            $resp->headers->set('Server', 'Apache');
            $resp->headers->set('X-Powered-By', 'PHP');
            return $resp;
        });

        $this->assertNull($response->headers->get('Server'));
        $this->assertNull($response->headers->get('X-Powered-By'));
    }

    /**
     * Тест логирования подозрительной активности
     */
    public function test_logs_suspicious_activity(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Suspicious activity detected', \Mockery::type('array'));

        $request = Request::create('/test?q=<script>', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.105');

        $middleware = new SecurityMiddleware();

        try {
            $middleware->handle($request, function ($req) {
                return new Response('Test');
            });
        } catch (\Exception $e) {
            // Ожидаем исключение
        }
    }
}
