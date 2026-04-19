<?php

namespace App\Jobs\Tenant;

use App\Models\AppVersion;
use App\Models\Tenant;
use App\Notifications\Central\UpdateAppliedNotification;
use App\Notifications\Central\UpdateFailedNotification;
use App\Updates\TenantUpdateHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ApplyTenantUpdate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Tenant $tenant,
        public AppVersion $appVersion
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting update process for {$this->tenant->company_name} to version {$this->appVersion->version}.");

        try {
            // 1. Initialize Tenancy explicitly here to run inside the right scope
            // Note: Since this is purely data transformation without requiring tenant
            // identification traits, we run stancl's initialization.
            tenancy()->initialize($this->tenant);

            // 2. Run Database Migrations
            Artisan::call('tenants:migrate', [
                '--tenants' => [$this->tenant->getTenantKey()],
                '--force' => true,
            ]);

            // 3. Resolve and run custom version handler class if it exists.
            // Handlers should be placed in App\Updates\ and named structurally.
            // Example: version "v2.1.0" would resolve to App\Updates\V210Update
            $normalizedVersion = str_replace(['.', '-'], '', $this->appVersion->version);

            // Allow case insensitivity, capitalize first letter.
            $className = 'App\\Updates\\'.Str::ucfirst($normalizedVersion).'Update';

            if (class_exists($className)) {
                Log::info("Found custom update handler: {$className}");
                /** @var TenantUpdateHandler $handler */
                $handler = app($className);
                $handler->handle($this->tenant);
            }

            // End Tenant Context
            tenancy()->end();

            // 4. Update the Tenant centrally
            $this->tenant->update([
                'current_version' => $this->appVersion->version,
                'last_updated_at' => now(),
                'update_dismissed_at' => null,
            ]);

            Log::info("Successfully updated {$this->tenant->company_name} to version {$this->appVersion->version}.");

            // 5. Notify the Tenant Admin of success
            if (! empty($this->tenant->admin_email)) {
                Notification::route('mail', $this->tenant->admin_email)
                    ->notify(new UpdateAppliedNotification($this->tenant, $this->appVersion));
            }

        } catch (\Throwable $e) {
            // Clean up scoping if it crashed partially
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            Log::error("Failed to apply update {$this->appVersion->version} to tenant {$this->tenant->company_name}. Error: ".$e->getMessage());

            // 6. Notify the Tenant Admin of failure
            if (! empty($this->tenant->admin_email)) {
                Notification::route('mail', $this->tenant->admin_email)
                    ->notify(new UpdateFailedNotification($this->tenant, $this->appVersion, $e->getMessage()));
            }

            throw $e;
        }
    }
}
