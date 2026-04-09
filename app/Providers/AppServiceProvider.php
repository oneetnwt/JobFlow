<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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

        // Define Gates
        Gate::define('manage-payroll', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-workers', function (User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('manage-jobs', function (User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });
    }
}
