<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'description', 'status', 'priority', 'deadline_at', 'completed_at', 'created_by', 'assigned_to'])]
class JobOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the tasks for the job order (legacy).
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the checklist subtasks for the job order.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(JobOrderSubtask::class)->orderBy('order');
    }

    /**
     * Get all completions across all subtasks for this job order.
     */
    public function subtaskCompletions(): HasMany
    {
        return $this->hasMany(JobOrderSubtaskCompletion::class);
    }

    /**
     * Get the audit trail logs for this job order.
     */
    public function audits(): HasMany
    {
        return $this->hasMany(JobOrderAudit::class)->latest();
    }

    /**
     * Calculate job progress percentage based on completed tasks/subtasks.
     */
    public function getProgressAttribute(): int
    {
        // Incorporating the new subtasks logic
        $totalSubtasks = $this->subtasks()->count();
        if ($totalSubtasks > 0) {
            $completedSubtasks = $this->subtaskCompletions()->count();

            return (int) (($completedSubtasks / $totalSubtasks) * 100);
        }

        // Fallback to legacy tasks if no subtasks exist
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
