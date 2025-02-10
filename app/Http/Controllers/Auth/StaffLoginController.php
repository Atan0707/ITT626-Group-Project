<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('staff')->attempt([
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => 1
        ], $request->remember)) {
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withInput($request->only('username', 'remember'))
            ->withErrors(['username' => 'These credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login');
    }
} 