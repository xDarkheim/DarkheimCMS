<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
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
    public function getFormattedSalaryExpectationAttribute()
    {
        if ($this->salary_expectation) {
            return '$' . number_format($this->salary_expectation, 0);
        }
        return null;
    }

    public function getIsJobApplicationAttribute()
    {
        return $this->message_type === 'job_application';
    }

    public function getResumeUrlAttribute()
    {
        if ($this->resume_file) {
            return asset('storage/' . $this->resume_file);
        }
        return null;
    }

    // Existing methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // New scopes
    public function scopeJobApplications($query)
    {
        return $query->where('message_type', 'job_application');
    }

    public function scopeGeneralMessages($query)
    {
        return $query->where('message_type', 'general');
    }

    public function scopeByMessageType($query, $type)
    {
        return $query->where('message_type', $type);
    }
}
