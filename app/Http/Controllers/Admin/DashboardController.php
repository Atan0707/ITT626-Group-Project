<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'total_packages' => Package::count(),
            'pending_packages' => Package::where('status', 'pending')->count(),
            'collected_packages' => Package::where('status', 'collected')->count(),
            'total_students' => User::where('role', 'student')->count(),
        ];

        $recent_packages = Package::with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_packages'));
    }
}
