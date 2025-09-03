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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('message_type')->default('general'); // general, job_application, partnership
            $table->string('position_interest')->nullable(); // для заявок на работу
            $table->string('resume_file')->nullable(); // путь к файлу резюме
            $table->string('portfolio_url')->nullable(); // ссылка на портфолио
            $table->text('experience_summary')->nullable(); // краткое описание опыта
            $table->string('availability')->nullable(); // доступность (immediate, 2weeks, 1month, etc.)
            $table->decimal('salary_expectation', 10, 2)->nullable(); // ожидаемая зарплата
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn([
                'message_type',
                'position_interest',
                'resume_file',
                'portfolio_url',
                'experience_summary',
                'availability',
                'salary_expectation'
            ]);
        });
    }
};
