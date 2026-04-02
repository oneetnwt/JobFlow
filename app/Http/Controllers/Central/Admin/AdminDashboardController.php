<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\View\View;

use App\Models\ActivityLog;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'pending_approvals' => Tenant::where('status', 'pending')->count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
        ];

        $recent_tenants = Tenant::latest()->take(5)->get();
        $recent_logs = ActivityLog::with(['admin', 'tenant'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_tenants', 'recent_logs'));
    }
}
