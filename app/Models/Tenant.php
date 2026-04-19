<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_name',
            'subdomain',
            'db_name',
            'db_host',
            'admin_name',
            'admin_email',
            'plan_id',
            'billing_cycle',
            'brand_color',
            'logo_url',
            'current_version',
            'last_updated_at',
            'update_dismissed_at',
        ];
    }

    /**
     * Get the subscription plan for the tenant.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the latest central plan record for this tenant.
     */
    public function tenantPlan()
    {
        return $this->hasOne(TenantPlan::class)->latestOfMany('valid_until');
    }

    /**
     * Get the new option C subscription for the tenant.
     */
    public function subscription()
    {
        return $this->hasOne(TenantSubscription::class)->latestOfMany();
    }
}
