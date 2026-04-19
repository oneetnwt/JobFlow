<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subtask_id', 'job_order_id', 'checked_by', 'checked_at', 'note'])]
class JobOrderSubtaskCompletion extends Model
{
    use HasFactory;

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    /**
     * Get the subtask this completion belongs to.
     */
    public function subtask(): BelongsTo
    {
        return $this->belongsTo(JobOrderSubtask::class);
    }

    /**
     * Get the job order this completion belongs to.
     */
    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    /**
     * Get the worker who checked off the subtask.
     */
    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
