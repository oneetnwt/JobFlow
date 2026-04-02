<?php

namespace App\Services\Central;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Services\Central\PlatformActivityService;

class TenantOnboardingService
{
    public function __construct(protected PlatformActivityService $activityService)
    {}

    /**
     * Register a new tenant and provision their database and admin user.
     *
     * @param array $data The validated registration data
     * @return Tenant
     * @throws ValidationException
     */
    public function registerTenant(array $data): Tenant
    {
        // 1. Create the tenant record in the central database
        $domain = $data['domain'];

        $plan = null;
        if (!empty($data['plan_id'])) {
            $plan = \App\Models\Plan::find($data['plan_id']);
        }

        $tenant = Tenant::create([
            'company_name' => $data['company_name'],
            'admin_name' => $data['admin_name'],
            'admin_email' => $data['admin_email'],
            'plan_id' => $data['plan_id'] ?? null,
            'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
            'industry' => $data['industry'] ?? null,
            'size' => $data['size'] ?? null,
            'website' => $data['website'] ?? null,
            'status' => ($plan && $plan->auto_approve) ? 'active' : 'pending',
        ]);

        $this->activityService->log(
            'tenant.registered',
            "New tenant registration for '{$tenant->company_name}'",
            $tenant->id
        );

        // 2. Associate the full domain
        $tenant->domains()->create([
            'domain' => $data['domain'],
        ]);

        // 3. Initialize tenancy context to create the admin user in the isolated DB
        tenancy()->initialize($tenant);

        User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        // 4. Return to the central context
        tenancy()->end();

        return $tenant;
    }
}
