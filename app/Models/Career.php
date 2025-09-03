<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
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
        'application_deadline' => 'date'
    ];

    /**
     * Get the skills attribute.
     */
    public function getSkillsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        // If it's a string, try to decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // If the result is still a string (double-encoded JSON), decode again
                if (is_string($decoded)) {
                    $doubleDecoded = json_decode($decoded, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($doubleDecoded)) {
                        return $doubleDecoded;
                    }
                } elseif (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return [];
    }

    /**
     * Scope to get only active careers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get formatted employment type.
     */
    public function getFormattedEmploymentTypeAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->employment_type));
    }

    /**
     * Get formatted experience level.
     */
    public function getFormattedExperienceLevelAttribute()
    {
        return ucfirst($this->experience_level);
    }

    /**
     * Get formatted salary.
     */
    public function getFormattedSalaryAttribute()
    {
        return $this->salary_range ?: 'Competitive salary';
    }
}
