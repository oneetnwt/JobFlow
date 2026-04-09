<?php

namespace App\Services\Central;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class TenantOnboardingService
{
    public function __construct(protected PlatformActivityService $activityService) {}

    /**
     * Register a new tenant request without provisioning domain/database.
     * Actual provisioning happens only after admin approval.
     *
     * @param  array  $data  The validated registration data
     * @throws ValidationException
     */
    public function registerTenant(array $data): Tenant
    {
        // Create tenant request in central DB only.
        $subdomain = $data['subdomain'];
        $dbName = $this->buildTenantDatabaseName($subdomain, $data['company_name'] ?? null);

        $plan = null;
        if (! empty($data['plan_id'])) {
            $plan = Plan::find($data['plan_id']);
        }

        $tenant = null;

        try {
            $tenant = Tenant::create([
                'company_name' => $data['company_name'],
                'subdomain' => $subdomain,
                'db_name' => $dbName,
                // Keep intended DB name; actual DB creation happens on admin approval.
                'tenancy_db_name' => $dbName,
                'db_host' => config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1')),
                'admin_name' => $data['admin_name'],
                'admin_email' => $data['admin_email'],
                'plan_id' => $data['plan_id'] ?? null,
                'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
                'industry' => $data['industry'] ?? null,
                'size' => $data['size'] ?? null,
                'website' => $data['website'] ?? null,
                'status' => 'pending',
            ]);

            $this->activityService->log(
                'tenant.registered',
                "New tenant registration request for '{$tenant->company_name}'",
                $tenant->id
            );

            // Store central auth metadata; used later during approval provisioning.
            TenantUser::create([
                'tenant_id' => $tenant->id,
                'email' => $data['admin_email'],
                'password_hash' => Hash::make($data['password']),
                'role' => 'admin',
                'is_active' => false,
            ]);

            // Store central plan metadata.
            TenantPlan::create([
                'tenant_id' => $tenant->id,
                'plan_name' => $plan?->name ?? 'Starter',
                'valid_until' => now()->addMonth(),
                'feature_flags' => $plan?->features ?? ['payroll' => true],
            ]);

            return $tenant;
        } catch (Throwable $e) {
            if ($tenant && $tenant->exists) {
                $tenant->delete();
            }

            throw $e;
        }
    }

    /**
     * Provision tenant domain/database/admin user after admin approval.
     */
    public function approveTenant(Tenant $tenant): void
    {
        if ($tenant->status === 'active' && $tenant->domains()->exists()) {
            return;
        }

        $tenantAdminCredentials = TenantUser::query()
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->latest('id')
            ->first();

        if (! $tenantAdminCredentials) {
            throw ValidationException::withMessages([
                'tenant' => 'Cannot approve tenant: missing tenant admin credentials.',
            ]);
        }

        $domain = $this->buildTenantDomain($tenant->subdomain ?: (string) $tenant->getTenantKey());
        $tenancyInitialized = false;

        try {
            $tenant->domains()->firstOrCreate([
                'domain' => $domain,
            ]);

            // This initializes tenant context and triggers DB provisioning for tenancy_db_name.
            tenancy()->initialize($tenant);
            $tenancyInitialized = true;

            User::updateOrCreate(
                ['email' => $tenantAdminCredentials->email],
                [
                    'name' => $tenant->admin_name,
                    'password' => $tenantAdminCredentials->password_hash,
                    'role' => 'admin',
                ]
            );

            tenancy()->end();
            $tenancyInitialized = false;

            $tenant->update([
                'status' => 'active',
            ]);

            $tenantAdminCredentials->update([
                'is_active' => true,
            ]);
        } catch (Throwable $e) {
            if ($tenancyInitialized) {
                tenancy()->end();
            }

            throw $e;
        }
    }

    protected function buildTenantDomain(string $subdomain): string
    {
        $baseDomain = collect(config('tenancy.central_domains', []))
            ->map(fn ($domain) => preg_replace('/:\\d+$/', '', (string) $domain))
            ->filter()
            ->reject(fn ($domain) => str_starts_with((string) $domain, 'admin.'))
            ->first() ?? 'localhost';

        return $subdomain.'.'.$baseDomain;
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

        return 'jobflow_'.Str::lower($candidate);
    }
}
