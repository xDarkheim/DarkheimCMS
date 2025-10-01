<?php

namespace App\Models;

use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property mixed $title
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

    // Статусы новостей для лучшего контроля публикации
    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SCHEDULED => 'Scheduled',
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_ARCHIVED => 'Archived'
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image_url',
        'author',
        'category',
        'status',
        'is_published',
        'is_featured',
        'published_at',
        'views',
        'reading_time',
        'tags',
        'meta_title',
        'meta_description',
        'social_image_url'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
        'reading_time' => 'integer',
        'tags' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            // Auto-calculate reading time if not set
            if (!$news->reading_time) {
                $news->reading_time = $news->calculateReadingTime();
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            // Update reading time if content changed
            if ($news->isDirty('content')) {
                $news->reading_time = $news->calculateReadingTime();
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

    // ==============================================
    // SCOPE METHODS
    // ==============================================

    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(\Illuminate\Database\Eloquent\Builder $query, string $category): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('category', $category);
    }

    public function scopeByTag(\Illuminate\Database\Eloquent\Builder $query, string $tag): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeByStatus(\Illuminate\Database\Eloquent\Builder $query, string $status): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', $status);
    }

    public function scopeSearch(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%");
        });
    }

    public function scopeRecent(\Illuminate\Database\Eloquent\Builder $query, int $days = 30): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    public function scopePopular(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('views', 'desc');
    }

    // ==============================================
    // ACCESSOR METHODS
    // ==============================================

    public function getFormattedDateAttribute(): ?string
    {
        return $this->published_at ? $this->published_at->format('F j, Y') : null;
    }

    public function getCategoryDisplayNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getStatusDisplayNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getSocialImageAttribute(): ?string
    {
        return $this->social_image_url ?: $this->image_url;
    }

    public function getMetaTitleFullAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function getMetaDescriptionFullAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->content), 160);
    }

    public function getUrlAttribute(): string
    {
        return "/news/{$this->slug}";
    }

    // ==============================================
    // INSTANCE METHODS
    // ==============================================

    public function calculateReadingTime(): int
    {
        if (!$this->content) return 1;

        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // 200 слов в минуту
    }

    public function isPublished(): bool
    {
        return $this->is_published &&
               $this->published_at &&
               $this->published_at <= now();
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED &&
               $this->published_at &&
               $this->published_at > now();
    }

    public function incrementViews(): self
    {
        $this->increment('views');
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, News>
     */
    public function getRelatedArticles(int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        // Сначала ищем по той же категории
        $related = self::published()
                      ->where('id', '!=', $this->id)
                      ->where('category', $this->category)
                      ->orderBy('published_at', 'desc')
                      ->limit($limit)
                      ->get();

        // Если недостаточно статей в той же категории, добавляем из других
        if ($related->count() < $limit) {
            $additional = self::published()
                             ->where('id', '!=', $this->id)
                             ->whereNotIn('id', $related->pluck('id'))
                             ->orderBy('published_at', 'desc')
                             ->limit($limit - $related->count())
                             ->get();

            $related = $related->merge($additional);
        }

        return $related;
    }

    // ==============================================
    // STATIC METHODS
    // ==============================================

    /**
     * @return array<string, mixed>
     */
    public static function getCategories(): array
    {
        return self::CATEGORIES;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    public static function isValidCategory(string $category): bool
    {
        return array_key_exists($category, self::CATEGORIES);
    }

    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::STATUSES);
    }

    /**
     * @return array<string>
     */
    public static function getAllTags(): array
    {
        try {
            return self::whereNotNull('tags')
                      ->where('tags', '!=', '[]')
                      ->where('is_published', true)
                      ->pluck('tags')
                      ->flatten()
                      ->unique()
                      ->filter()
                      ->sort()
                      ->values()
                      ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getCategoryStats(): array
    {
        $stats = self::published()
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray();

        $stats['total'] = self::published()->count();

        return $stats;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getGlobalStats(): array
    {
        return [
            'total' => self::published()->count(),
            'total_views' => self::published()->sum('views'),
            'featured_count' => self::published()->featured()->count(),
            'categories_count' => count(self::CATEGORIES),
            'recent_count' => self::published()->recent(30)->count(),
        ];
    }
}
