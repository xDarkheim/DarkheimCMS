<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrganizationData extends Model
{
    protected $fillable = [
        'type',
        'key',
        'value',
        'label',
        'description',
        'order',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Scopes для разных типов данных
    public function scopeDepartments($query)
    {
        return $query->where('type', 'department')->where('is_active', true)->orderBy('order');
    }

    public function scopePositions($query)
    {
        return $query->where('type', 'position')->where('is_active', true)->orderBy('order');
    }

    public function scopeSkills($query)
    {
        return $query->where('type', 'skill')->where('is_active', true)->orderBy('order');
    }

    public function scopeEmploymentTypes($query)
    {
        return $query->where('type', 'employment_type')->where('is_active', true)->orderBy('order');
    }

    public function scopeExperienceLevels($query)
    {
        return $query->where('type', 'experience_level')->where('is_active', true)->orderBy('order');
    }

    public function scopeLocations($query)
    {
        return $query->where('type', 'location')->where('is_active', true)->orderBy('order');
    }

    public function scopeStatuses($query)
    {
        return $query->where('type', 'status')->where('is_active', true)->orderBy('order');
    }

    // Статические методы для получения данных
    public static function getDepartments()
    {
        return cache()->remember('organization.departments', 3600, function () {
            return static::departments()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    public static function getPositions()
    {
        return cache()->remember('organization.positions', 3600, function () {
            return static::positions()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    public static function getSkills()
    {
        return cache()->remember('organization.skills', 3600, function () {
            return static::skills()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    public static function getEmploymentTypes()
    {
        return cache()->remember('organization.employment_types', 3600, function () {
            return static::employmentTypes()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    public static function getExperienceLevels()
    {
        return cache()->remember('organization.experience_levels', 3600, function () {
            return static::experienceLevels()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    public static function getLocations()
    {
        return cache()->remember('organization.locations', 3600, function () {
            return static::locations()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    public static function getStatuses()
    {
        return cache()->remember('organization.statuses', 3600, function () {
            return static::statuses()->get()->mapWithKeys(function ($item) {
                return [$item->key => $item->label];
            })->toArray();
        });
    }

    // Метод для получения всех данных определенного типа в формате для frontend
    public static function getByTypeFormatted($type)
    {
        return static::where('type', $type)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                return [
                    'key' => $item->key,
                    'label' => $item->label,
                    'description' => $item->description,
                    'order' => $item->order
                ];
            })->toArray();
    }

    // Метод для очистки кэша
    public static function clearCache()
    {
        $keys = [
            'organization.departments',
            'organization.positions',
            'organization.skills',
            'organization.employment_types',
            'organization.experience_levels',
            'organization.locations',
            'organization.statuses'
        ];

        foreach ($keys as $key) {
            cache()->forget($key);
        }
    }

    // Переопределяем метод синхронизации для очистки кэша
    public static function syncData($type, $items, $clearExisting = false)
    {
        if ($clearExisting) {
            static::where('type', $type)->delete();
        }

        foreach ($items as $order => $item) {
            static::updateOrCreate(
                ['type' => $type, 'key' => $item['key']],
                [
                    'value' => $item['value'] ?? $item['key'],
                    'label' => $item['label'] ?? $item['key'],
                    'description' => $item['description'] ?? null,
                    'order' => $item['order'] ?? $order,
                    'is_active' => $item['is_active'] ?? true,
                    'metadata' => $item['metadata'] ?? null
                ]
            );
        }

        // Очищаем кэш после обновления данных
        static::clearCache();
    }
}
