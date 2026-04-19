<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\Tenant\ApplyTenantUpdate;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TenantUpdateController extends Controller
{
    /**
     * Display the list of available updates.
     */
    public function index()
    {
        $tenant = tenant();
        $query = AppVersion::query()->orderBy('released_at', 'desc');

        if (! config('updates.include_prereleases', false)) {
            $query->production();
        }

        $versions = $query->take(10)->get();

        return view('tenant.updates.index', [
            'versions' => $versions,
            'currentVersion' => $tenant->current_version,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Apply a specific update version.
     */
    public function apply(Request $request, AppVersion $version)
    {
        $tenant = tenant();

        if ($tenant->current_version === $version->version) {
            return back()->with('error', 'This update is already applied.');
        }

        $queue = config('updates.queue', 'default');
        ApplyTenantUpdate::dispatch($tenant, $version)->onQueue($queue);

        return back()->with('success', 'Update application process has been queued. You will be notified shortly via email once completed.');
    }

    /**
     * Dismiss the update banner for the current latest version.
     */
    public function dismiss(Request $request)
    {
        $tenant = tenant();

        $tenant->update([
            'update_dismissed_at' => now(),
        ]);

        Cache::forget("tenant_{$tenant->id}_pending_update");

        return back()->with('success', 'Update prompt dismissed.');
    }
}
