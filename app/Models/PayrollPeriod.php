<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'start_date',
    'end_date',
    'period_start',
    'period_end',
    'period_type',
    'status',
    'processed_at',
    'total_amount',
])]
class PayrollPeriod extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'processed_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the individual payrolls for this period.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
