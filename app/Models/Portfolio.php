<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @use HasFactory<\Database\Factories\PortfolioFactory>
 */
class Portfolio extends Model
{
    /** @use HasFactory<\Database\Factories\PortfolioFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'image_url',
        'gallery_images',
        'project_url',
        'github_url',
        'technologies',
        'category',
        'portfolio_category_id',
        'client',
        'completed_at',
        'is_featured',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'technologies' => 'array',
        'gallery_images' => 'array',
        'completed_at' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * @var array<string>
     */
    protected array $dates = [
        'completed_at',
    ];

    // Автоматическое создание slug при сохранении
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($portfolio) {
            if (empty($portfolio->slug)) {
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });

        static::updating(function ($portfolio) {
            if ($portfolio->isDirty('title')) { // Убираем проверку empty($portfolio->slug)
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });
    }

    /**
     * Связь с категорией портфолио
     */
    public function portfolioCategory(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class);
    }

    /**
     * Получить название категории (с обратной совместимостью)
     */
    public function getCategoryNameAttribute(): ?string
    {
        if ($this->portfolioCategory && isset($this->portfolioCategory->name)) {
            return $this->portfolioCategory->name;
        }
        return $this->category;
    }

    /**
     * Получить slug категории (с обратной совместимостью)
     */
    public function getCategorySlugAttribute(): ?string
    {
        if ($this->portfolioCategory && isset($this->portfolioCategory->slug)) {
            return $this->portfolioCategory->slug;
        }
        return $this->category ? \Illuminate\Support\Str::slug($this->category) : null;
    }

    // Скопы для запросов
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('completed_at', 'desc');
    }

    // Аксессоры
    public function getRouteKeyName()
    {
        // Для admin API используем ID, для публичных маршрутов - slug
        if (request()->is('api/admin/*')) {
            return 'id';
        }
        return 'slug';
    }

    public function getTechnologiesListAttribute(): string
    {
        return implode(', ', $this->technologies ?? []);
    }

    public function getFormattedCompletedDateAttribute(): ?string
    {
        return $this->completed_at->format('M Y');
    }

    // Мутаторы
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}
