<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request — verify the authenticated user has the required role.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // If user is NOT logged in OR their role doesn't match → block with 403
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized. You do not have the required role.');
        }

        return $next($request);  // Role matches, let them through
    }
}
