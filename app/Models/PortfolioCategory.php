<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @template TFactory of \Illuminate\Database\Eloquent\Factories\Factory
 */
class PortfolioCategory extends Model
{
    /** @use HasFactory<TFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Автоматическое создание slug при сохранении
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get portfolios for this category
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'portfolio_category_id');
    }

    /**
     * Scope active categories
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered categories
     */
    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope with project counts
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeWithProjectCounts(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->withCount(['portfolios' => function ($query) {
            $query->where('is_published', true);
        }]);
    }

    /**
     * Check if category can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->portfolios()->count() === 0;
    }

    /**
     * Get CSS color attribute
     */
    public function getCssColorAttribute(): string
    {
        return $this->color ?: '#667eea';
    }

    /**
     * Get icon class attribute
     */
    public function getIconClassAttribute(): string
    {
        return $this->icon ?: 'fas fa-folder';
    }
}
