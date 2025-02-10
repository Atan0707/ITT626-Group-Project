<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mark discarded packages before getting stats
        Package::markDiscardedPackages();

        // Get statistics
        $stats = [
            'total_packages' => Package::count(),
            'pending_packages' => Package::where('status', 'pending')->count(),
            'collected_packages' => Package::where('status', 'collected')->count(),
            'discarded_packages' => Package::where('status', 'discarded')->count(),
        ];

        // Get recent packages
        $recent_packages = Package::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('staff.dashboard', compact('stats', 'recent_packages'));
    }
} 