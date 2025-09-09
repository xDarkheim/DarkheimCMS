<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @method static count()
 * @method static whereDate(string $string, CarbonInterface|Carbon $today)
 * @method static whereBetween(string $string, array $array)
 * @method static whereMonth(string $string, int $month)
 * @method static where(string $string, string $string1)
 * @method static selectRaw(string $string)
 * @method static distinct()
 */
class ActivityLog extends Model
{
    /**
     * @var array<int, string>
     */
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

    /**
     * @var array<string, string>
     */
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
     *
     * @param array<string, mixed>|null $changes
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
    public static function logCreated(Model $model, ?string $description = null): self
    {
        return static::log(
            'created',
            $description ?? "Created {$model->getMorphClass()} #{$model->getKey()}",
            $model->getMorphClass(),
            (int) $model->getKey(),
            $model->toArray(),
            'info'
        );
    }

    /**
     * Log model update
     *
     * @param array<string, mixed> $originalData
     */
    public static function logUpdated(Model $model, array $originalData, ?string $description = null): self
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
            $description ?? "Updated {$model->getMorphClass()} #{$model->getKey()}",
            $model->getMorphClass(),
            (int) $model->getKey(),
            $changes,
            'info'
        );
    }

    /**
     * Log model deletion
     */
    public static function logDeleted(Model $model, ?string $description = null): self
    {
        return static::log(
            'deleted',
            $description ?? "Deleted {$model->getMorphClass()} #{$model->getKey()}",
            $model->getMorphClass(),
            (int) $model->getKey(),
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
            'security_event',
            $description,
            null,
            null,
            ['event' => $event],
            'critical'
        );
    }

    /**
     * Get severity levels
     *
     * @return array<string>
     */
    public static function getSeverityLevels(): array
    {
        return ['info', 'warning', 'critical', 'success'];
    }

    /**
     * Scope for filtering by severity
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}
