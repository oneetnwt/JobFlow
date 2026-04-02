<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a comprehensive list of platform activity logs.
     */
    public function index(): View
    {
        $logs = ActivityLog::with(['admin', 'tenant'])
            ->latest()
            ->paginate(50);

        return view('admin.logs.index', compact('logs'));
    }
}
