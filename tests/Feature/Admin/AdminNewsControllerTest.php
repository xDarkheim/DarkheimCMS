<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNewsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected bool $seed = false; // Disable automatic seeding

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function admin_can_list_all_news(): void
    {
        News::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->getJson('/api/admin/news');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data'
                ]);
    }

    #[Test]
    public function admin_can_create_news(): void
    {
        $data = [
            'title' => 'Breaking News',
            'content' => 'This is breaking news content',
            'excerpt' => 'Brief excerpt',
            'author' => 'John Doe',
            'category' => 'announcements',
            'is_published' => true,
            'is_featured' => false
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/news', $data);

        $response->assertStatus(201)
                ->assertJsonPath('data.title', 'Breaking News');

        $this->assertDatabaseHas('news', [
            'title' => 'Breaking News',
            'category' => 'announcements'
        ]);
    }

    #[Test]
    public function admin_can_update_news(): void
    {
        $news = News::factory()->create([
            'title' => 'Original News',
            'category' => 'announcements',
            'author' => 'Test Author'
        ]);

        $updateData = [
            'title' => 'Updated News Title',
            'content' => 'Updated content',
            'category' => 'announcements',
            'author' => 'Test Author'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->putJson("/api/admin/news/{$news->id}", $updateData);

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_delete_news(): void
    {
        $news = News::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->deleteJson("/api/admin/news/{$news->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    #[Test]
    public function admin_can_toggle_news_published_status(): void
    {
        $news = News::factory()->create(['is_published' => false]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson("/api/admin/news/{$news->id}/toggle-published");

        $response->assertStatus(200);

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'is_published' => true
        ]);
    }

    #[Test]
    public function admin_can_toggle_news_featured_status(): void
    {
        $news = News::factory()->create(['is_featured' => false]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson("/api/admin/news/{$news->id}/toggle-featured");

        $response->assertStatus(200);

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'is_featured' => true
        ]);
    }

    #[Test]
    public function admin_can_perform_bulk_actions(): void
    {
        $news1 = News::factory()->create(['is_published' => false]);
        $news2 = News::factory()->create(['is_published' => false]);

        $data = [
            'action' => 'publish',
            'ids' => [$news1->id, $news2->id]
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/news/bulk-action', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('news', ['id' => $news1->id, 'is_published' => true]);
        $this->assertDatabaseHas('news', ['id' => $news2->id, 'is_published' => true]);
    }
}
