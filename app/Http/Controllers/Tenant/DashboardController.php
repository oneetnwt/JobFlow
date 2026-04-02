<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use App\Models\User;
use App\Models\PayrollPeriod;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard with live analytics.
     */
    public function index(): View
    {
        $stats = [
            'active_jobs' => JobOrder::whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
            'total_workers' => User::workers()->count(),
            'pending_payroll' => PayrollPeriod::where('status', 'processed')->sum('total_amount'),
            'completed_jobs_count' => JobOrder::where('status', 'completed')->count(),
        ];

        $recent_jobs = JobOrder::with(['assignee', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard', compact('stats', 'recent_jobs'));
    }
}
