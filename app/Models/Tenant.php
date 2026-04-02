<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_name',
            'admin_name',
            'admin_email',
            'plan_id',
            'billing_cycle',
            'status',
            'brand_color',
            'logo_url',
        ];
    }

    /**
     * Check if the tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the subscription plan for the tenant.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
