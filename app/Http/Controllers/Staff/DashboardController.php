<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_packages' => Package::count(),
            'pending_packages' => Package::where('status', 'pending')->count(),
            'collected_packages' => Package::where('status', 'collected')->count(),
            'discarded_packages' => Package::where('status', 'discarded')->count(),
        ];

        $recent_packages = Package::latest()
            ->take(10)
            ->get();

        return view('staff.dashboard', compact('stats', 'recent_packages'));
    }
} 