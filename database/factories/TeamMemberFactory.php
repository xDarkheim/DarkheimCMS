<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\TeamMember>
     */
    protected $model = TeamMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        return [
            'name' => $name,
            'position' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Design', 'Marketing', 'Sales']),
            'bio' => fake()->paragraphs(2, true),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => null,
            'skills' => fake()->randomElements(['PHP', 'Laravel', 'Vue.js', 'React', 'MySQL', 'JavaScript', 'Python'], 4),
            'social_links' => [
                'linkedin' => fake()->url(),
                'github' => fake()->url(),
                'twitter' => fake()->url(),
            ],
            'status' => fake()->randomElement(['active', 'inactive', 'on-leave']),
            'joined_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'priority' => fake()->numberBetween(1, 10),
            'show_on_website' => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that the team member is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'show_on_website' => true,
        ]);
    }
}
