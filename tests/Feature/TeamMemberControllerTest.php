<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_active_team_members()
    {
        TeamMember::factory()->create(['is_active' => true, 'name' => 'Active Member']);
        TeamMember::factory()->create(['is_active' => false, 'name' => 'Inactive Member']);

        $response = $this->getJson('/api/team');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Active Member');
    }

    #[Test]
    public function it_can_show_specific_team_member()
    {
        $member = TeamMember::factory()->create([
            'is_active' => true,
            'name' => 'John Developer',
            'slug' => 'john-developer',
            'position' => 'Senior Developer',
            'bio' => 'Experienced full-stack developer'
        ]);

        $response = $this->getJson("/api/team/{$member->slug}");

        $response->assertStatus(200)
                ->assertJson([
                    'name' => 'John Developer',
                    'position' => 'Senior Developer'
                ]);
    }

    #[Test]
    public function it_returns_404_for_inactive_team_member()
    {
        $member = TeamMember::factory()->create([
            'is_active' => false,
            'slug' => 'inactive-member'
        ]);

        $response = $this->getJson('/api/team/inactive-member');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_orders_team_members_by_sort_order()
    {
        TeamMember::factory()->create(['is_active' => true, 'sort_order' => 2, 'name' => 'Second']);
        TeamMember::factory()->create(['is_active' => true, 'sort_order' => 1, 'name' => 'First']);
        TeamMember::factory()->create(['is_active' => true, 'sort_order' => 3, 'name' => 'Third']);

        $response = $this->getJson('/api/team');

        $response->assertStatus(200)
                ->assertJsonPath('data.0.name', 'First')
                ->assertJsonPath('data.1.name', 'Second')
                ->assertJsonPath('data.2.name', 'Third');
    }
}
