<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegisterTenantRequest;
use App\Services\Central\TenantOnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Exception;

class RegisterTenantController extends Controller
{
    public function __construct(protected TenantOnboardingService $onboardingService)
    {
    }

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

        if (!$selectedPlanId && request()->has('plan')) {
            $selectedPlan = $plans->firstWhere('slug', request('plan'));
            $selectedPlanId = $selectedPlan?->id;
        }

        if (!$selectedPlanId) {
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

            Cache::store('file')->forget('register_otp_' . $request->admin_email);

            return redirect()->route('tenant.register.create')->with('success', 'Registration received. Your workspace will be provisioned after admin approval.');
        } catch (Exception $e) {
            // In a production app, we would log this and return a friendlier error
            return back()->withInput()->withErrors([
                'domain' => 'Failed to submit tenant registration request. Please try again or choose a different subdomain. Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Send verification code to the admin email.
     */
    public function sendCode(Request $request)
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::store('file')->put('register_otp_' . $request->email, $code, now()->addMinutes(10));

        Mail::raw("Your verification code is: $code", function ($msg) use ($request) {
            $msg->to($request->email)->subject('Verification Code');
        });

        return response()->json(['success' => true]);
    }
}
