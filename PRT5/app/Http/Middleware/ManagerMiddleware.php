<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     * Requires user to be logged in and have manager or admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access the admin area.');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Access denied. Manager or Admin privileges required.');
        }

        return $next($request);
    }
}
