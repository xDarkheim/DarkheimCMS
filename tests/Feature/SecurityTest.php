<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_prevents_sql_injection_in_search()
    {
        Portfolio::factory()->create(['title' => 'Safe Project', 'is_published' => true]);

        $maliciousSearch = "'; DROP TABLE portfolios; --";

        $response = $this->getJson('/api/portfolios?search=' . urlencode($maliciousSearch));

        $response->assertStatus(200);
        // Database should still exist and be queryable
        $this->assertDatabaseHas('portfolios', ['title' => 'Safe Project']);
    }

    #[Test]
    public function it_sanitizes_user_input()
    {
        $maliciousData = [
            'name' => '<script>alert("xss")</script>John Doe',
            'email' => 'test@example.com',
            'message' => '<img src=x onerror=alert("xss")>Hello'
        ];

        $response = $this->postJson('/api/contact', $maliciousData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    public function it_rate_limits_contact_form_submissions()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message'
        ];

        // Submit multiple requests rapidly
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/contact', array_merge($data, [
                'email' => "john{$i}@example.com"
            ]));
        }

        // The last request should be rate limited
        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john999@example.com',
            'message' => 'Test message'
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    #[Test]
    public function it_validates_token_expiration()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['*'], now()->subHour())->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(401);
    }

    #[Test]
    public function it_prevents_mass_assignment_vulnerabilities()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($adminUser, 'sanctum')
                        ->postJson('/api/admin/portfolios', [
                            'title' => 'Test Portfolio',
                            'description' => 'Test Description',
                            'id' => 999999, // Trying to set ID
                            'created_at' => '2020-01-01', // Trying to set timestamp
                        ]);

        $response->assertStatus(201);

        // Verify mass assignment was prevented
        $portfolio = Portfolio::where('title', 'Test Portfolio')->first();
        $this->assertNotEquals(999999, $portfolio->id);
        $this->assertNotEquals('2020-01-01', $portfolio->created_at->format('Y-m-d'));
    }
}
