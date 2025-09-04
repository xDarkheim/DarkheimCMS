<?php

namespace Tests\Feature;

use App\Models\Career;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_active_careers()
    {
        Career::factory()->create(['is_active' => true, 'title' => 'Active Position']);
        Career::factory()->create(['is_active' => false, 'title' => 'Inactive Position']);

        $response = $this->getJson('/api/careers');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Active Position');
    }

    #[Test]
    public function it_can_show_specific_career()
    {
        $career = Career::factory()->create([
            'is_active' => true,
            'title' => 'Software Engineer',
            'slug' => 'software-engineer',
            'description' => 'Join our engineering team'
        ]);

        $response = $this->getJson("/api/careers/{$career->slug}");

        $response->assertStatus(200)
                ->assertJson([
                    'title' => 'Software Engineer',
                    'description' => 'Join our engineering team'
                ]);
    }

    #[Test]
    public function it_returns_404_for_inactive_career()
    {
        $career = Career::factory()->create([
            'is_active' => false,
            'slug' => 'inactive-position'
        ]);

        $response = $this->getJson('/api/careers/inactive-position');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_can_filter_careers_by_location()
    {
        Career::factory()->create(['is_active' => true, 'location' => 'Remote']);
        Career::factory()->create(['is_active' => true, 'location' => 'New York']);

        $response = $this->getJson('/api/careers?location=Remote');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function it_can_filter_careers_by_employment_type()
    {
        Career::factory()->create(['is_active' => true, 'employment_type' => 'full-time']);
        Career::factory()->create(['is_active' => true, 'employment_type' => 'part-time']);

        $response = $this->getJson('/api/careers?employment_type=full-time');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
}
