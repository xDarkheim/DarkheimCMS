<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string $name
 * @property string $position
 * @property string $department
 * @property string $bio
 * @property string $email
 * @property string|null $avatar
 * @property array<string> $skills
 * @property array<string, string> $social_links
 * @property string $status
 * @property Carbon $joined_date
 * @property int $priority
 * @property bool $show_on_website
 *
 * @use HasFactory<TeamMemberFactory>
 */
class TeamMember extends Model
{
    /** @use HasFactory<TeamMemberFactory> */
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

    protected static function boot()
    {
        parent::boot();
    }

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
