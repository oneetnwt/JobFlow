<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantActiveMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant && $tenant->status !== 'active') {
            abort(403, 'This workspace is currently pending approval or has been suspended.');
        }

        return $next($request);
    }
}
