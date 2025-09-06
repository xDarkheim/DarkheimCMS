<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function app_environment_is_set_to_testing(): void
    {
        $this->assertEquals('testing', config('app.env'));
    }

    #[Test]
    public function database_uses_mysql_for_testing(): void
    {
        $this->assertEquals('mysql', config('database.default'));
        $this->assertEquals('darkheim_db_test', config('database.connections.mysql.database'));
    }

    #[Test]
    public function cache_uses_array_driver_for_testing(): void
    {
        $this->assertEquals('array', config('cache.default'));
    }

    #[Test]
    public function queue_uses_sync_for_testing(): void
    {
        $this->assertEquals('sync', config('queue.default'));
    }

    #[Test]
    public function session_uses_array_driver_for_testing(): void
    {
        $this->assertEquals('array', config('session.driver'));
    }

    #[Test]
    public function mail_uses_array_driver_for_testing(): void
    {
        $this->assertEquals('array', config('mail.default'));
    }

    #[Test]
    public function sanctum_configuration_is_correct(): void
    {
        // Проверяем, что Sanctum настроен правильно
        $this->assertNotNull(config('sanctum'));
        $this->assertIsArray(config('sanctum.guard'));
    }

    #[Test]
    public function app_has_required_configuration_keys(): void
    {
        $requiredKeys = [
            'app.name',
            'app.env',
            'app.key',
            'app.debug',
            'app.url',
            'database.default',
            'cache.default',
            'session.driver'
        ];

        foreach ($requiredKeys as $key) {
            $this->assertNotNull(config($key), "Configuration key {$key} is missing");
        }
    }
}
