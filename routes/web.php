<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\RegisterTenantController;
use App\Http\Controllers\Central\Admin\AdminDashboardController;
use App\Http\Controllers\Central\Admin\AdminAuthController;
use App\Http\Controllers\Central\Admin\TenantManagementController;
use App\Http\Controllers\Central\Admin\ActivityLogController;
use App\Http\Controllers\Central\Admin\PricingPlanController;

// Loop over your configured central domains (from config/tenancy.php)
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->middleware('web')->group(function () use ($domain) {
        
        // 1. Super Admin Portal (Restricted to admin.* subdomains)
        if (str_starts_with($domain, 'admin.')) {
            
            // Define plain 'login' route for Laravel's auth middleware to redirect to
            Route::get('/login', [AdminAuthController::class, 'create'])->name('login')->middleware('guest');

            Route::name('admin.')->group(function () {
                Route::middleware('guest')->group(function () {
                    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
                    Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');
                });

                Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsSuperAdmin::class])->group(function () {
                    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
                    // Dashboard is now mapped to the root "/"
                    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
                    
                    // Tenant Management
                    Route::get('/tenants', [TenantManagementController::class, 'index'])->name('tenants.index');
                    Route::get('/tenants/{tenant}', [TenantManagementController::class, 'show'])->name('tenants.show');
                    Route::put('/tenants/{tenant}', [TenantManagementController::class, 'update'])->name('tenants.update');
                    Route::post('/tenants/{tenant}/approve', [TenantManagementController::class, 'approve'])->name('tenants.approve');
                    Route::post('/tenants/{tenant}/suspend', [TenantManagementController::class, 'suspend'])->name('tenants.suspend');
                    Route::get('/tenants/{tenant}/impersonate', [TenantManagementController::class, 'impersonate'])->name('tenants.impersonate');

                    // Audit Logs
                    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

                    // Pricing Plans
                    Route::resource('plans', PricingPlanController::class)->except(['show'])->names('plans');
                });
            });
        }

        // 2. Main Public Application Routes
        else {
            Route::get('/', function () {
                return view('welcome');
            })->name('home');

            Route::get('/pricing', [\App\Http\Controllers\Central\PricingController::class, 'index'])->name('pricing');

            // Registration Flow
            Route::get('/register', [RegisterTenantController::class, 'create'])->name('tenant.register.create');
            Route::post('/register', [RegisterTenantController::class, 'store'])->name('tenant.register.store');
        }

    });
}
