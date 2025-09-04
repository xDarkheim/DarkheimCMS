<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @method static create(array|string $array_merge)
 * @method static where(string $string, string $key)
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'value' => 'json'
    ];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /**
     * Get public settings (for frontend)
     */
    public static function getPublic(): array
    {
        return Cache::remember('public_settings', 3600, function () {
            return static::where('is_public', true)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('public_settings');
        // Clear individual setting caches would require knowing all keys
        // In production, consider using cache tags
    }
}
