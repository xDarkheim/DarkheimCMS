<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string)
 * @method static create(array $validated)
 * @method static where(string $string, mixed $id)
 * @property mixed $id
 */
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

    /**
     * Scope active company info
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope by type
     */
    public function scopeByType(\Illuminate\Database\Eloquent\Builder $query, string $type): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Get by key
     */
    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }

    /**
     * Get contact info
     * @return \Illuminate\Database\Eloquent\Collection<int, CompanyInfo>
     */
    public static function getContactInfo(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->ordered()
            ->whereIn('type', ['email', 'phone', 'address'])
            ->get();
    }

    /**
     * Get social links
     * @return \Illuminate\Database\Eloquent\Collection<int, CompanyInfo>
     */
    public static function getSocialLinks(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->ordered()
            ->where('type', 'social')
            ->get();
    }

    /**
     * Get formatted value attribute
     */
    public function getFormattedValueAttribute(): string
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
