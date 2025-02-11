<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;

class StaffLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:staff')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.staff-login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // Attempt to log the staff member in
        if (Auth::guard('staff')->attempt([
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => true
        ], $request->remember)) {
            // If successful, redirect to their intended location
            return redirect()->intended(route('staff.dashboard'));
        }

        // If unsuccessful, redirect back with input
        return back()
            ->withInput($request->only('username', 'remember'))
            ->with('error', 'Invalid login credentials or account is inactive');
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }
} 