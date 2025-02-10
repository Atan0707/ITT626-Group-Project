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
        // Add debug logging
        \Log::info('Staff login attempt started');

        // Temporarily bypass authentication and directly login as staff
        $staff = \App\Models\Staff::first(); // Get the first staff member
        
        \Log::info('Staff lookup result', [
            'found' => $staff ? 'yes' : 'no',
            'staff_details' => $staff ? $staff->toArray() : null
        ]);

        if ($staff) {
            Auth::guard('staff')->login($staff);
            \Log::info('Staff login successful', ['staff_id' => $staff->id]);
            return redirect()->route('staff.dashboard');
        }

        \Log::error('No staff members found in database');
        return back()->with('error', 'No staff members found in database. Please create a staff member first.');
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login');
    }
} 