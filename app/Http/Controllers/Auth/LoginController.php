<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Add this line for debugging
            \Log::info('User authenticated successfully', ['user' => Auth::user()]);
            
            return redirect()->intended('/admin/dashboard');
        }

        // Add this line for debugging
        \Log::info('Login failed', ['username' => $request->username]);

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $role = $request->input('role');
        
        if ($role === 'admin') {
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
                'role' => 'required|in:admin',
            ]);
        } else {
            $request->validate([
                'student_id' => 'required|string',
                'password' => 'required|string',
                'role' => 'required|in:student',
            ]);
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the post register / login redirect path.
     */
    protected function redirectTo()
    {
        return '/admin/dashboard';
    }

    // Override the authenticated method to redirect admin
    protected function authenticated(Request $request, $user)
    {
        // For admin users
        if ($request->input('login_type') === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // For staff users
        if ($request->input('login_type') === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        // Fallback
        return redirect('/admin/dashboard');
    }

    // Add logout method
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    protected function loggedOut(Request $request)
    {
        return redirect('/login');
    }
}
