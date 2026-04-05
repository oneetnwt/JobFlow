<?php

namespace App\Services\Central;

use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
     * @return array{tenant: Tenant, tenant_admin_user_id: int}
     * @throws ValidationException
     */
    public function registerTenant(array $data): array
    {
        // 1) Create tenant record in central DB.
        $domain = $data['domain'];
        $subdomain = $data['subdomain'];
        $dbName = $this->buildTenantDatabaseName($subdomain, $data['company_name'] ?? null);

        $plan = null;
        if (!empty($data['plan_id'])) {
            $plan = \App\Models\Plan::find($data['plan_id']);
        }

        $tenant = Tenant::create([
            'company_name' => $data['company_name'],
            'subdomain' => $subdomain,
            'db_name' => $dbName,
            // Tenancy package reads this internal key for actual DB provisioning.
            'tenancy_db_name' => $dbName,
            'db_host' => config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1')),
            'admin_name' => $data['admin_name'],
            'admin_email' => $data['admin_email'],
            'plan_id' => $data['plan_id'] ?? null,
            'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
            'industry' => $data['industry'] ?? null,
            'size' => $data['size'] ?? null,
            'website' => $data['website'] ?? null,
            'status' => 'active',
        ]);

        $this->activityService->log(
            'tenant.registered',
            "New tenant registration for '{$tenant->company_name}'",
            $tenant->id
        );

        // 2) Associate tenant domain for domain-based routing.
        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        // 3) Store central auth metadata for tenant admin.
        TenantUser::create([
            'tenant_id' => $tenant->id,
            'email' => $data['admin_email'],
            'password_hash' => Hash::make($data['password']),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 4) Store central plan metadata.
        TenantPlan::create([
            'tenant_id' => $tenant->id,
            'plan_name' => $plan?->name ?? 'Starter',
            'valid_until' => now()->addMonth(),
            'feature_flags' => $plan?->features ?? ['payroll' => true],
        ]);

        // 5) Initialize tenant context and create first tenant admin user.
        tenancy()->initialize($tenant);

        $tenantAdmin = User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        // 6) Return to central context.
        tenancy()->end();

        return [
            'tenant' => $tenant,
            'tenant_admin_user_id' => $tenantAdmin->id,
        ];
    }

    protected function buildTenantDatabaseName(?string $subdomain, ?string $companyName, ?string $tenantId = null): string
    {
        $candidate = trim((string) ($subdomain ?: ''));

        if ($candidate === '') {
            $candidate = Str::slug((string) ($companyName ?: ''), '_');
        }

        if ($candidate === '' && $tenantId) {
            $candidate = Str::lower(str_replace('-', '', $tenantId));
        }

        if ($candidate === '') {
            $candidate = 'tenant';
        }

        return 'jobflow_' . Str::lower($candidate);
    }
}
