<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_order_id', 'subtask_id', 'performed_by', 'action', 'old_value', 'new_value'])]
class JobOrderAudit extends Model
{
    use HasFactory;

    /**
     * Get the job order associated with the audit.
     */
    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    /**
     * Get the specific subtask associated with the audit (if any).
     */
    public function subtask(): BelongsTo
    {
        return $this->belongsTo(JobOrderSubtask::class);
    }

    /**
     * Get the user who triggered the audited event.
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
