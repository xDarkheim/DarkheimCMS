<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use PHPUnit\Framework\Attributes\Test;
use App\Models\News;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_get_public_stats()
    {
        Portfolio::factory()->count(10)->create(['is_published' => true]);
        Portfolio::factory()->count(3)->create(['is_published' => true, 'is_featured' => true]);
        News::factory()->count(5)->create(['is_published' => true]);

        $response = $this->getJson('/api/stats');

        $response->assertStatus(200)
                ->assertJson([
                    'total_portfolios' => 10,
                    'featured_portfolios' => 3,
                    'total_news' => 5
                ]);
    }

    #[Test]
    public function it_can_get_admin_stats()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        Portfolio::factory()->count(8)->create();
        News::factory()->count(6)->create();
        ContactMessage::factory()->count(4)->create(['is_read' => false]);
        User::factory()->count(3)->create();

        $response = $this->actingAs($adminUser, 'sanctum')
                        ->getJson('/api/admin/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'portfolios',
                    'news',
                    'unread_messages',
                    'users'
                ]);
    }

    #[Test]
    public function non_admin_cannot_access_admin_stats()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/admin/stats');

        $response->assertStatus(403);
    }
}
