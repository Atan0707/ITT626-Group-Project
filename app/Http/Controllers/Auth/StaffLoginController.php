<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'name' => 'required|string',
            'password' => 'required|min:6'
        ]);

        // Check if staff exists and is active
        $staff = Staff::where('name', $request->name)
                     ->where('is_active', true)
                     ->first();

        if (!$staff) {
            return back()
                ->withInput($request->only('name'))
                ->withErrors(['name' => 'Staff member not found or inactive.']);
        }

        // Attempt to log the staff in
        if (Auth::guard('staff')->attempt([
            'name' => $request->name,
            'password' => $request->password,
            'is_active' => 1
        ])) {
            // If successful, redirect to staff dashboard
            return redirect()->intended(route('staff.dashboard'));
        }

        // If unsuccessful, redirect back with input
        return back()
            ->withInput($request->only('name'))
            ->withErrors(['name' => 'These credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login');
    }
} 