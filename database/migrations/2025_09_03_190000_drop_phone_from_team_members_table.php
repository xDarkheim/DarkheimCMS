<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('team_members', 'phone')) {
            // Use raw SQL to avoid requiring doctrine/dbal
            DB::statement('ALTER TABLE team_members DROP COLUMN phone');
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('team_members', 'phone')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->string('phone')->nullable();
            });
        }
    }
};

