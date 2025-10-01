<?php

namespace Database\Factories;

use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Portfolio>
 */
class PortfolioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Portfolio>
     */
    protected $model = Portfolio::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(),
            'image_url' => fake()->imageUrl(),
            'gallery_images' => fake()->randomElements(['image1.jpg', 'image2.jpg', 'image3.jpg'], 2),
            'project_url' => fake()->url(),
            'github_url' => fake()->url(),
            'technologies' => fake()->randomElements(['PHP', 'Laravel', 'Vue.js', 'React', 'MySQL', 'JavaScript'], 3),
            'category' => fake()->randomElement(['web', 'mobile', 'desktop']),
            'portfolio_category_id' => null, // Will be set in tests
            'client' => fake()->company(),
            'completed_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_featured' => fake()->boolean(20),
            'is_published' => fake()->boolean(70),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the portfolio is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the portfolio is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_published' => true,
        ]);
    }
}
