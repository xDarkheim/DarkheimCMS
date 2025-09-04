<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortfolioControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function admin_can_list_all_portfolios()
    {
        Portfolio::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->getJson('/api/admin/portfolios');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function admin_can_create_portfolio()
    {
        $data = [
            'title' => 'New Portfolio Project',
            'description' => 'A comprehensive project description',
            'short_description' => 'Brief overview',
            'technologies' => ['PHP', 'Laravel', 'Vue.js'],
            'project_url' => 'https://example.com',
            'github_url' => 'https://github.com/example/project',
            'client' => 'Test Client',
            'completed_at' => '2023-12-01',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 1
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/portfolios', $data);

        $response->assertStatus(201)
                ->assertJson(['title' => 'New Portfolio Project']);

        $this->assertDatabaseHas('portfolios', [
            'title' => 'New Portfolio Project',
            'slug' => 'new-portfolio-project'
        ]);
    }

    #[Test]
    public function admin_can_update_portfolio()
    {
        $portfolio = Portfolio::factory()->create(['title' => 'Original Title']);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->putJson("/api/admin/portfolios/{$portfolio->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson(['title' => 'Updated Title']);

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id,
            'title' => 'Updated Title'
        ]);
    }

    #[Test]
    public function admin_can_delete_portfolio()
    {
        $portfolio = Portfolio::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->deleteJson("/api/admin/portfolios/{$portfolio->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('portfolios', ['id' => $portfolio->id]);
    }

    #[Test]
    public function non_admin_cannot_access_admin_endpoints()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/admin/portfolios');

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_admin_endpoints()
    {
        $response = $this->getJson('/api/admin/portfolios');

        $response->assertStatus(401);
    }

    #[Test]
    public function admin_can_view_single_portfolio()
    {
        $portfolio = Portfolio::factory()->create(['title' => 'Test Portfolio']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->getJson("/api/admin/portfolios/{$portfolio->id}");

        $response->assertStatus(200)
                ->assertJson(['title' => 'Test Portfolio']);
    }
}
