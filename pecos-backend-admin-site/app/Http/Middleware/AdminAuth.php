<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dev token bypass for local development
        if ($request->get('token') === 'local-dev-token') {
            Session::put('admin_logged_in', true);
            Session::put('admin_user', [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@pecosriver.com',
                'role' => 'admin'
            ]);
        }

        if (!Session::has('admin_logged_in') || !Session::get('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login to access the admin panel.');
        }

        // Optionally check if user still has admin role
        $user = Session::get('admin_user');
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'administrator', 'manager'])) {
            Session::forget('admin_user');
            Session::forget('admin_token');
            Session::forget('admin_logged_in');
            return redirect()->route('login')->withErrors(['email' => 'Your session has expired or you no longer have admin access.']);
        }

        return $next($request);
    }
}
