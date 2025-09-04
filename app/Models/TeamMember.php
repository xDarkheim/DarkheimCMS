<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TFactory of \Illuminate\Database\Eloquent\Factories.Factory
 */
class TeamMember extends Model
{
    /** @use HasFactory<TFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'position',
        'department',
        'bio',
        'email',
        'avatar',
        'skills',
        'social_links',
        'status',
        'joined_date',
        'priority',
        'show_on_website'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'joined_date' => 'date',
        'show_on_website' => 'boolean',
        'priority' => 'integer',
        'skills' => 'array',
        'social_links' => 'array'
    ];

    /**
     * Scope active team members (using status = 'active').
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope visible team members
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('show_on_website', true);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Scope team members by position.
     */
    public function scopeByPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst(str_replace('-', ' ', $this->status));
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path($this->avatar))) {
            return $this->avatar;
        }
        return '/images/default-avatar.png';
    }
}
