<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Portfolio extends Model
{
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

    protected $dates = [
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
            if ($portfolio->isDirty('title') && empty($portfolio->slug)) {
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });
    }

    // Скопы для запросов
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('completed_at', 'desc');
    }

    // Аксессоры
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTechnologiesListAttribute()
    {
        return implode(', ', $this->technologies ?? []);
    }

    public function getFormattedCompletedDateAttribute()
    {
        return $this->completed_at?->format('M Y');
    }

    // Мутаторы
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}
