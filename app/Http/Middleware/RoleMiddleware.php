<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'hasAnyRole')) {
            abort(403, 'Unauthorized.');
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        $hasRole = false;
        foreach ($roles as $r) {
            if ($user->hasRole($r)) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            abort(403, 'User does not have the right roles.');
        }

        return $next($request);
    }
}
