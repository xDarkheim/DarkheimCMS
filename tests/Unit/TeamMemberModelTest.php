<?php

namespace Tests\Unit;

use App\Models\TeamMember;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_team_member(): void
    {
        $member = TeamMember::create([
            'name' => 'John Doe',
            'position' => 'Senior Developer',
            'department' => 'Engineering', // Добавляем обязательное поле
            'bio' => 'Experienced developer with 5+ years',
            'email' => 'john@company.com',
            'skills' => ['PHP', 'Laravel', 'Vue.js'],
            'social_links' => [
                'linkedin' => 'https://linkedin.com/in/johndoe',
                'github' => 'https://github.com/johndoe'
            ],
            'status' => 'active', // Используем status вместо is_active
            'show_on_website' => true, // Используем show_on_website
            'priority' => 1 // Используем priority вместо sort_order
        ]);

        $this->assertInstanceOf(TeamMember::class, $member);
        $this->assertEquals('John Doe', $member->name);
        $this->assertEquals('Senior Developer', $member->position);
        $this->assertEquals('active', $member->status);
        $this->assertIsArray($member->skills);
        $this->assertIsArray($member->social_links);
    }

    #[Test]
    public function it_can_scope_active_members(): void
    {
        TeamMember::factory()->create(['status' => 'active']);
        TeamMember::factory()->create(['status' => 'inactive']);

        $activeMembers = TeamMember::active()->get();

        $this->assertCount(1, $activeMembers);
    }

    #[Test]
    public function it_can_scope_members_by_position(): void
    {
        TeamMember::factory()->create(['position' => 'Developer']);
        TeamMember::factory()->create(['position' => 'Designer']);

        $developers = TeamMember::byPosition('Developer')->get();

        $this->assertCount(1, $developers);
    }
}
