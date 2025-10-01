<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_returns_consistent_response_structure(): void
    {
        Portfolio::factory()->count(3)->create(['is_published' => true]);

        $response = $this->getJson('/api/portfolios');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'is_published'
                        ]
                    ]
                ]);
    }

    #[Test]
    public function api_handles_errors_consistently(): void
    {
        $response = $this->getJson('/api/portfolios/999999');

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success',
                    'message'
                ]);
    }

    #[Test]
    public function api_validates_input_consistently(): void
    {
        $response = $this->postJson('/api/contact', []);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors'
                ]);
    }
}
