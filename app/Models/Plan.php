<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'slug',
    'tagline',
    'monthly_price',
    'annual_price',
    'currency',
    'max_workers',
    'max_job_orders',
    'has_payroll',
    'has_priority_support',
    'has_custom_integrations',
    'is_contact_sales',
    'features',
    'badge_label',
    'status',
    'is_free',
    'auto_approve',
    'sort_order',
])]
class Plan extends Model
{
    use HasFactory, SoftDeletes;

    public function getConnectionName()
    {
        return config('tenancy.database.central_connection');
    }

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'monthly_price' => 'decimal:2',
            'annual_price' => 'decimal:2',
            'has_payroll' => 'boolean',
            'has_priority_support' => 'boolean',
            'has_custom_integrations' => 'boolean',
            'is_contact_sales' => 'boolean',
            'is_free' => 'boolean',
            'auto_approve' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the tenants on this plan.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
