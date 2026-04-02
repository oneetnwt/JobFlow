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
        $plans = \App\Models\Plan::active()->orderBy('monthly_price')->get();   

        // If a plan slug is passed via query string (e.g. from pricing page)   
        $selectedPlanId = null;
        if (request()->has('plan')) {
            $selectedPlan = $plans->where('slug', request('plan'))->first();    
            if ($selectedPlan) {
                $selectedPlanId = $selectedPlan->id;
            }
        }

        // Default to free plan
        if (!$selectedPlanId) {
            $freePlan = $plans->where('is_free', true)->first();
            if ($freePlan) {
                $selectedPlanId = $freePlan->id;
            } elseif ($plans->count() > 0) {
                $selectedPlanId = $plans->first()->id;
            }
        }

        return view('central.auth.register', compact('plans', 'selectedPlanId'));
    }

    /**
     * Handle the incoming registration request.
     */
    public function store(RegisterTenantRequest $request)
    {
        try {
            $tenant = $this->onboardingService->registerTenant($request->validated());

            // The specific tenant domain with scheme
            $domain = $tenant->domains->first()->domain;
            $scheme = request()->secure() ? 'https://' : 'http://';

            // Free plan auto-approved and redirected
            if ($tenant->status === 'pending') { 
                return back()->with('pending', "Your application has been submitted. Our team will review it and contact you at {$tenant->admin_email} within 1 business day."); 
            } 
            
            return redirect()->away($scheme . $domain . '/login')
                ->with('success', "Your account is ready. You can now log in at {$domain}");    
        } catch (Exception $e) {
            // In a production app, we would log this and return a friendlier error
            return back()->withInput()->withErrors([
                'domain' => 'Failed to provision tenant workspace. Please try again or choose a different subdomain. Error: ' . $e->getMessage(),
            ]);
        }
    }
}
