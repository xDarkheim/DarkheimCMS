<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function admin_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_create_user(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/users', $userData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    public function admin_can_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->putJson("/api/admin/users/{$user->id}", [
                            'name' => 'Updated Name',
                            'email' => $user->email,
                            'role' => 'admin'
                        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Name', $user->fresh()->name);
    }
}
