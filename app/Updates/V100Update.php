<?php

namespace App\Updates;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class V100Update
 *
 * Example update handler for version v1.0.0.
 * This class is automatically resolved and executed by ApplyTenantUpdate
 * after the tenant database migrations have finished running.
 */
class V100Update implements TenantUpdateHandler
{
    /**
     * Handle the custom data transformation logic for this update.
     *
     * @throws \Exception
     */
    public function handle(Tenant $tenant): void
    {
        Log::info("Running custom V1.0.0 update logic for tenant: {$tenant->company_name}");

        // Example: You can run raw DB queries or Eloquent updates here.
        // Because this runs inside `tenancy()->initialize($tenant)`,
        // Eloquent queries will automatically route to the tenant's database!

        /*
        // 1. Updating records using Eloquent
        \App\Models\WorkerProfile::whereNull('hourly_rate')->update([
            'hourly_rate' => 0.00
        ]);

        // 2. Running a raw DB query
        DB::table('settings')->updateOrInsert(
            ['key' => 'new_v1_feature_enabled'],
            ['value' => 'true']
        );
        */

        Log::info("V1.0.0 update logic completed for tenant: {$tenant->company_name}");
    }
}
