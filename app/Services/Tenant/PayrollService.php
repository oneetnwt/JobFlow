<?php

namespace App\Services\Tenant;

use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate payroll slips for all active workers for a given period.
     */
    public function generateForPeriod(PayrollPeriod $period): void
    {
        DB::transaction(function () use ($period) {
            $workers = User::workers()->with('profile')->get();
            $totalAmount = 0;

            foreach ($workers as $worker) {
                $baseRate = $worker->profile?->hourly_rate ?? 0;
                
                // For this MVP, we assume a standard 40hr/week or 80hr/bi-weekly
                // In a real app, we would sum hours from a 'timesheets' table
                $hoursWorked = 40.00; 
                $gross = $baseRate * $hoursWorked;
                $net = $gross; // Subtract taxes/deductions here if needed

                $payroll = Payroll::updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'user_id' => $worker->id,
                    ],
                    [
                        'base_rate' => $baseRate,
                        'hours_worked' => $hoursWorked,
                        'gross_amount' => $gross,
                        'net_amount' => $net,
                        'status' => 'pending',
                    ]
                );

                $totalAmount += $net;
            }

            $period->update([
                'status' => 'processed',
                'total_amount' => $totalAmount,
            ]);
        });
    }

    /**
     * Mark a payroll period as released/paid.
     */
    public function releasePeriod(PayrollPeriod $period): void
    {
        DB::transaction(function () use ($period) {
            $period->update(['status' => 'released']);
            $period->payrolls()->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }
}
