<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'service' => fake()->randomElement(['Web Development', 'Mobile App', 'E-commerce', 'Consulting']),
            'budget' => fake()->randomElement(['5000-10000', '10000-25000', '25000-50000', '50000+']),
            'message' => fake()->paragraphs(2, true),
            'message_type' => fake()->randomElement(['general', 'job_application']),
            'position_interest' => fake()->jobTitle(),
            'resume_file' => null,
            'portfolio_url' => fake()->url(),
            'experience_summary' => fake()->sentence(),
            'availability' => fake()->randomElement(['Immediately', '2 weeks', '1 month', '2 months']),
            'salary_expectation' => fake()->randomFloat(2, 40000, 120000),
            'is_read' => fake()->boolean(30),
        ];
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Indicate that the message is a job application.
     */
    public function jobApplication(): static
    {
        return $this->state(fn (array $attributes) => [
            'message_type' => 'job_application',
        ]);
    }
}
