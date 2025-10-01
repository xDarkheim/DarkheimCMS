<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description');
            $table->string('image_url')->nullable();
            $table->json('gallery_images')->nullable(); // Дополнительные изображения
            $table->string('project_url')->nullable(); // Ссылка на проект
            $table->string('github_url')->nullable(); // Ссылка на GitHub
            $table->json('technologies'); // Массив технологий
            $table->string('category'); // web, mobile, desktop, etc.
            $table->string('client')->nullable();
            $table->date('completed_at');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index(['is_published', 'is_featured']);
            $table->index(['category', 'is_published']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
