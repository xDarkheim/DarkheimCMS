<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Middleware\SQLInjectionProtectionMiddleware;
use Symfony\Component\HttpFoundation\Response;

class SQLInjectionProtectionMiddlewareTest extends TestCase
{
    /**
     * Тест пропуска обычных безопасных запросов
     */
    public function test_allows_normal_requests(): void
    {
        $request = Request::create('/api/users', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new SQLInjectionProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('User list');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('User list', $response->getContent());
    }

    /**
     * Тест обнаружения критичных SQL инъекций
     */
    public function test_detects_critical_sql_injection(): void
    {
        $request = Request::create('/api/users', 'POST', [
            'search' => "'; DROP TABLE users; --"
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        $middleware = new SQLInjectionProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Critical security violation detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест обнаружения UNION SELECT атак
     */
    public function test_detects_union_select_attacks(): void
    {
        $request = Request::create('/api/products', 'POST', [
            'category' => "1 UNION SELECT username, password FROM users"
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.101');

        $middleware = new SQLInjectionProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Critical security violation detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест обнаружения критичных XSS атак
     */
    public function test_detects_critical_xss_attacks(): void
    {
        $request = Request::create('/api/comments', 'POST', [
            'title' => '<script>document.location="http://evil.com"</script>'
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.102');

        $middleware = new SQLInjectionProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Malicious content detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест разрешения контентных полей с HTML
     */
    public function test_allows_content_fields_with_safe_html(): void
    {
        $request = Request::create('/api/articles', 'POST', [
            'content' => '<p>This is a <strong>safe</strong> article content</p>',
            'description' => '<em>Article description</em> with formatting'
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new SQLInjectionProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Article created');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест пропуска внутренних Laravel запросов
     */
    public function test_skips_internal_laravel_requests(): void
    {
        $request = Request::create('/_debugbar/assets/stylesheets', 'GET');
        $middleware = new SQLInjectionProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Debug asset');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест пропуска AJAX запросов с CSRF токеном
     */
    public function test_skips_ajax_requests_with_csrf_token(): void
    {
        $request = Request::create('/api/data', 'POST');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('X-CSRF-TOKEN', 'valid-token');

        $middleware = new SQLInjectionProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('AJAX data');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест обнаружения directory traversal атак
     */
    public function test_detects_directory_traversal(): void
    {
        $request = Request::create('/api/files?path=../../etc/passwd', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.103');

        $middleware = new SQLInjectionProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Invalid request detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест обнаружения null byte инъекций
     */
    public function test_detects_null_byte_injection(): void
    {
        $request = Request::create('/api/download?file=document.pdf%00.php', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.104');

        $middleware = new SQLInjectionProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Invalid request detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест фильтрации коротких строк (менее 10 символов)
     */
    public function test_ignores_short_strings(): void
    {
        $request = Request::create('/api/search', 'POST', [
            'q' => 'test', // короткая строка
            'filter' => 'new'
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $middleware = new SQLInjectionProtectionMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Search results');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест валидации массивов входных данных
     */
    public function test_validates_array_input(): void
    {
        $request = Request::create('/api/bulk-update', 'POST', [
            'items' => [
                ['id' => 1, 'name' => 'Safe name'],
                ['id' => 2, 'name' => "'; DROP TABLE products; --"]
            ]
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.105');

        $middleware = new SQLInjectionProtectionMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Critical security violation detected');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }
}
