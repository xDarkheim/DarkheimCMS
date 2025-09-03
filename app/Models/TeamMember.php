<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
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
        'phone',
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
        'skills' => 'json',
        'social_links' => 'json',
        'joined_date' => 'date',
        'show_on_website' => 'boolean',
        'priority' => 'integer'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Scope to get only active team members.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only team members visible on website.
     */
    public function scopeVisible($query)
    {
        return $query->where('show_on_website', true);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get the formatted employment type.
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->status));
    }

    /**
     * Get the avatar URL attribute.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && file_exists(public_path($this->avatar))) {
            return $this->avatar;
        }

        // Return a default avatar if none is set or file doesn't exist
        return '/images/default-avatar.png';
    }
}
