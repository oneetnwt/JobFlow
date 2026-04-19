<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['job_order_id', 'title', 'description', 'order', 'is_required', 'created_by', 'updated_by'])]
class JobOrderSubtask extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the job order that owns the subtask.
     */
    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    /**
     * Get all completions for this subtask (usually just one per job instance).
     */
    public function completion(): HasOne
    {
        return $this->hasOne(JobOrderSubtaskCompletion::class, 'subtask_id');
    }

    /**
     * Get the user who created the subtask.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the subtask.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
