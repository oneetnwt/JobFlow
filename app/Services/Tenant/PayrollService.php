<?php

namespace App\Services\Tenant;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\Timesheet;
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
            $this->generateNewPayrollRecords($period);
            $legacyTotalAmount = $this->generateLegacyPayrolls($period);

            $period->update([
                'status' => 'processed',
                'processed_at' => now(),
                'period_start' => $period->period_start ?? $period->start_date,
                'period_end' => $period->period_end ?? $period->end_date,
                'total_amount' => $legacyTotalAmount,
            ]);
        });
    }

    /**
     * New payroll pipeline: employees + timesheets + payroll_records.
     */
    protected function generateNewPayrollRecords(PayrollPeriod $period): void
    {
        $periodStart = $period->period_start ?? $period->start_date;
        $periodEnd = $period->period_end ?? $period->end_date;

        if (! $periodStart || ! $periodEnd) {
            return;
        }

        $employees = Employee::where('status', 'active')->get();

        foreach ($employees as $employee) {
            $entries = Timesheet::where('employee_id', $employee->id)
                ->whereBetween('work_date', [$periodStart, $periodEnd])
                ->get();

            $hourGroups = $entries->groupBy('work_type');
            $regularHours = (float) $hourGroups->get('regular', collect())->sum('hours_worked');
            $overtimeHours = (float) $hourGroups->get('overtime', collect())->sum('hours_worked');
            $holidayHours = (float) $hourGroups->get('holiday', collect())->sum('hours_worked');

            $totalHours = $regularHours + $overtimeHours + $holidayHours;

            // Basic multipliers for work types; easy to externalize later.
            $weightedHours = $regularHours + ($overtimeHours * 1.25) + ($holidayHours * 2.00);

            $grossPay = 0.0;
            if (! is_null($employee->hourly_rate) && (float) $employee->hourly_rate > 0) {
                $grossPay = $weightedHours * (float) $employee->hourly_rate;
            } elseif (! is_null($employee->daily_rate) && (float) $employee->daily_rate > 0) {
                $daysWorked = $entries->pluck('work_date')->unique()->count();
                $grossPay = $daysWorked * (float) $employee->daily_rate;
            }

            PayrollRecord::updateOrCreate(
                [
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'total_hours' => $totalHours,
                    'gross_pay' => $grossPay,
                    'net_pay' => $grossPay,
                    'payment_status' => 'pending',
                ]
            );
        }
    }

    /**
     * Legacy pipeline: users + worker_profiles + payrolls.
     */
    protected function generateLegacyPayrolls(PayrollPeriod $period): float
    {
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

            return $totalAmount;
    }

    /**
     * Mark a payroll period as released/paid.
     */
    public function releasePeriod(PayrollPeriod $period): void
    {
        DB::transaction(function () use ($period) {
            $period->update(['status' => 'released', 'processed_at' => now()]);

            PayrollRecord::where('payroll_period_id', $period->id)
                ->update(['payment_status' => 'paid']);

            $period->payrolls()->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }
}
