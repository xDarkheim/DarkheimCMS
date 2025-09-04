<?php

namespace Tests\Feature;

use App\Models\News;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_published_news()
    {
        News::factory()->create(['is_published' => true, 'title' => 'Published News']);
        News::factory()->create(['is_published' => false, 'title' => 'Draft News']);

        $response = $this->getJson('/api/news');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Published News');
    }

    #[Test]
    public function it_can_show_specific_news_by_slug()
    {
        $news = News::factory()->create([
            'is_published' => true,
            'title' => 'Test News',
            'slug' => 'test-news',
            'content' => 'Test Content'
        ]);

        $response = $this->getJson('/api/news/test-news');

        $response->assertStatus(200)
                ->assertJson([
                    'title' => 'Test News',
                    'content' => 'Test Content'
                ]);
    }

    #[Test]
    public function it_returns_404_for_unpublished_news()
    {
        News::factory()->create([
            'is_published' => false,
            'slug' => 'unpublished-news'
        ]);

        $response = $this->getJson('/api/news/unpublished-news');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_can_get_featured_news()
    {
        News::factory()->create(['is_published' => true, 'is_featured' => true]);
        News::factory()->create(['is_published' => true, 'is_featured' => false]);

        $response = $this->getJson('/api/news/featured');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function it_can_get_latest_news()
    {
        News::factory()->count(3)->create(['is_published' => true]);

        $response = $this->getJson('/api/news/latest');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_can_get_news_categories()
    {
        News::factory()->create(['is_published' => true, 'category' => 'announcements']);
        News::factory()->create(['is_published' => true, 'category' => 'updates']);

        $response = $this->getJson('/api/news/categories');

        $response->assertStatus(200)
                ->assertJsonFragment(['announcements', 'updates']);
    }

    #[Test]
    public function it_can_get_all_predefined_categories()
    {
        $response = $this->getJson('/api/news/all-categories');

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'announcements' => 'Announcements',
                    'updates' => 'Updates'
                ]);
    }

    #[Test]
    public function it_can_filter_news_by_category()
    {
        News::factory()->create(['is_published' => true, 'category' => 'announcements']);
        News::factory()->create(['is_published' => true, 'category' => 'updates']);

        $response = $this->getJson('/api/news?category=announcements');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
}
