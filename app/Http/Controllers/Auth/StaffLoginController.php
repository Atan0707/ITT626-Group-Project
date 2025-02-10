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
        // Add debug logging
        \Log::info('Staff login attempt started', ['name' => $request->name]);

        // Validate the form data
        $this->validate($request, [
            'name' => 'required|string',
            'password' => 'required'
        ]);

        // Check if staff exists and is active
        $staff = Staff::where('name', $request->name)
                     ->where('is_active', true)
                     ->first();

        \Log::info('Staff lookup result', [
            'name' => $request->name,
            'found' => $staff ? 'yes' : 'no',
            'active' => $staff ? $staff->is_active : 'n/a'
        ]);

        if (!$staff) {
            return back()
                ->withInput($request->only('name'))
                ->withErrors(['name' => 'Staff member not found or inactive.']);
        }

        // Attempt to log the staff in
        $credentials = [
            'name' => $request->name,
            'password' => $request->password,
            'is_active' => 1
        ];

        \Log::info('Attempting staff login with credentials', ['name' => $credentials['name']]);

        if (Auth::guard('staff')->attempt($credentials)) {
            \Log::info('Staff login successful', ['staff_id' => $staff->id]);
            return redirect()->route('staff.dashboard');
        }

        \Log::error('Staff login failed', [
            'name' => $request->name
        ]);

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