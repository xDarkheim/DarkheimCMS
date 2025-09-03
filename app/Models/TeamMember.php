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
     * Get the social_links attribute.
     */
    public function getSocialLinksAttribute($value)
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
}
