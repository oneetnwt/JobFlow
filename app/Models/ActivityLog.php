<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'tenant_id', 'event', 'description', 'properties', 'ip_address'])]
class ActivityLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * Get the admin user associated with the log.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the tenant associated with the log (from central context).
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
