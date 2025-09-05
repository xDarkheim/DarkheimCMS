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

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%");
        });
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    // ==============================================
    // ACCESSOR METHODS
    // ==============================================

    public function getFormattedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('F j, Y') : null;
    }

    public function getCategoryDisplayNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getStatusDisplayNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getSocialImageAttribute()
    {
        return $this->social_image_url ?: $this->image_url;
    }

    public function getMetaTitleFullAttribute()
    {
        return $this->meta_title ?: $this->title;
    }

    public function getMetaDescriptionFullAttribute()
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->content), 160);
    }

    public function getUrlAttribute()
    {
        return "/news/{$this->slug}";
    }

    // ==============================================
    // INSTANCE METHODS
    // ==============================================

    public function calculateReadingTime()
    {
        if (!$this->content) return 1;

        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // 200 слов в минуту
    }

    public function isPublished()
    {
        return $this->is_published &&
               $this->published_at &&
               $this->published_at <= now();
    }

    public function isScheduled()
    {
        return $this->status === self::STATUS_SCHEDULED &&
               $this->published_at &&
               $this->published_at > now();
    }

    public function incrementViews()
    {
        $this->increment('views');
        return $this;
    }

    public function getRelatedArticles($limit = 3)
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

    public static function getCategories()
    {
        return self::CATEGORIES;
    }

    public static function getStatuses()
    {
        return self::STATUSES;
    }

    public static function isValidCategory($category)
    {
        return array_key_exists($category, self::CATEGORIES);
    }

    public static function isValidStatus($status)
    {
        return array_key_exists($status, self::STATUSES);
    }

    public static function getAllTags()
    {
        try {
            return self::whereNotNull('tags')
                      ->where('tags', '!=', '[]')
                      ->where('is_published', true)
                      ->get()
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

    public static function getCategoryStats()
    {
        $stats = self::published()
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray();

        $stats['total'] = self::published()->count();

        return $stats;
    }

    public static function getGlobalStats()
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
