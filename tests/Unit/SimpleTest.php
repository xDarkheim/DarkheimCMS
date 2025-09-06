<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SimpleTest extends TestCase
{
    #[Test]
    public function basic_test_works(): void
    {
        $this->assertTrue(true);
        $this->assertEquals(2, 1 + 1);
    }

    #[Test]
    public function can_access_config(): void
    {
        $this->assertEquals('testing', config('app.env'));
    }
}
