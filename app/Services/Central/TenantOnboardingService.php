<?php

namespace App\Services\Central;

use App\Models\CentralUser;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\TenantSubscription;
use App\Models\TenantUser;
use App\Models\User;
use App\Notifications\Central\NewTenantSignupNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class TenantOnboardingService
{
    public function __construct(protected PlatformActivityService $activityService)
    {
    }

    /**
     * Register a new tenant request and provision domain/database immediately.
     *
     * @param  array  $data  The validated registration data
     *
     * @throws ValidationException
     */
    public function registerTenant(array $data): Tenant
    {
        $subdomain = $data['subdomain'];
        $dbName = $this->buildTenantDatabaseName($subdomain, $data['company_name'] ?? null);

        $plan = null;
        if (!empty($data['plan_id'])) {
            $plan = Plan::find($data['plan_id']);
        }

        $tenant = null;
        $tenancyInitialized = false;

        try {
            $tenant = Tenant::create([
                'company_name' => $data['company_name'],
                'subdomain' => $subdomain,
                'db_name' => $dbName,
                'tenancy_db_name' => $dbName,
                'db_host' => config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1')),
                'admin_name' => $data['admin_name'],
                'admin_email' => $data['admin_email'],
                'plan_id' => $data['plan_id'] ?? null,
                'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
                'industry' => $data['industry'] ?? null,
                'size' => $data['size'] ?? null,
                'website' => $data['website'] ?? null,
            ]);

            $this->activityService->log(
                'tenant.registered',
                "New tenant registration for '{$tenant->company_name}'",
                $tenant->id
            );

            $passwordHash = Hash::make($data['password']);

            // Store central auth metadata.
            $tenantAdminCredentials = TenantUser::create([
                'tenant_id' => $tenant->id,
                'email' => $data['admin_email'],
                'password_hash' => $passwordHash,
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Store central plan metadata.
            TenantPlan::create([
                'tenant_id' => $tenant->id,
                'plan_name' => $plan?->name ?? 'Starter',
                'valid_until' => now()->addDays($plan ? $plan->trial_days : 14),
                'feature_flags' => $plan?->features ?? ['payroll' => true],
            ]);

            // Create the trial subscription
            $trialDays = $plan ? $plan->trial_days : 14;
            TenantSubscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan ? $plan->id : Plan::where('slug', 'starter')->first()?->id,
                'status' => 'trialing',
                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays($trialDays),
                'current_period_start' => now(),
                'current_period_end' => now()->addDays($trialDays),
            ]);

            // Instantly provision domain and run tenant DB migrations
            $domain = $this->buildTenantDomain($tenant->subdomain ?: (string) $tenant->getTenantKey());

            $tenant->domains()->firstOrCreate([
                'domain' => $domain,
            ]);

            // Initialize tenant context
            tenancy()->initialize($tenant);
            $tenancyInitialized = true;

            $tenantAdmin = User::updateOrCreate(
                ['email' => $tenantAdminCredentials->email],
                [
                    'name' => $tenant->admin_name,
                    'password' => $tenantAdminCredentials->password_hash,
                ]
            );
            $tenantAdmin->assignRole('admin');

            // Directly send the email verification notification within the tenant context
            // instead of relying on the Registered event listener which might not be mapped
            // or might be incorrectly queued outside the tenant context
            $tenantAdmin->sendEmailVerificationNotification();

            tenancy()->end();
            $tenancyInitialized = false;

            // Notify Central Admins
            $superAdmins = CentralUser::where('is_super_admin', true)->get();
            Notification::send($superAdmins, new NewTenantSignupNotification($tenant, $plan));

            return $tenant;
        } catch (Throwable $e) {
            if ($tenancyInitialized) {
                tenancy()->end();
            }

            if ($tenant && $tenant->exists) {
                $tenant->delete();
            }

            throw $e;
        }
    }

    protected function buildTenantDomain(string $subdomain): string
    {
        $baseDomain = collect(config('tenancy.central_domains', []))
            ->map(fn($domain) => preg_replace('/:\\d+$/', '', (string) $domain))
            ->filter()
            ->reject(fn($domain) => str_starts_with((string) $domain, 'admin.'))
            ->first() ?? 'localhost';

        return $subdomain . '.' . $baseDomain;
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
