<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Portfolio;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function portfolio_creation_requires_title()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/portfolios', [
                            'description' => 'Test description'
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function portfolio_title_must_be_unique()
    {
        Portfolio::factory()->create(['title' => 'Existing Title']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/portfolios', [
                            'title' => 'Existing Title',
                            'description' => 'Test description'
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function news_creation_requires_valid_category()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/news', [
                            'title' => 'Test News',
                            'content' => 'Test content',
                            'category' => 'invalid_category',
                            'author' => 'Test Author'
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['category']);
    }

    #[Test]
    public function contact_form_validates_email_format()
    {
        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'invalid-email-format',
            'message' => 'Test message'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function portfolio_technologies_must_be_array()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/portfolios', [
                            'title' => 'Test Portfolio',
                            'description' => 'Test description',
                            'technologies' => 'PHP, Laravel' // String instead of array
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['technologies']);
    }

    #[Test]
    public function user_password_must_be_confirmed()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
                        ->postJson('/api/admin/users', [
                            'name' => 'Test User',
                            'email' => 'test@example.com',
                            'password' => 'password123',
                            'role' => 'user'
                            // Missing password_confirmation
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }
}
