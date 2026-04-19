<?php

namespace App\Updates;

use App\Models\Tenant;

/**
 * Interface TenantUpdateHandler
 * 
 * Executed after a tenant's database migrations have run to perform
 * data transformations tailored to a specific version.
 */
interface TenantUpdateHandler
{
    /**
     * Handle the custom data transformation logic for this update.
     * 
     * @param Tenant $tenant
     * @return void
     * @throws \Exception
     */
    public function handle(Tenant $tenant): void;
}
