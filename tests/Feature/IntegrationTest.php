<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use App\Models\News;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function complete_portfolio_workflow_works()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Admin creates portfolio
        $portfolioData = [
            'title' => 'E-commerce Platform',
            'description' => 'Full-featured e-commerce solution',
            'technologies' => ['PHP', 'Laravel', 'Vue.js'],
            'is_published' => false,
            'is_featured' => false
        ];

        $createResponse = $this->actingAs($adminUser, 'sanctum')
                               ->postJson('/api/admin/portfolios', $portfolioData);

        $createResponse->assertStatus(201);
        $portfolioId = $createResponse->json('id');

        // Admin publishes portfolio
        $updateResponse = $this->actingAs($adminUser, 'sanctum')
                               ->putJson("/api/admin/portfolios/{$portfolioId}", [
                                   'is_published' => true,
                                   'is_featured' => true
                               ]);

        $updateResponse->assertStatus(200);

        // Public can now see the portfolio
        $publicResponse = $this->getJson('/api/portfolios');
        $publicResponse->assertStatus(200)
                      ->assertJsonCount(1, 'data');

        // Portfolio appears in featured list
        $featuredResponse = $this->getJson('/api/portfolios/featured');
        $featuredResponse->assertStatus(200)
                        ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function complete_news_workflow_works()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Admin creates news
        $newsData = [
            'title' => 'Company Update',
            'content' => 'We are excited to announce our new features',
            'excerpt' => 'Exciting company updates',
            'author' => 'Admin',
            'category' => 'announcements',
            'is_published' => false
        ];

        $createResponse = $this->actingAs($adminUser, 'sanctum')
                               ->postJson('/api/admin/news', $newsData);

        $createResponse->assertStatus(201);
        $newsId = $createResponse->json('id');

        // Admin publishes news
        $publishResponse = $this->actingAs($adminUser, 'sanctum')
                                ->postJson("/api/admin/news/{$newsId}/toggle-published");

        $publishResponse->assertStatus(200);

        // Public can see published news
        $publicResponse = $this->getJson('/api/news');
        $publicResponse->assertStatus(200)
                      ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function contact_to_admin_review_workflow_works()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Public submits contact form
        $contactData = [
            'name' => 'Potential Client',
            'email' => 'client@example.com',
            'message' => 'I need a website for my business',
            'service' => 'Web Development',
            'budget' => '10000-25000'
        ];

        $submitResponse = $this->postJson('/api/contact', $contactData);
        $submitResponse->assertStatus(200);

        // Admin can see unread messages
        $messagesResponse = $this->actingAs($adminUser, 'sanctum')
                                 ->getJson('/api/admin/contact-messages?unread=true');

        $messagesResponse->assertStatus(200)
                        ->assertJsonCount(1, 'data');

        $messageId = $messagesResponse->json('data.0.id');

        // Admin marks message as read
        $readResponse = $this->actingAs($adminUser, 'sanctum')
                             ->patchJson("/api/admin/contact-messages/{$messageId}/mark-read");

        $readResponse->assertStatus(200);

        // Message no longer appears in unread list
        $unreadResponse = $this->actingAs($adminUser, 'sanctum')
                               ->getJson('/api/admin/contact-messages?unread=true');

        $unreadResponse->assertStatus(200)
                      ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function dashboard_reflects_current_system_state()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Create test data
        Portfolio::factory()->count(5)->create();
        News::factory()->count(3)->create();
        ContactMessage::factory()->count(2)->create(['is_read' => false]);
        User::factory()->count(4)->create();

        $response = $this->actingAs($adminUser, 'sanctum')
                         ->getJson('/api/admin/dashboard/stats');

        $response->assertStatus(200)
                ->assertJson([
                    'portfolios' => 5,
                    'news' => 3,
                    'unread_messages' => 2,
                    'total_users' => 5 // 4 + 1 admin
                ]);
    }
}
