<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->is_super_admin) {
            // If they are on the admin domain but not a super admin, abort
            abort(403, 'Unauthorized access to the Super Admin portal.');
        }

        return $next($request);
    }
}
