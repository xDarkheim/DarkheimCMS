<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
        'icon',
        'is_active',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'sort_order' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Static methods for easy access
    public static function getByKey($key)
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }

    public static function getContactInfo()
    {
        return static::active()
            ->ordered()
            ->whereIn('type', ['email', 'phone', 'address'])
            ->get();
    }

    public static function getSocialLinks()
    {
        return static::active()
            ->ordered()
            ->where('type', 'social')
            ->get();
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'email':
                return "mailto:{$this->value}";
            case 'phone':
                return "tel:{$this->value}";
            case 'url':
            case 'social':
                return $this->value;
            default:
                return $this->value;
        }
    }
}
