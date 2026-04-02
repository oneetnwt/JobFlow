<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['payroll_period_id', 'user_id', 'base_rate', 'hours_worked', 'gross_amount', 'net_amount', 'status', 'paid_at'])]
class Payroll extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'hours_worked' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the payroll period for this payslip.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    /**
     * Get the worker who owns this payslip.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
