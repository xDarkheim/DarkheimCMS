<?php

namespace App\Models;

use Database\Factories\CareerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * @property string $title
 * @property string $department
 * @property string $employment_type
 * @property string $location
 * @property bool $remote_available
 * @property string $short_description
 * @property string $description
 * @property array<string> $requirements
 * @property array<string> $benefits
 * @property string|null $salary_range
 * @property string $experience_level
 * @property array<string> $skills
 * @property bool $is_active
 * @property int $priority
 * @property \Carbon\Carbon|null $application_deadline
 *
 * @use HasFactory<CareerFactory>
 */
class Career extends Model
{
    /** @use HasFactory<CareerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'department',
        'employment_type',
        'location',
        'remote_available',
        'short_description',
        'description',
        'requirements',
        'benefits',
        'salary_range',
        'experience_level',
        'skills',
        'is_active',
        'priority',
        'application_deadline'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'remote_available' => 'boolean',
        'priority' => 'integer',
        'application_deadline' => 'date',
        'requirements' => 'array',
        'benefits' => 'array',
        'skills' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Scope active careers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get formatted employment type.
     */
    public function getFormattedEmploymentTypeAttribute(): string
    {
        return ucfirst(str_replace('-', ' ', $this->employment_type));
    }

    /**
     * Get formatted experience level.
     */
    public function getFormattedExperienceLevelAttribute(): string
    {
        return ucfirst($this->experience_level);
    }

    /**
     * Get formatted salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        return $this->salary_range ?: 'Competitive salary';
    }
}
