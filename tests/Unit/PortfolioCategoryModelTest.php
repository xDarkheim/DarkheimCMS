<?php

namespace Tests\Unit;

use App\Models\PortfolioCategory;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioCategoryModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_portfolio_category(): void
    {
        $category = PortfolioCategory::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Web development projects'
        ]);

        $this->assertInstanceOf(PortfolioCategory::class, $category);
        $this->assertEquals('Web Development', $category->name);
        $this->assertEquals('web-development', $category->slug);
    }

    #[Test]
    public function it_has_many_portfolios(): void
    {
        $category = PortfolioCategory::factory()->create();
        Portfolio::factory()->count(3)->create(['portfolio_category_id' => $category->id]);

        $this->assertCount(3, $category->portfolios);
        $this->assertInstanceOf(Portfolio::class, $category->portfolios->first());
    }

    #[Test]
    public function it_automatically_generates_slug_from_name(): void
    {
        $category = PortfolioCategory::create([
            'name' => 'Mobile Applications'
        ]);

        $this->assertEquals('mobile-applications', $category->slug);
    }
}
