<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use PHPUnit\Framework\Attributes\Test;
use App\Models\PortfolioCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_published_portfolios(): void
    {
        Portfolio::factory()->create(['is_published' => true, 'title' => 'Published Project']);
        Portfolio::factory()->create(['is_published' => false, 'title' => 'Draft Project']);

        $response = $this->getJson('/api/portfolios');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Published Project');
    }

    #[Test]
    public function it_can_show_specific_portfolio(): void
    {
        $portfolio = Portfolio::factory()->create([
            'is_published' => true,
            'title' => 'Test Portfolio',
            'description' => 'Test Description'
        ]);

        $response = $this->getJson("/api/portfolios/{$portfolio->id}");

        $response->assertStatus(200)
                ->assertJsonPath('data.title', 'Test Portfolio')
                ->assertJsonPath('data.description', 'Test Description');
    }

    #[Test]
    public function it_returns_404_for_unpublished_portfolio(): void
    {
        $portfolio = Portfolio::factory()->create(['is_published' => false]);

        $response = $this->getJson("/api/portfolios/{$portfolio->id}");

        $response->assertStatus(404);
    }

    #[Test]
    public function it_can_get_featured_portfolios(): void
    {
        Portfolio::factory()->create(['is_published' => true, 'is_featured' => true]);
        Portfolio::factory()->create(['is_published' => true, 'is_featured' => false]);

        $response = $this->getJson('/api/portfolios/featured');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function it_can_get_portfolio_categories(): void
    {
        $category = PortfolioCategory::factory()->create(['name' => 'Web Development']);
        Portfolio::factory()->create([
            'is_published' => true,
            'portfolio_category_id' => $category->id
        ]);

        $response = $this->getJson('/api/portfolios/categories');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data'
                ]);
    }

    #[Test]
    public function it_can_get_portfolio_stats(): void
    {
        Portfolio::factory()->count(5)->create(['is_published' => true]);
        Portfolio::factory()->count(2)->create(['is_published' => true, 'is_featured' => true]);

        $response = $this->getJson('/api/portfolios/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_projects',
                        'featured_projects'
                    ]
                ]);
    }

    #[Test]
    public function it_can_filter_portfolios_by_category(): void
    {
        $category = PortfolioCategory::factory()->create(['slug' => 'web-development']);
        Portfolio::factory()->create([
            'is_published' => true,
            'portfolio_category_id' => $category->id
        ]);
        Portfolio::factory()->create(['is_published' => true]);

        $response = $this->getJson('/api/portfolios?category=web-development');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
}
