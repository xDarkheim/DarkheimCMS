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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('department');
            $table->text('bio');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable(); // Profile photo path
            $table->json('skills')->nullable(); // JSON array of skills
            $table->json('social_links')->nullable(); // JSON object with social media links
            $table->enum('status', ['active', 'inactive', 'on-leave'])->default('active');
            $table->date('joined_date')->nullable();
            $table->integer('priority')->default(0); // For ordering
            $table->boolean('show_on_website')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
