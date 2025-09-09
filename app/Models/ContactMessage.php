<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $is_read
 * @property mixed $resume_file
 * @property mixed $name
 * @property mixed $id
 * @use HasFactory<ContactMessageFactory>
 * @method static count()
 * @method static where(string $string, false $false)
 * @method static whereDate(string $string, \Carbon\CarbonInterface|\Illuminate\Support\Carbon $today)
 * @method static whereBetween(string $string, array $array)
 * @method static whereMonth(string $string, int $month)
 * @method static whereNotNull(string $string)
 * @method static whereIn(string $string, mixed $ids)
 */
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'service',
        'budget',
        'message',
        'message_type',
        'position_interest',
        'resume_file',
        'portfolio_url',
        'experience_summary',
        'availability',
        'salary_expectation',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'salary_expectation' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessors
    public function getFormattedSalaryExpectationAttribute(): ?string
    {
        if ($this->salary_expectation) {
            return '$' . number_format((float) $this->salary_expectation, 0);
        }
        return null;
    }

    public function getIsJobApplicationAttribute(): bool
    {
        return $this->message_type === 'job_application';
    }

    public function getResumeUrlAttribute(): ?string
    {
        if ($this->resume_file) {
            return asset('storage/' . $this->resume_file);
        }
        return null;
    }

    // Existing methods
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function scopeUnread(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_read', true);
    }

    public function scopeRecent(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // New scopes
    public function scopeJobApplications(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('message_type', 'job_application');
    }

    public function scopeGeneralMessages(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('message_type', 'general');
    }

    public function scopeByMessageType(\Illuminate\Database\Eloquent\Builder $query, string $type): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('message_type', $type);
    }
}
