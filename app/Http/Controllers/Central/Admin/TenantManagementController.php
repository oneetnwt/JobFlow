<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Notification;
use App\Notifications\Central\TenantApprovedNotification;

use App\Services\Central\PlatformActivityService;
use App\Services\Central\TenantOnboardingService;
use Throwable;

class TenantManagementController extends Controller
{
    public function __construct(
        protected PlatformActivityService $activityService,
        protected TenantOnboardingService $onboardingService,
    )
    {}

    public function index(): View
    {
        $tenants = Tenant::with(['tenantPlan', 'plan'])->latest()->paginate(15);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant): View
    {
        $metrics = [
            'workers_count' => 0,
            'jobs_count' => 0,
            'tasks_count' => 0,
            'payroll_periods_count' => 0,
            'total_payroll_value' => 0,
        ];

        // Pending/suspended workspaces may not have a provisioned tenant DB yet.
        if ($tenant->status === 'active') {
            $metrics = $tenant->run(function () {
                return [
                    'workers_count' => \App\Models\User::workers()->count(),
                    'jobs_count' => \App\Models\JobOrder::count(),
                    'tasks_count' => \App\Models\Task::count(),
                    'payroll_periods_count' => \App\Models\PayrollPeriod::count(),
                    'total_payroll_value' => \App\Models\PayrollPeriod::sum('total_amount'),
                ];
            });
        }

        return view('admin.tenants.show', compact('tenant', 'metrics'));
    }

    public function approve(Tenant $tenant)
    {
        try {
            $this->onboardingService->approveTenant($tenant);
        } catch (Throwable $e) {
            return back()->withErrors([
                'approval' => "Failed to approve tenant '{$tenant->company_name}'. Please try again. Error: {$e->getMessage()}",
            ]);
        }

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
        $token = tenancy()->impersonate($tenant, (string) $adminUser->id, '/dashboard', 'web');

        $scheme = request()->secure() ? 'https://' : 'http://';
        $baseDomain = preg_replace('/:\\d+$/', '', (string) (config('tenancy.central_domains')[0] ?? 'localhost'));
        $subdomain = $tenant->subdomain ?: $tenant->getTenantKey();
        $port = request()->getPort();
        $portSegment = in_array((int) $port, [80, 443], true) ? '' : ':' . $port;

        return redirect()->away($scheme . $subdomain . '.' . $baseDomain . $portSegment . '/impersonate/' . $token->token);
    }
}
