<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
        $role = request()->input('role');
        return $role === 'admin' ? 'email' : 'student_id';
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only($this->username(), 'password');
        $role = $request->input('role');

        // For admin login
        if ($role === 'admin') {
            if ($credentials['email'] === 'admin' && $credentials['password'] === 'root') {
                $admin = \App\Models\User::where('role', 'admin')->first();
                if ($admin) {
                    Auth::login($admin);
                    return $this->sendLoginResponse($request);
                }
            }
            return $this->sendFailedLoginResponse($request);
        }

        // For student login
        if ($role === 'student') {
            $credentials['role'] = 'student';
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
            return $this->sendFailedLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
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
        $credentials = $request->only($this->username(), 'password');
        if ($request->input('role') === 'student') {
            $credentials['role'] = 'student';
        }
        return $credentials;
    }

    /**
     * Get the post register / login redirect path.
     */
    public function redirectPath()
    {
        if (Auth::user()->role === 'admin') {
            return '/admin/dashboard';
        }
        return '/student/dashboard';
    }
}
