<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Show login form.
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if (Session::has('admin_user')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            // Call API to authenticate
            $response = $this->api->login($request->email, $request->password);

            if (isset($response['success']) && $response['success']) {
                $user = $response['data']['user'] ?? $response['user'] ?? null;
                $token = $response['data']['token'] ?? $response['token'] ?? null;

                // Check if user is admin
                $role = $user['role'] ?? 'customer';
                if (!in_array($role, ['admin', 'administrator', 'manager'])) {
                    return back()->withErrors(['email' => 'You do not have permission to access the admin panel.'])->withInput();
                }

                // Store user in session
                Session::put('admin_user', $user);
                Session::put('admin_token', $token);
                Session::put('admin_logged_in', true);

                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . ($user['first_name'] ?? 'Admin') . '!');
            }

            return back()->withErrors(['email' => $response['message'] ?? 'Invalid credentials'])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Login failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Session::forget('admin_user');
        Session::forget('admin_token');
        Session::forget('admin_logged_in');

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
