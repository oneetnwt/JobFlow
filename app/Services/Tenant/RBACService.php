<?php

namespace App\Services\Tenant;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;

/**
 * Class RBACService
 * Handles the caching and retrieval of RBAC data in a tenant context.
 */
class RBACService
{
    /**
     * Get all active permissions for the current tenant.
     * Caches the list heavily as system permissions rarely change.
     */
    public function getPermissions()
    {
        $tenantId = tenant('id') ?? 'central';

        return Cache::tags(['tenant_'.$tenantId, 'rbac'])->rememberForever('all_permissions', function () {
            return Permission::all();
        });
    }

    /**
     * Bust the cache when roles or permissions change globally for a tenant.
     */
    public function clearRbacCache()
    {
        $tenantId = tenant('id') ?? 'central';
        Cache::tags(['tenant_'.$tenantId, 'rbac'])->flush();
    }
}
