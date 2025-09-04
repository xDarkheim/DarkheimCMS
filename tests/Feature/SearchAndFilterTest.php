<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use PHPUnit\Framework\Attributes\Test;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndFilterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_search_portfolios_by_title()
    {
        Portfolio::factory()->create(['title' => 'Laravel E-commerce', 'is_published' => true]);
        Portfolio::factory()->create(['title' => 'Vue.js Dashboard', 'is_published' => true]);
        Portfolio::factory()->create(['title' => 'React Native App', 'is_published' => true]);

        $response = $this->getJson('/api/portfolios?search=Laravel');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Laravel E-commerce');
    }

    #[Test]
    public function it_can_filter_portfolios_by_technology()
    {
        Portfolio::factory()->create([
            'title' => 'PHP Project',
            'technologies' => ['PHP', 'Laravel'],
            'is_published' => true
        ]);
        Portfolio::factory()->create([
            'title' => 'JavaScript Project',
            'technologies' => ['JavaScript', 'Vue.js'],
            'is_published' => true
        ]);

        $response = $this->getJson('/api/portfolios?technology=PHP');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function it_can_paginate_portfolios()
    {
        Portfolio::factory()->count(15)->create(['is_published' => true]);

        $response = $this->getJson('/api/portfolios?per_page=10');

        $response->assertStatus(200)
                ->assertJsonCount(10, 'data')
                ->assertJsonStructure([
                    'data',
                    'meta' => ['current_page', 'last_page', 'per_page', 'total']
                ]);
    }

    #[Test]
    public function it_can_sort_portfolios_by_date()
    {
        $old = Portfolio::factory()->create([
            'title' => 'Old Project',
            'is_published' => true,
            'created_at' => now()->subDays(10)
        ]);
        $new = Portfolio::factory()->create([
            'title' => 'New Project',
            'is_published' => true,
            'created_at' => now()->subDays(1)
        ]);

        $response = $this->getJson('/api/portfolios?sort=created_at&direction=desc');

        $response->assertStatus(200)
                ->assertJsonPath('data.0.title', 'New Project')
                ->assertJsonPath('data.1.title', 'Old Project');
    }

    #[Test]
    public function it_can_filter_news_by_date_range()
    {
        News::factory()->create([
            'title' => 'Old News',
            'is_published' => true,
            'published_at' => now()->subMonths(2)
        ]);
        News::factory()->create([
            'title' => 'Recent News',
            'is_published' => true,
            'published_at' => now()->subDays(5)
        ]);

        $response = $this->getJson('/api/news?from=' . now()->subDays(10)->format('Y-m-d'));

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Recent News');
    }
}
