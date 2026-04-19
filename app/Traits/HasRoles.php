<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Trait HasRoles
 * Provides RBAC capabilities natively within the tenant context.
 */
trait HasRoles
{
    /**
     * A user belongs to many roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Determine if the user has the given role.
     */
    public function hasRole(string|Role $role): bool
    {
        $roleSlug = $role instanceof Role ? $role->slug : $role;

        return $this->roles->contains('slug', $roleSlug);
    }

    /**
     * Determine if the user has the given permission via their roles.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('admin') || $this->hasRole('Admin')) {
            return true;
        }

        return clone $this->getPermissionsViaRoles()->contains('slug', $permission);
    }

    /**
     * Determine if the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole('admin') || $this->hasRole('Admin')) {
            return true;
        }

        $userPermissions = clone $this->getPermissionsViaRoles();

        foreach ($permissions as $permission) {
            if ($userPermissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->hasRole('admin') || $this->hasRole('Admin')) {
            return true;
        }

        $userPermissions = clone $this->getPermissionsViaRoles();

        foreach ($permissions as $permission) {
            if (!$userPermissions->contains('slug', $permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string|Role $role): void
    {
        $roleModel = $this->getRoleModel($role);

        if ($roleModel && !$this->hasRole($roleModel)) {
            $this->roles()->attach($roleModel);
            $this->forgetCachedPermissions();
        }
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string|Role $role): void
    {
        $roleModel = $this->getRoleModel($role);

        if ($roleModel) {
            $this->roles()->detach($roleModel);
            $this->forgetCachedPermissions();
        }
    }

    /**
     * Get all permissions via the user's assigned roles, cached efficiently.
     */
    public function getPermissionsViaRoles(): Collection
    {
        $cacheKey = 'user_permissions_' . $this->id;
        $tenantId = tenant('id') ?? 'central';

        return Cache::tags(['tenant_' . $tenantId, 'rbac'])->rememberForever($cacheKey, function () {
            // Load roles with permissions
            $this->loadMissing('roles.permissions');

            return $this->roles->flatMap(function ($role) {
                return $role->permissions;
            })->unique('slug');
        });
    }

    /**
     * Clear the cached permissions for this user.
     */
    public function forgetCachedPermissions(): void
    {
        $cacheKey = 'user_permissions_' . $this->id;
        $tenantId = tenant('id') ?? 'central';

        Cache::tags(['tenant_' . $tenantId, 'rbac'])->forget($cacheKey);
        // Also unset the loaded relation
        $this->unsetRelation('roles');
    }

    /**
     * Resolve the role model.
     */
    protected function getRoleModel(string|Role $role): ?Role
    {
        if ($role instanceof Role) {
            return $role;
        }

        return Role::where('slug', $role)->first();
    }
}
