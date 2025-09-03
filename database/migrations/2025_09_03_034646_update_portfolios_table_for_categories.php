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
        Schema::table('portfolios', function (Blueprint $table) {
            // Добавляем внешний ключ на категорию
            $table->unsignedBigInteger('portfolio_category_id')->nullable()->after('category');

            // Создаем внешний ключ
            $table->foreign('portfolio_category_id')
                  ->references('id')
                  ->on('portfolio_categories')
                  ->onDelete('set null'); // При удалении категории устанавливаем NULL

            // Добавляем индекс для быстрого поиска
            $table->index('portfolio_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            // Удаляем внешний ключ и колонку
            $table->dropForeign(['portfolio_category_id']);
            $table->dropColumn('portfolio_category_id');
        });
    }
};
