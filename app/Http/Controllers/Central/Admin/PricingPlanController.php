<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PricingPlanController extends Controller
{
    /**
     * Display a listing of pricing plans.
     */
    public function index(): View
    {
        $plans = Plan::withCount(['tenants' => function($q) {
            $q->where('status', 'active');
        }])->orderBy('sort_order')->withTrashed()->get();

        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Store a new plan.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);
        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['name']);
        if (!$validated['slug']) {
             $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['currency'] = 'PHP'; // default currency

        Plan::create($validated);

        return back()->with('success', 'Pricing plan created successfully.');
    }

    /**
     * Update a plan.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $this->validatePlan($request, $plan->id);
        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['name']);
        if (!$validated['slug']) {
             $validated['slug'] = Str::slug($validated['name']);
        }
        
        $plan->update($validated);

        return back()->with('success', 'Pricing plan updated successfully.');
    }

    /**
     * Retire/Archive a plan.
     */
    public function destroy(Plan $plan)
    {
        if ($plan->tenants()->where('status', 'active')->count() > 0) {
            // If active tenants exist, we archive (soft delete) instead of hard delete
            $plan->update(['status' => 'archived']);
            $plan->delete();
            return back()->with('success', 'Plan has been archived and hidden from new signups. Existing tenants are unaffected.');
        }

        $plan->forceDelete();
        return back()->with('success', 'Pricing plan deleted permanently.');    
    }

    protected function validatePlan(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:plans,slug,' . $ignoreId],
            'tagline' => ['nullable', 'string', 'max:500'],
            'is_free' => ['boolean'],
            'is_contact_sales' => ['boolean'],
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'annual_price' => ['nullable', 'numeric', 'min:0'],
            'max_workers' => ['nullable', 'integer', 'min:1'],
            'max_job_orders' => ['nullable', 'integer', 'min:1'],
            'has_payroll' => ['boolean'],
            'has_priority_support' => ['boolean'],
            'has_custom_integrations' => ['boolean'],
            'auto_approve' => ['boolean'],
            'features' => ['nullable', 'array'],
            'badge_label' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:draft,active,archived'],     
            'sort_order' => ['nullable', 'integer'],
        ]);
    }
}
