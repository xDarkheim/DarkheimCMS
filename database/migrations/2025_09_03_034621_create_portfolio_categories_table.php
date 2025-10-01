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
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Название категории (например, "Web Development")
            $table->string('slug')->unique(); // URL-дружественный slug (например, "web-development")
            $table->string('description')->nullable(); // Описание категории
            $table->string('icon')->nullable(); // CSS класс иконки
            $table->string('color', 7)->default('#667eea'); // Цвет категории в hex формате
            $table->boolean('is_active')->default(true); // Активна ли категория
            $table->integer('sort_order')->default(0); // Порядок сортировки
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index(['is_active', 'sort_order']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_categories');
    }
};
