<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Session\ArraySessionHandler;
use App\Http\Middleware\AntiCSRFMiddleware;
use Symfony\Component\HttpFoundation\Response;

class AntiCSRFMiddlewareTest extends TestCase
{
    /**
     * Тест пропуска GET запросов
     */
    public function test_allows_get_requests(): void
    {
        $request = Request::create('/admin/dashboard', 'GET');
        $this->addSessionToRequest($request);

        $middleware = new AntiCSRFMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Dashboard content');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест пропуска статических ресурсов
     */
    public function test_allows_static_resources(): void
    {
        $staticFiles = [
            '/assets/style.css',
            '/js/app.js',
            '/images/logo.png',
            '/fonts/font.woff2'
        ];

        $middleware = new AntiCSRFMiddleware();

        foreach ($staticFiles as $file) {
            $request = Request::create($file, 'GET');

            $response = $middleware->handle($request, function ($req) {
                return new Response('Static content');
            });

            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    /**
     * Тест пропуска API запросов с Bearer токеном
     */
    public function test_allows_api_requests_with_bearer_token(): void
    {
        $request = Request::create('/api/users', 'POST');
        $request->headers->set('Authorization', 'Bearer valid-token');

        $middleware = new AntiCSRFMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('API response');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест блокировки POST запросов без CSRF токена
     */
    public function test_blocks_post_requests_without_csrf_token(): void
    {
        $request = Request::create('/admin/save', 'POST');
        $this->addSessionToRequest($request);

        $middleware = new AntiCSRFMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('CSRF token mismatch');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест валидации правильного CSRF токена
     */
    public function test_validates_correct_csrf_token(): void
    {
        $request = Request::create('/admin/save', 'POST');
        $session = $this->addSessionToRequest($request);

        $token = $session->token();
        $request->request->set('_token', $token);

        $middleware = new AntiCSRFMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Data saved');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест валидации CSRF токена в заголовке
     */
    public function test_validates_csrf_token_in_header(): void
    {
        $request = Request::create('/admin/api', 'POST');
        $session = $this->addSessionToRequest($request);

        $token = $session->token();
        $request->headers->set('X-CSRF-TOKEN', $token);

        $middleware = new AntiCSRFMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('API call successful');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Тест блокировки неправильного CSRF токена
     */
    public function test_blocks_invalid_csrf_token(): void
    {
        $request = Request::create('/admin/save', 'POST');
        $this->addSessionToRequest($request);

        $request->request->set('_token', 'invalid-token');

        $middleware = new AntiCSRFMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('CSRF token mismatch');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест валидации Origin заголовка
     */
    public function test_validates_origin_header(): void
    {
        $request = Request::create('/admin/save', 'POST');
        $request->headers->set('Origin', 'https://evil.com');
        $session = $this->addSessionToRequest($request);

        // Добавляем валидный CSRF токен, чтобы дойти до проверки Origin
        $request->request->set('_token', $session->token());

        $middleware = new AntiCSRFMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Invalid origin');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест разрешенных Origins
     */
    public function test_allows_valid_origins(): void
    {
        $validOrigins = [
            'https://darkheim.net',
            'https://www.darkheim.net',
            'http://localhost',
            'http://127.0.0.1'
        ];

        $middleware = new AntiCSRFMiddleware();

        foreach ($validOrigins as $origin) {
            $request = Request::create('/admin/save', 'POST');
            $request->headers->set('Origin', $origin);
            $session = $this->addSessionToRequest($request);
            $request->request->set('_token', $session->token());

            $response = $middleware->handle($request, function ($req) {
                return new Response('Valid origin');
            });

            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    /**
     * Тест валидации Referer заголовка
     */
    public function test_validates_referer_header(): void
    {
        $request = Request::create('/admin/save', 'POST');
        $request->headers->set('Referer', 'https://evil.com/attack');
        $session = $this->addSessionToRequest($request);
        $request->request->set('_token', $session->token());

        $middleware = new AntiCSRFMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Invalid referer');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест обработки запросов без сессии
     */
    public function test_handles_requests_without_session(): void
    {
        $request = Request::create('/admin/save', 'POST');
        // Не добавляем сессию

        $middleware = new AntiCSRFMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Session not available');

        $middleware->handle($request, function ($req) {
            return new Response('Should not reach here');
        });
    }

    /**
     * Тест установки заголовков защиты CSRF
     */
    public function test_sets_csrf_protection_headers(): void
    {
        $request = Request::create('/admin/dashboard', 'GET');
        $this->addSessionToRequest($request);

        $middleware = new AntiCSRFMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return new Response('Dashboard');
        });

        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
    }

    /**
     * Тест поддержки всех HTTP методов требующих CSRF
     */
    public function test_validates_all_state_changing_methods(): void
    {
        $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $middleware = new AntiCSRFMiddleware();

        foreach ($methods as $method) {
            $request = Request::create('/admin/action', $method);
            $this->addSessionToRequest($request);

            $exceptionThrown = false;
            $exceptionMessage = '';

            try {
                $middleware->handle($request, function ($req) {
                    return new Response('Should not reach here');
                });
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $exceptionThrown = true;
                $exceptionMessage = $e->getMessage();
            }

            $this->assertTrue($exceptionThrown, "Expected HttpException for method {$method}");
            $this->assertStringContainsString('CSRF token mismatch', $exceptionMessage, "Expected CSRF token mismatch message for method {$method}");
        }
    }

    /**
     * Вспомогательный метод для добавления сессии к запросу
     */
    private function addSessionToRequest(Request $request): Store
    {
        $session = new Store('test-session', new ArraySessionHandler(60));
        $session->start();
        $request->setLaravelSession($session);

        return $session;
    }
}
