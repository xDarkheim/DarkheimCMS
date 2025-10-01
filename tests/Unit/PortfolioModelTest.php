<?php

namespace Tests\Unit;

use App\Models\Portfolio;
use PHPUnit\Framework\Attributes\Test;
use App\Models\PortfolioCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_portfolio(): void
    {
        $portfolio = Portfolio::create([
            'title' => 'Test Portfolio',
            'description' => 'Test Description',
            'is_published' => true,
            'is_featured' => false,
            'technologies' => ['PHP', 'Laravel'],
            'gallery_images' => ['image1.jpg', 'image2.jpg'],
            'category' => 'web',
            'completed_at' => '2023-01-01' // Добавляем обязательное поле
        ]);

        $this->assertInstanceOf(Portfolio::class, $portfolio);
        $this->assertEquals('Test Portfolio', $portfolio->title);
        $this->assertTrue($portfolio->is_published);
        $this->assertFalse($portfolio->is_featured);
        $this->assertEquals(['PHP', 'Laravel'], $portfolio->technologies);
    }

    #[Test]
    public function it_automatically_generates_slug_on_create(): void
    {
        $portfolio = Portfolio::create([
            'title' => 'My Awesome Project',
            'description' => 'Test Description',
            'technologies' => ['PHP'],
            'category' => 'web',
            'completed_at' => '2023-01-01' // Добавляем обязательное поле
        ]);

        $this->assertEquals('my-awesome-project', $portfolio->slug);
    }

    #[Test]
    public function it_updates_slug_when_title_changes(): void
    {
        $portfolio = Portfolio::create([
            'title' => 'Original Title',
            'description' => 'Test Description',
            'technologies' => ['PHP'],
            'category' => 'web',
            'completed_at' => '2023-01-01' // Добавляем обязательное поле
        ]);

        $portfolio->update(['title' => 'Updated Title']);

        $this->assertEquals('updated-title', $portfolio->slug);
    }

    #[Test]
    public function it_belongs_to_portfolio_category(): void
    {
        $category = PortfolioCategory::factory()->create();
        $portfolio = Portfolio::factory()->create([
            'portfolio_category_id' => $category->id
        ]);

        $this->assertInstanceOf(PortfolioCategory::class, $portfolio->portfolioCategory);
        $this->assertEquals($category->id, $portfolio->portfolioCategory->id);
    }

    #[Test]
    public function it_can_get_category_name_from_relationship(): void
    {
        $category = PortfolioCategory::factory()->create(['name' => 'Web Development']);
        $portfolio = Portfolio::factory()->create([
            'portfolio_category_id' => $category->id
        ]);

        $this->assertEquals('Web Development', $portfolio->category_name);
    }

    #[Test]
    public function it_falls_back_to_category_field_for_category_name(): void
    {
        $portfolio = Portfolio::factory()->create([
            'category' => 'Mobile Apps',
            'portfolio_category_id' => null
        ]);

        $this->assertEquals('Mobile Apps', $portfolio->category_name);
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $portfolio = Portfolio::factory()->create([
            'technologies' => ['PHP', 'Laravel'],
            'gallery_images' => ['image1.jpg'],
            'completed_at' => '2023-01-01',
            'is_featured' => true,
            'is_published' => false
        ]);

        $this->assertIsArray($portfolio->technologies);
        $this->assertIsArray($portfolio->gallery_images);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $portfolio->completed_at);
        $this->assertTrue($portfolio->is_featured);
        $this->assertFalse($portfolio->is_published);
    }
}
