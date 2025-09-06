<?php

namespace Tests\Unit;

use App\Models\Career;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CareerModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_career(): void
    {
        $career = Career::create([
            'title' => 'Full Stack Developer',
            'department' => 'Engineering', // Добавляем обязательное поле
            'short_description' => 'We are hiring a talented full stack developer', // Добавляем обязательное поле
            'description' => 'We are looking for a full stack developer',
            'requirements' => ['PHP', 'Laravel', 'Vue.js'],
            'location' => 'Remote',
            'employment_type' => 'full-time',
            'experience_level' => 'senior', // Добавляем поле с валидным значением
            'salary_range' => '$50,000 - $70,000',
            'is_active' => true
        ]);

        $this->assertInstanceOf(Career::class, $career);
        $this->assertEquals('Full Stack Developer', $career->title);
        $this->assertTrue($career->is_active);
        $this->assertIsArray($career->requirements);
    }

    #[Test]
    public function it_can_scope_active_careers(): void
    {
        Career::factory()->create(['is_active' => true]);
        Career::factory()->create(['is_active' => false]);

        $activeCareers = Career::active()->get();

        $this->assertCount(1, $activeCareers);
    }
}
