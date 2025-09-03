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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship']);
            $table->string('location');
            $table->boolean('remote_available')->default(false);
            $table->text('short_description');
            $table->longText('description');
            $table->longText('requirements');
            $table->text('benefits')->nullable();
            $table->string('salary_range')->nullable();
            $table->enum('experience_level', ['junior', 'mid', 'senior', 'lead']);
            $table->json('skills')->nullable(); // JSON array of required skills
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For ordering
            $table->date('application_deadline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
