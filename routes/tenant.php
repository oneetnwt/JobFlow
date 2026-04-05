<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------     
| Tenant Routes
|--------------------------------------------------------------------------     
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenancyServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\JobOrderController;
use App\Http\Controllers\Tenant\WorkerController;
use App\Http\Controllers\Tenant\TaskController;
use App\Http\Controllers\Tenant\PayrollController;
use App\Http\Controllers\Tenant\DashboardController;
use Stancl\Tenancy\Features\UserImpersonation;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\TenantActiveMiddleware::class,
])->group(function () {
    
    // Guest Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('tenant.login');
        Route::post('/login', [LoginController::class, 'store'])->name('tenant.login.store');
        Route::get('/impersonate/{token}', function (string $token) {
            return UserImpersonation::makeResponse($token);
        })->name('tenant.impersonate');
    });

    // Authenticated Routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('tenant.logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

        // Job Order Management
        Route::resource('jobs', JobOrderController::class)->names('tenant.jobs');
        Route::post('jobs/{job}/tasks', [TaskController::class, 'store'])->name('tenant.tasks.store');
        Route::post('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tenant.tasks.toggle');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tenant.tasks.destroy');

        // Worker Management (Admin/Manager only)
        Route::middleware('can:manage-workers')->group(function () {
            Route::resource('workers', WorkerController::class)->names('tenant.workers');
        });

        // Payroll Management (Admin only)
        Route::middleware('can:manage-payroll')->group(function () {
            Route::get('payroll', [PayrollController::class, 'index'])->name('tenant.payroll.index');
            Route::get('payroll/create', [PayrollController::class, 'create'])->name('tenant.payroll.create');
            Route::post('payroll', [PayrollController::class, 'store'])->name('tenant.payroll.store');
            Route::get('payroll/{period}', [PayrollController::class, 'show'])->name('tenant.payroll.show');
            Route::post('payroll/{period}/generate', [PayrollController::class, 'generate'])->name('tenant.payroll.generate');
            Route::post('payroll/{period}/release', [PayrollController::class, 'release'])->name('tenant.payroll.release');
        });
    });

    Route::get('/', function () {
        return redirect('/dashboard');
    });
});
