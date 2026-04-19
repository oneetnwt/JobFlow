<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\VerificationController;
use App\Http\Controllers\Tenant\DashboardController;
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

use App\Http\Controllers\Tenant\JobOrderController;
use App\Http\Controllers\Tenant\JobOrderSubtaskController;
use App\Http\Controllers\Tenant\PayrollController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\SubscriptionController;
use App\Http\Controllers\Tenant\SubtaskTemplateController;
use App\Http\Controllers\Tenant\TaskController;
use App\Http\Controllers\Tenant\TenantUpdateController;
use App\Http\Controllers\Tenant\WorkerController;
use App\Http\Middleware\CheckTenantSubscription;
use App\Http\Middleware\CheckTenantUpdate;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    CheckTenantSubscription::class,
    CheckTenantUpdate::class,
])->group(function () {

    // Guest Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('tenant.login');
        Route::post('/login', [LoginController::class, 'store'])->name('tenant.login.store');
        Route::get('/impersonate/{token}', function (string $token) {
            return UserImpersonation::makeResponse($token);
        })->name('tenant.impersonate');
    });

    // Email link verification (no auth required so users can click on phone/other tabs)
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');

    // Authenticated Routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('tenant.logout');

        // Email Verification Routes
        Route::get('/email/verify', [VerificationController::class, 'notice'])
            ->name('verification.notice');

        Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');

        Route::middleware('verified')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

            // Billing and Subscription
            Route::middleware('permission:billing.view')->group(function () {
                Route::get('/billing', [SubscriptionController::class, 'index'])->name('tenant.billing.index');
                Route::post('/billing/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('tenant.billing.checkout');
                Route::get('/billing/success', [SubscriptionController::class, 'success'])->name('tenant.billing.success');
            });

            // System Updates
            Route::get('/updates', [TenantUpdateController::class, 'index'])->name('tenant.updates.index');
            Route::post('/updates/{version}/apply', [TenantUpdateController::class, 'apply'])->name('tenant.updates.apply');
            Route::post('/updates/dismiss', [TenantUpdateController::class, 'dismiss'])->name('tenant.updates.dismiss');

            // Roles Management
            // Tenant Settings
            Route::get('/settings', [SettingsController::class, 'edit'])->name('tenant.settings.edit');
            Route::put('/settings', [SettingsController::class, 'update'])->name('tenant.settings.update');

            Route::middleware('permission:roles.manage')->group(function () {
                Route::resource('roles', RoleController::class)->only(['index', 'edit', 'update'])->names('tenant.roles');
            });

            // Job Order Management
            Route::resource('jobs', JobOrderController::class)->names('tenant.jobs');
            Route::post('jobs/{job}/tasks', [TaskController::class, 'store'])->name('tenant.tasks.store');
            Route::post('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tenant.tasks.toggle');
            Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tenant.tasks.destroy');

            // Subtask Management (Admin & Manager features)
            Route::post('jobs/{job}/subtasks', [JobOrderSubtaskController::class, 'store'])->name('tenant.subtasks.store');
            Route::patch('jobs/{job}/subtasks/{subtask}', [JobOrderSubtaskController::class, 'update'])->name('tenant.subtasks.update');
            Route::delete('jobs/{job}/subtasks/{subtask}', [JobOrderSubtaskController::class, 'destroy'])->name('tenant.subtasks.destroy');
            Route::post('jobs/{job}/subtasks/reorder', [JobOrderSubtaskController::class, 'reorder'])->name('tenant.subtasks.reorder');

            // Subtask Check/Uncheck logic (Worker functionality via AJAX)
            Route::post('jobs/{job}/subtasks/{subtask}/toggle', [JobOrderSubtaskController::class, 'toggle'])->name('tenant.subtasks.toggle');

            // Subtask Templates Management (Admin & Manager features)
            Route::get('subtask-templates', [SubtaskTemplateController::class, 'index'])->name('tenant.subtask-templates.index');
            Route::patch('subtask-templates/{template}', [SubtaskTemplateController::class, 'update'])->name('tenant.subtask-templates.update');
            Route::delete('subtask-templates/{template}', [SubtaskTemplateController::class, 'destroy'])->name('tenant.subtask-templates.destroy');

            Route::post('jobs/{job}/subtasks/save-template', [SubtaskTemplateController::class, 'saveFromJob'])->name('tenant.subtasks.save-template');
            Route::post('jobs/{job}/subtasks/load-template', [SubtaskTemplateController::class, 'loadIntoJob'])->name('tenant.subtasks.load-template');

            // Worker Management
            Route::middleware('permission:users.view|users.manage')->group(function () {
                Route::resource('workers', WorkerController::class)->names('tenant.workers');
            });

            // Payroll Management
            Route::middleware('permission:payroll.view|payroll.manage')->group(function () {
                Route::get('payroll', [PayrollController::class, 'index'])->name('tenant.payroll.index');
                Route::get('payroll/create', [PayrollController::class, 'create'])->name('tenant.payroll.create');
                Route::post('payroll', [PayrollController::class, 'store'])->name('tenant.payroll.store');
                Route::get('payroll/{period}', [PayrollController::class, 'show'])->name('tenant.payroll.show');
                Route::post('payroll/{period}/generate', [PayrollController::class, 'generate'])->name('tenant.payroll.generate');
                Route::post('payroll/{period}/release', [PayrollController::class, 'release'])->name('tenant.payroll.release');
            });

        }); // End verified middleware group
    });

    Route::get('/', function () {
        return redirect('/dashboard');
    });
});
