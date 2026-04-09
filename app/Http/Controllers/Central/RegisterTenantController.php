<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegisterTenantRequest;
use App\Services\Central\TenantOnboardingService;
use Exception;

class RegisterTenantController extends Controller
{
    public function __construct(protected TenantOnboardingService $onboardingService)
    {}

    /**
     * Show the tenant registration form.
     */
    public function create()
    {
        $plans = \App\Models\Plan::active()
            ->where('slug', '!=', 'enterprise')
            ->where('name', '!=', 'Enterprise')
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        $selectedPlanId = old('plan_id');

        if (! $selectedPlanId && request()->has('plan')) {
            $selectedPlan = $plans->firstWhere('slug', request('plan'));
            $selectedPlanId = $selectedPlan?->id;
        }

        if (! $selectedPlanId) {
            $selectedPlanId = $plans->firstWhere('is_free', true)?->id ?? $plans->first()?->id;
        }

        return view('central.auth.register', compact('plans', 'selectedPlanId'));
    }

    /**
     * Handle the incoming registration request.
     */
    public function store(RegisterTenantRequest $request)
    {
        try {
            $this->onboardingService->registerTenant($request->validated());

            return redirect()->route('tenant.register.create')->with('success', 'Registration received. Your workspace will be provisioned after admin approval.');
        } catch (Exception $e) {
            // In a production app, we would log this and return a friendlier error
            return back()->withInput()->withErrors([
                'domain' => 'Failed to submit tenant registration request. Please try again or choose a different subdomain. Error: ' . $e->getMessage(),
            ]);
        }
    }
}
