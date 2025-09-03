<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PortfolioCategory extends Model
{
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
     * Связь с портфолио проектами
     */
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'portfolio_category_id');
    }

    /**
     * Скоуп для получения только активных категорий
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Скоуп для сортировки по порядку
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Получить категории с количеством проектов
     */
    public static function withProjectCounts()
    {
        return static::active()
            ->ordered()
            ->withCount(['portfolios' => function ($query) {
                $query->where('is_published', true);
            }])
            ->get();
    }

    /**
     * Проверить, можно ли удалить категорию
     */
    public function canBeDeleted()
    {
        return $this->portfolios()->count() === 0;
    }

    /**
     * Получить цвет категории в CSS формате
     */
    public function getCssColorAttribute()
    {
        return $this->color ?: '#667eea';
    }

    /**
     * Получить иконку категории
     */
    public function getIconClassAttribute()
    {
        return $this->icon ?: 'fas fa-folder';
    }
}
