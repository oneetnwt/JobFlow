<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SubscriptionInvoice;
use App\Services\PayMongo\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Show the billing and subscription page.
     */
    public function index()
    {
        $tenant = tenant();
        $subscription = $tenant->subscription;

        $plans = Plan::active()->where('slug', '!=', 'enterprise')->orderBy('sort_order')->get();
        // You could fetch invoices here as well
        $invoices = SubscriptionInvoice::where('tenant_id', $tenant->id)->latest()->get();

        return view('tenant.billing.index', compact('tenant', 'subscription', 'plans', 'invoices'));
    }

    /**
     * Fake checkout method (PayMongo to be handled in Phase 6).
     */
    public function checkout(Request $request, PayMongoService $payMongo)
    {
        $tenant = tenant();
        $planId = $request->input('plan_id');
        $plan = Plan::findOrFail($planId);

        try {
            $checkout = $payMongo->createCheckoutSession([
                'cancel_url' => route('tenant.billing.index'),
                'success_url' => route('tenant.billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'line_items' => [
                    [
                        'name' => $plan->name.' Plan',
                        'amount' => $plan->price * 100, // Amount in cents
                        'currency' => 'PHP',
                        'quantity' => 1,
                        'description' => $plan->description ?? 'Subscription plan',
                    ],
                ],
                'payment_method_types' => ['card', 'paymaya', 'gcash', 'grab_pay'],
                'reference_number' => $tenant->id.'_'.$plan->id.'_'.time(),
            ]);

            return redirect($checkout['data']['attributes']['checkout_url']);
        } catch (\Exception $e) {
            Log::error('Checkout session creation failed.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Checkout unavailable right now. Try again later.');
        }
    }

    /**
     * Show the success page after payment.
     */
    public function success(Request $request)
    {
        // Mock success marking for now
        $tenant = tenant();
        $subscription = $tenant->subscription;

        $sessionId = $request->query('session_id', 'mock_sess_'.uniqid());

        // Update status if it was trialing/past_due
        if ($subscription->status !== 'active') {
            $subscription->update([
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Generate a mock invoice
            SubscriptionInvoice::create([
                'tenant_id' => $tenant->id,
                'tenant_subscription_id' => $subscription->id,
                'amount' => $subscription->plan->price ?? 0,
                'status' => 'paid',
                'invoice_url' => '#',
                'payment_intent_id' => 'pi_'.time(),
                'paid_at' => now(),
            ]);
        }

        return view('tenant.billing.success', compact('sessionId'));
    }
}
