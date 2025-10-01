<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddlewareTest extends TestCase
{
    /**
     * Тест установки базовых заголовков безопасности
     */
    public function test_sets_basic_security_headers(): void
    {
        $request = Request::create('/test');
        $middleware = new SecurityHeadersMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Test content');
        });

        // Проверяем основные заголовки безопасности
        $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString('max-age=31536000', $response->headers->get('Strict-Transport-Security'));
        $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
    }

    /**
     * Тест Content Security Policy для обычных страниц
     */
    public function test_sets_strict_csp_for_regular_pages(): void
    {
        $request = Request::create('/');
        $middleware = new SecurityHeadersMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Test content');
        });

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
    }

    /**
     * Тест более мягкой CSP для админки
     */
    public function test_sets_relaxed_csp_for_admin_pages(): void
    {
        $request = Request::create('/admin');
        $middleware = new SecurityHeadersMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Admin content');
        });

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline' https: http:", $csp);
        $this->assertStringContainsString("font-src 'self' https: http: data:", $csp);
        $this->assertStringNotContainsString("upgrade-insecure-requests", $csp);
    }

    /**
     * Тест удаления информации о сервере
     */
    public function test_removes_server_information(): void
    {
        $request = Request::create('/test');
        $middleware = new SecurityHeadersMiddleware();

        $response = new Response('Test content');
        $response->headers->set('Server', 'Apache/2.4.41');
        $response->headers->set('X-Powered-By', 'PHP/8.2');

        $finalResponse = $middleware->handle($request, function ($req) use ($response) {
            return $response;
        });

        $this->assertNull($finalResponse->headers->get('Server'));
        $this->assertNull($finalResponse->headers->get('X-Powered-By'));
    }

    /**
     * Тест установки nonce для CSP
     */
    public function test_shares_nonce_with_views(): void
    {
        $request = Request::create('/test');
        $middleware = new SecurityHeadersMiddleware();

        $middleware->handle($request, function ($req) {
            return new Response('Test content');
        });

        // Проверяем, что nonce доступен в view
        $this->assertNotNull(view()->shared('csp_nonce'));
        $this->assertIsString(view()->shared('csp_nonce'));
    }
}
