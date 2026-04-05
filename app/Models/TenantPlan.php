<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan_name',
        'valid_until',
        'feature_flags',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'feature_flags' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
