<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Tenant\RBACService;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use App\Models\Tenant;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            if ($event->command === 'migrate:fresh' && Schema::hasTable('tenants')) {
                try {
                    $tenants = Tenant::all();
                    foreach ($tenants as $tenant) {
                        try {
                            $tenant->database()->manager()->deleteDatabase($tenant);
                            $event->output->writeln("<info>Deleted database for tenant: {$tenant->id}</info>");
                        } catch (\Exception $e) {
                            $event->output->writeln("<error>Failed to delete database for tenant {$tenant->id}: {$e->getMessage()}</error>");
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore if tenants table doesn't exist yet or other DB error
                }
            }
        });

        Authenticate::redirectUsing(function ($request) {
            $host = $request->getHost();

            if (str_starts_with($host, 'admin.')) {
                return '/login';
            }

            return '/login';
        });

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $host = $request->getHost();

            if (str_starts_with($host, 'admin.')) {
                return route('admin.dashboard');
            }

            return '/dashboard';
        });

        // Register Gates per Permission dynamically
        try {
            $rbac = app(RBACService::class);
            if (Schema::hasTable('permissions')) {
                foreach ($rbac->getPermissions() as $permission) {
                    Gate::define($permission->slug, function (User $user) use ($permission) {
                        return $user->hasPermissionTo($permission->slug);
                    });
                }
            }
        } catch (\Exception $e) {
            // Ignore for initial setup or situations where DB isn't available yet
        }
    }
}
