<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AntiCSRFMiddleware;
use App\Http\Middleware\APISecurityMiddleware;
use App\Http\Middleware\BruteForceProtectionMiddleware;
use App\Http\Middleware\FileUploadSecurityMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SecurityMiddleware;
use App\Http\Middleware\SQLInjectionProtectionMiddleware;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->append(SecurityMiddleware::class);
        $middleware->api(prepend: [ EnsureFrontendRequestsAreStateful::class]);
        $middleware->api(append: [SQLInjectionProtectionMiddleware::class]);
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'security' => SecurityHeadersMiddleware::class,
            'anti-csrf' => AntiCSRFMiddleware::class,
            'brute-force' => BruteForceProtectionMiddleware::class,
            'sql-protection' => SQLInjectionProtectionMiddleware::class,
            'file-security' => FileUploadSecurityMiddleware::class,
            'api-security' => APISecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            if ($e instanceof QueryException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                \Illuminate\Support\Facades\Log::error($e->getMessage(), ['exception' => $e]);
            }
        });
    })->create();
