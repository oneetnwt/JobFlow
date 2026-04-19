<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use App\Models\PayrollPeriod;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Central\PlatformActivityService;
use App\Services\Central\TenantOnboardingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantManagementController extends Controller
{
    public function __construct(
        protected PlatformActivityService $activityService,
        protected TenantOnboardingService $onboardingService,
    ) {}

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

        $metrics = $tenant->run(function () {
            return [
                'workers_count' => User::workers()->count(),
                'jobs_count' => JobOrder::count(),
                'tasks_count' => Task::count(),
                'payroll_periods_count' => PayrollPeriod::count(),
                'total_payroll_value' => PayrollPeriod::sum('total_amount'),
            ];
        });

        return view('admin.tenants.show', compact('tenant', 'metrics'));
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
            return User::where('role', 'admin')->first();
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
        $portSegment = in_array((int) $port, [80, 443], true) ? '' : ':'.$port;

        return redirect()->away($scheme.$subdomain.'.'.$baseDomain.$portSegment.'/impersonate/'.$token->token);
    }
}
