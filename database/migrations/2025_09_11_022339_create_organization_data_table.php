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
        Schema::create('organization_data', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // department, position, skill, employment_type, etc.
            $table->string('key')->index(); // уникальный ключ для элемента
            $table->string('value'); // значение для хранения
            $table->string('label'); // человекочитаемое название
            $table->text('description')->nullable(); // описание
            $table->integer('order')->default(0)->index(); // порядок сортировки
            $table->boolean('is_active')->default(true)->index(); // активность
            $table->json('metadata')->nullable(); // дополнительные данные
            $table->timestamps();

            // Составной уникальный индекс
            $table->unique(['type', 'key']);

            // Индексы для быстрого поиска
            $table->index(['type', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_data');
    }
};
