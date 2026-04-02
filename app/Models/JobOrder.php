<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'status', 'priority', 'deadline_at', 'completed_at', 'created_by', 'assigned_to'])]
class JobOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the tasks for the job order.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Calculate job progress percentage based on completed tasks.
     */
    public function getProgressAttribute(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return $this->status === 'completed' ? 100 : 0;
        }

        $completed = $this->tasks()->where('status', 'completed')->count();
        
        return (int) (($completed / $total) * 100);
    }

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created the job order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to this job order.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Scope for open jobs.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
