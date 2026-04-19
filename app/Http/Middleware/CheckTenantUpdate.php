<?php

namespace App\Http\Middleware;

use App\Models\AppVersion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantUpdate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant) {
            $cacheKey = "tenant_{$tenant->id}_pending_update";

            $pendingUpdate = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($tenant) {
                $query = AppVersion::query()->orderBy('released_at', 'desc');

                if (! config('updates.include_prereleases', false)) {
                    $query->production();
                }

                $latestVersion = $query->first();

                if ($latestVersion && $tenant->current_version !== $latestVersion->version) {

                    if ($latestVersion->is_critical) {
                        return $latestVersion;
                    }

                    if ($tenant->update_dismissed_at && $tenant->update_dismissed_at >= $latestVersion->released_at) {
                        return null;
                    }

                    return $latestVersion;
                }

                return null;
            });

            View::share('pendingUpdate', $pendingUpdate);
        }

        return $next($request);
    }
}
