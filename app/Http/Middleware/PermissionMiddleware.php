<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'hasPermission')) {
            abort(403, 'Unauthorized.');
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        if (! $user->hasAnyPermission($permissions)) {
            abort(403, 'User does not have the right permissions.');
        }

        return $next($request);
    }
}
