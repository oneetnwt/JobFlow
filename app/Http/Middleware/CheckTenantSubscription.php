<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        // If no tenant is initialized, allow request to proceed (e.g. for central routes)
        if (! $tenant) {
            return $next($request);
        }

        // Allow exempt routes
        $exemptRoutes = [
            'tenant.login',
            'tenant.logout',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'tenant.billing.index',
            'tenant.billing.checkout',
            'tenant.billing.success',
            'tenant.billing.failed',
        ];

        if (Route::currentRouteNamed(...$exemptRoutes)) {
            return $next($request);
        }

        // Fetch the tenant's current subscription
        $subscription = $tenant->subscription;

        // If there's no subscription logic set up (e.g., legacy tenant), let them pass for backwards compatibility.
        // Or if you prefer to redirect them to subscribe, you can do so. For this flow, we will let them pass
        // if they don't have a subscription yet, but realistically new tenants will have one.
        if (! $subscription) {
            return $next($request);
        }

        $status = $subscription->status;
        $now = now();

        switch ($status) {
            case 'trialing':
                // trial expired -> redirect to subscription page
                if ($subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
                    return redirect()->route('tenant.billing.index')->with('error', 'Your trial has expired. Please subscribe to continue using the workspace.');
                }
                break;

            case 'active':
                // period expired without renewal -> redirect to subscription page
                if ($subscription->current_period_end && $subscription->current_period_end->isPast()) {
                    return redirect()->route('tenant.billing.index')->with('error', 'Your subscription has expired. Please renew to continue.');
                }
                break;

            case 'past_due':
                // Allow GET requests (view-only), block POST/PUT/PATCH/DELETE
                if (! $request->isMethod('GET')) {
                    abort(403, 'Your subscription is past due. You only have view access. Please update your billing information.');
                }
                break;

            case 'cancelled':
            case 'expired':
                return redirect()->route('tenant.billing.index')->with('error', 'Your subscription is cancelled or expired. Please reactivate your account.');
                break;
        }

        return $next($request);
    }
}
