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
        Schema::table('news', static function (Blueprint $table) {
            // Проверяем и добавляем только те колонки, которых еще нет
            if (!Schema::hasColumn('news', 'status')) {
                $table->string('status')->default('draft')->after('category');
            }

            if (!Schema::hasColumn('news', 'reading_time')) {
                $table->integer('reading_time')->default(1)->after('views');
            }

            if (!Schema::hasColumn('news', 'tags')) {
                $table->json('tags')->nullable()->after('reading_time');
            }

            if (!Schema::hasColumn('news', 'meta_title')) {
                $table->string('meta_title', 60)->nullable()->after('tags');
            }

            if (!Schema::hasColumn('news', 'meta_description')) {
                $table->string('meta_description', 160)->nullable()->after('meta_title');
            }

            if (!Schema::hasColumn('news', 'social_image_url')) {
                $table->string('social_image_url')->nullable()->after('image_url');
            }
        });

        // Добавляем индекс только если его еще нет
        if (!Schema::hasIndex('news', 'news_status_index')) {
            Schema::table('news', static function (Blueprint $table) {
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', static function (Blueprint $table) {
            $table->dropIndex(['status']);

            $table->dropColumn([
                'status',
                'reading_time',
                'tags',
                'meta_title',
                'meta_description',
                'social_image_url'
            ]);
        });
    }
};
