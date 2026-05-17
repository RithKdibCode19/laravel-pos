<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the authenticated user is an admin
        // Assuming admin users have an 'is_admin' attribute set to true
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.'); // Or redirect to a different page
        }

        return $next($request);
    }
}
