<?php

namespace App\Services\Central;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class PlatformActivityService
{
    /**
     * Log a platform-level event.
     */
    public function log(string $event, string $description, ?string $tenantId = null, array $properties = []): void
    {
        ActivityLog::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'tenant_id' => $tenantId,
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
        ]);
    }
}
