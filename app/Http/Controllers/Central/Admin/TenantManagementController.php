<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Notification;
use App\Notifications\Central\TenantApprovedNotification;

use App\Services\Central\PlatformActivityService;

class TenantManagementController extends Controller
{
    public function __construct(protected PlatformActivityService $activityService)
    {}

    public function index(): View
    {
        $tenants = Tenant::latest()->paginate(15);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant): View
    {
        // Peek into the tenant database to get metrics
        $metrics = $tenant->run(function () {
            return [
                'workers_count' => \App\Models\User::workers()->count(),
                'jobs_count' => \App\Models\JobOrder::count(),
                'tasks_count' => \App\Models\Task::count(),
                'payroll_periods_count' => \App\Models\PayrollPeriod::count(),
                'total_payroll_value' => \App\Models\PayrollPeriod::sum('total_amount'),
            ];
        });

        return view('admin.tenants.show', compact('tenant', 'metrics'));
    }

    public function approve(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);

        // Log the action
        $this->activityService->log(
            'tenant.approved',
            "Approved workspace for '{$tenant->company_name}'",
            $tenant->id
        );

        // Send Notification to the stored admin email
        Notification::route('mail', $tenant->admin_email)
            ->notify(new TenantApprovedNotification($tenant));

        return back()->with('success', "Tenant '{$tenant->company_name}' has been approved and notified.");
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);

        // Log the action
        $this->activityService->log(
            'tenant.suspended',
            "Suspended workspace for '{$tenant->company_name}'",
            $tenant->id
        );

        return back()->with('success', "Tenant '{$tenant->company_name}' has been suspended.");
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'brand_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'logo_url' => ['nullable', 'url'],
        ]);

        $tenant->update($validated);

        // Log the action
        $this->activityService->log(
            'tenant.branded',
            "Updated branding for '{$tenant->company_name}'",
            $tenant->id
        );

        return back()->with('success', 'Tenant branding updated successfully.');
    }

    public function impersonate(Tenant $tenant)
    {
        // Get the first admin user from the tenant database
        $adminUser = $tenant->run(function () {
            return \App\Models\User::where('role', 'admin')->first();
        });

        if (! $adminUser) {
            return back()->with('error', 'No admin user found for this tenant.');
        }

        // Log the impersonation
        $this->activityService->log(
            'tenant.impersonated',
            "Impersonated admin for '{$tenant->company_name}'",
            $tenant->id
        );

        // Generate token and redirect to tenant dashboard
        // Note: The token is valid for a short period
        $token = tenancy()->impersonate($tenant, $adminUser->id, route('tenant.dashboard'), 'web');

        return redirect($token->getTargetUrl());
    }
}
