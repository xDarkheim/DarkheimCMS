<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_user_can_access_admin_routes()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($adminUser, 'sanctum')
                        ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
    }

    #[Test]
    public function regular_user_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_admin_routes()
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(401);
    }

    #[Test]
    public function admin_routes_require_sanctum_authentication()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Without token
        $response = $this->getJson('/api/admin/dashboard');
        $response->assertStatus(401);

        // With token
        $token = $adminUser->createToken('test-token')->plainTextToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
    }
}
