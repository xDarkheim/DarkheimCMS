<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
        'changes',
        'severity'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $changes = null,
        string $severity = 'info'
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $changes,
            'severity' => $severity
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin(User $user, bool $successful = true): self
    {
        return static::create([
            'user_id' => $successful ? $user->id : null,
            'action' => $successful ? 'login' : 'login_failed',
            'description' => $successful
                ? "User {$user->email} logged in successfully"
                : "Failed login attempt for {$user->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => $successful ? 'info' : 'warning'
        ]);
    }

    /**
     * Log logout activity
     */
    public static function logLogout(): self
    {
        $user = auth()->user();
        return static::create([
            'user_id' => $user?->id,
            'action' => 'logout',
            'description' => "User {$user?->email} logged out",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => 'info'
        ]);
    }

    /**
     * Log model creation
     */
    public static function logCreated(Model $model, string $description = null): self
    {
        return static::log(
            'created',
            $description ?: "Created {$model->getMorphClass()} #{$model->id}",
            $model->getMorphClass(),
            $model->id,
            $model->toArray(),
            'info'
        );
    }

    /**
     * Log model update
     */
    public static function logUpdated(Model $model, array $originalData, string $description = null): self
    {
        $changes = [];
        foreach ($model->getDirty() as $key => $newValue) {
            $changes[$key] = [
                'old' => $originalData[$key] ?? null,
                'new' => $newValue
            ];
        }

        return static::log(
            'updated',
            $description ?: "Updated {$model->getMorphClass()} #{$model->id}",
            $model->getMorphClass(),
            $model->id,
            $changes,
            'info'
        );
    }

    /**
     * Log model deletion
     */
    public static function logDeleted(Model $model, string $description = null): self
    {
        return static::log(
            'deleted',
            $description ?: "Deleted {$model->getMorphClass()} #{$model->id}",
            $model->getMorphClass(),
            $model->id,
            $model->toArray(),
            'warning'
        );
    }

    /**
     * Log security events
     */
    public static function logSecurity(string $event, string $description): self
    {
        return static::log(
            'security',
            $description,
            null,
            null,
            null,
            'critical'
        );
    }

    /**
     * Get formatted changes for display
     */
    public function getFormattedChangesAttribute(): string
    {
        if (!$this->changes) return '';

        $formatted = [];
        foreach ($this->changes as $field => $change) {
            if (is_array($change) && isset($change['old'], $change['new'])) {
                $formatted[] = "{$field}: '{$change['old']}' → '{$change['new']}'";
            }
        }

        return implode(', ', $formatted);
    }

    /**
     * Get severity color for display
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => '#dc3545',
            'warning' => '#fd7e14',
            'info' => '#0dcaf0',
            'success' => '#198754',
            default => '#6c757d'
        };
    }
}
