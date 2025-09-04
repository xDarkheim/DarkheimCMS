<?php

namespace App\Models;

use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @use HasFactory<NewsFactory>
 */
class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    // Предопределенные категории новостей
    const CATEGORIES = [
        'announcements' => 'Announcements',
        'updates' => 'Updates',
        'releases' => 'Releases',
        'development' => 'Development',
        'community' => 'Community',
        'events' => 'Events',
        'tutorials' => 'Tutorials',
        'behind-scenes' => 'Behind the Scenes',
        'partnerships' => 'Partnerships',
        'general' => 'General'
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image_url',
        'author',
        'category',
        'is_published',
        'is_featured',
        'published_at',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    public function getRouteKeyName()
    {
        // Используем slug только для публичных маршрутов
        // Для админских маршрутов (API) используем ID
        if (request()->is('api/admin/*')) {
            return 'id';
        }
        return 'slug';
    }

    /**
     * Получить все доступные категории
     * @return array<string, string>
     */
    public static function getCategories(): array
    {
        return self::CATEGORIES;
    }

    /**
     * Получить отображаемое название категории
     */
    public function getCategoryDisplayNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Проверить валидность категории
     */
    public static function isValidCategory(string $category): bool
    {
        return array_key_exists($category, self::CATEGORIES);
    }

    /**
     * Скоп для фильтрации по категории
     */
    public function scopeByCategory(\Illuminate\Database\Eloquent\Builder $query, string $category): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Скоп для получения опубликованных новостей
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Скоп для получения избранных новостей
     */
    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_featured', true);
    }
}
