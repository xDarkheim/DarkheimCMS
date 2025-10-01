<?php

namespace Database\Factories;

use App\Models\Career;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Career>
 */
class CareerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Career>
     */
    protected $model = Career::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->jobTitle();
        return [
            'title' => $title,
            'department' => fake()->randomElement(['Engineering', 'Design', 'Marketing', 'Sales', 'HR', 'Operations']),
            'employment_type' => fake()->randomElement(['full-time', 'part-time', 'contract', 'internship']),
            'location' => fake()->randomElement(['Remote', 'New York', 'San Francisco', 'London', 'Berlin']),
            'remote_available' => fake()->boolean(70),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'requirements' => fake()->randomElements(['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Git', 'REST APIs'], 4),
            'benefits' => fake()->randomElements(['Health Insurance', 'Remote Work', 'Flexible Hours', '401k', 'Stock Options'], 3),
            'salary_range' => fake()->randomElement(['$50,000 - $70,000', '$70,000 - $90,000', '$90,000 - $120,000']),
            'experience_level' => fake()->randomElement(['junior', 'mid', 'senior', 'lead']),
            'skills' => fake()->randomElements(['Communication', 'Leadership', 'Problem Solving', 'Teamwork'], 2),
            'is_active' => fake()->boolean(80),
            'priority' => fake()->numberBetween(1, 10),
            'application_deadline' => fake()->optional(0.6)->dateTimeBetween('now', '+3 months'),
        ];
    }

    /**
     * Indicate that the career is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
