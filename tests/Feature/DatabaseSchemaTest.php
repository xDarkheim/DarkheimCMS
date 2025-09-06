<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function users_table_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('users'));

        $columns = ['id', 'name', 'email', 'email_verified_at', 'password', 'role', 'remember_token', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column), "Users table missing column: {$column}");
        }
    }

    #[Test]
    public function portfolios_table_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('portfolios'));

        $columns = [
            'id', 'title', 'slug', 'description', 'short_description', 'image_url', 'gallery_images',
            'project_url', 'github_url', 'technologies', 'category', 'portfolio_category_id',
            'client', 'completed_at', 'is_featured', 'is_published', 'sort_order', 'created_at', 'updated_at'
        ];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('portfolios', $column), "Portfolios table missing column: {$column}");
        }
    }

    #[Test]
    public function news_table_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('news'));

        $columns = [
            'id', 'title', 'slug', 'content', 'excerpt', 'image_url', 'author',
            'category', 'is_published', 'is_featured', 'published_at', 'views', 'created_at', 'updated_at'
        ];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('news', $column), "News table missing column: {$column}");
        }
    }

    #[Test]
    public function contact_messages_table_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('contact_messages'));

        $columns = [
            'id', 'name', 'email', 'phone', 'company', 'service', 'budget', 'message',
            'message_type', 'position_interest', 'resume_file', 'portfolio_url',
            'experience_summary', 'availability', 'salary_expectation', 'is_read', 'created_at', 'updated_at'
        ];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('contact_messages', $column), "Contact messages table missing column: {$column}");
        }
    }

    #[Test]
    public function portfolio_categories_table_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('portfolio_categories'));

        $columns = ['id', 'name', 'slug', 'description', 'sort_order', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('portfolio_categories', $column), "Portfolio categories table missing column: {$column}");
        }
    }

    #[Test]
    public function personal_access_tokens_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));

        $columns = ['id', 'tokenable_type', 'tokenable_id', 'name', 'token', 'abilities', 'last_used_at', 'expires_at', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('personal_access_tokens', $column), "Personal access tokens table missing column: {$column}");
        }
    }
}
