<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use App\Models\News;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function admin_can_bulk_publish_portfolios()
    {
        $portfolios = Portfolio::factory()->count(3)->create(['is_published' => false]);
        $portfolioIds = $portfolios->pluck('id')->toArray();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/portfolios/bulk-action', [
                            'action' => 'publish',
                            'items' => $portfolioIds
                        ]);

        $response->assertStatus(200);

        foreach ($portfolioIds as $id) {
            $this->assertDatabaseHas('portfolios', [
                'id' => $id,
                'is_published' => true
            ]);
        }
    }

    #[Test]
    public function admin_can_bulk_delete_news()
    {
        $news = News::factory()->count(3)->create();
        $newsIds = $news->pluck('id')->toArray();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/news/bulk-action', [
                            'action' => 'delete',
                            'items' => $newsIds
                        ]);

        $response->assertStatus(200);

        foreach ($newsIds as $id) {
            $this->assertDatabaseMissing('news', ['id' => $id]);
        }
    }

    #[Test]
    public function admin_can_bulk_mark_messages_as_read()
    {
        $messages = ContactMessage::factory()->count(3)->create(['is_read' => false]);
        $messageIds = $messages->pluck('id')->toArray();

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/contact-messages/bulk-action', [
                            'action' => 'mark_read',
                            'items' => $messageIds
                        ]);

        $response->assertStatus(200);

        foreach ($messageIds as $id) {
            $this->assertDatabaseHas('contact_messages', [
                'id' => $id,
                'is_read' => true
            ]);
        }
    }
}
