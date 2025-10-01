<?php

namespace Tests\Unit;

use App\Models\News;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_news(): void
    {
        $news = News::create([
            'title' => 'Test News',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'author' => 'John Doe',
            'category' => 'announcements',
            'is_published' => true,
            'is_featured' => false
        ]);

        $this->assertInstanceOf(News::class, $news);
        $this->assertEquals('Test News', $news->title);
        $this->assertEquals('announcements', $news->category);
        $this->assertTrue($news->is_published);
        $this->assertFalse($news->is_featured);
    }

    #[Test]
    public function it_has_predefined_categories(): void
    {
        $expectedCategories = [
            'announcements' => 'Announcements',
            'updates' => 'Updates',
            'releases' => 'Releases',
            'development' => 'Development',
            'community' => 'Community',
            'events' => 'Events',
            'tutorials' => 'Tutorials',
            'behind-scenes' => 'Behind the Scenes',
            'partnerships' => 'Partnerships',
            'general' => 'General'
        ];

        $this->assertEquals($expectedCategories, News::CATEGORIES);
    }

    #[Test]
    public function it_casts_boolean_attributes(): void
    {
        $news = News::factory()->create([
            'is_published' => 1,
            'is_featured' => 0
        ]);

        $this->assertTrue($news->is_published);
        $this->assertFalse($news->is_featured);
    }

    #[Test]
    public function it_casts_published_at_to_datetime(): void
    {
        $news = News::factory()->create([
            'published_at' => '2023-01-01 12:00:00'
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $news->published_at);
    }
}
