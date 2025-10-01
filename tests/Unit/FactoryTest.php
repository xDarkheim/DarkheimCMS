<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use App\Models\News;
use App\Models\ContactMessage;
use Database\Factories\UserFactory;
use Database\Factories\NewsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_factory_creates_valid_users(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertTrue(filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false);
        $this->assertNotEmpty($user->password);
    }

    #[Test]
    public function user_factory_can_create_admin_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->isAdmin());
    }

    #[Test]
    public function portfolio_factory_creates_valid_portfolios(): void
    {
        $portfolio = Portfolio::factory()->create();

        $this->assertInstanceOf(Portfolio::class, $portfolio);
        $this->assertNotEmpty($portfolio->title);
        $this->assertNotEmpty($portfolio->description);
        $this->assertIsArray($portfolio->technologies);
        $this->assertIsBool($portfolio->is_published);
    }

    #[Test]
    public function portfolio_factory_can_create_featured_portfolios(): void
    {
        $featured = Portfolio::factory()->featured()->create();

        $this->assertTrue($featured->is_featured);
        $this->assertTrue($featured->is_published);
    }

    #[Test]
    public function news_factory_creates_valid_news(): void
    {
        $news = News::factory()->create();

        $this->assertInstanceOf(News::class, $news);
        $this->assertNotEmpty($news->title);
        $this->assertNotEmpty($news->content);
        $this->assertContains($news->category, array_keys(News::CATEGORIES));
        $this->assertIsBool($news->is_published);
    }

    #[Test]
    public function news_factory_can_create_published_news(): void
    {
        $published = News::factory()->published()->create();

        $this->assertTrue($published->is_published);
        $this->assertNotNull($published->published_at);
    }

    #[Test]
    public function contact_message_factory_creates_valid_messages(): void
    {
        $message = ContactMessage::factory()->create();

        $this->assertInstanceOf(ContactMessage::class, $message);
        $this->assertNotEmpty($message->name);
        $this->assertTrue(filter_var($message->email, FILTER_VALIDATE_EMAIL) !== false);
        $this->assertNotEmpty($message->message);
        $this->assertIsBool($message->is_read);
    }

    #[Test]
    public function factories_can_create_sequences(): void
    {
        $users = User::factory()->count(3)->create();

        $this->assertCount(3, $users);
        $users->each(function ($user) {
            $this->assertInstanceOf(User::class, $user);
        });
    }

    #[Test]
    public function factories_support_state_customization(): void
    {
        $portfolio = Portfolio::factory()
            ->published()
            ->featured()
            ->create(['title' => 'Custom Title']);

        $this->assertEquals('Custom Title', $portfolio->title);
        $this->assertTrue($portfolio->is_published);
        $this->assertTrue($portfolio->is_featured);
    }
}
