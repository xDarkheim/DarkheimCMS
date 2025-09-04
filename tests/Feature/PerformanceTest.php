<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use PHPUnit\Framework\Attributes\Test;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_optimizes_database_queries_for_portfolio_list()
    {
        Portfolio::factory()->count(50)->create(['is_published' => true]);

        DB::enableQueryLog();

        $response = $this->getJson('/api/portfolios');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);
        // Should not have N+1 query problems
        $this->assertLessThan(5, count($queries));
    }

    #[Test]
    public function it_caches_frequently_accessed_data()
    {
        Cache::flush();

        Portfolio::factory()->count(10)->create(['is_published' => true]);

        // First request - data should be cached
        $response1 = $this->getJson('/api/portfolios/stats');
        $response1->assertStatus(200);

        // Verify cache was set
        $this->assertTrue(Cache::has('portfolio.stats'));

        // Second request - should use cached data
        DB::enableQueryLog();
        $response2 = $this->getJson('/api/portfolios/stats');
        $queries = DB::getQueryLog();

        $response2->assertStatus(200);
        $this->assertEmpty($queries); // No database queries if cached
    }

    #[Test]
    public function it_handles_large_dataset_pagination_efficiently()
    {
        Portfolio::factory()->count(1000)->create(['is_published' => true]);

        $startTime = microtime(true);

        $response = $this->getJson('/api/portfolios?page=50&per_page=20');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200);
        // Should complete within reasonable time (2 seconds)
        $this->assertLessThan(2, $executionTime);
    }

    #[Test]
    public function it_optimizes_eager_loading_for_relationships()
    {
        $category = \App\Models\PortfolioCategory::factory()->create();
        Portfolio::factory()->count(20)->create([
            'is_published' => true,
            'portfolio_category_id' => $category->id
        ]);

        DB::enableQueryLog();

        $response = $this->getJson('/api/portfolios');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);
        // Should use eager loading to avoid N+1 queries
        $this->assertLessThan(3, count($queries));
    }

    #[Test]
    public function it_handles_concurrent_admin_requests()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $token = $adminUser->createToken('test-token')->plainTextToken;

        // Simulate multiple concurrent requests
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/admin/dashboard/stats');
        }

        foreach ($responses as $response) {
            $response->assertStatus(200);
        }
    }
}
