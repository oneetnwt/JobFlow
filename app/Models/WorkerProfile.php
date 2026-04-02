<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'employee_id', 'department', 'skills', 'employment_type', 'phone_number', 'joined_at', 'hourly_rate'])]
class WorkerProfile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'hourly_rate' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
