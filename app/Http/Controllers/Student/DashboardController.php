<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $packages = $user->packages()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_packages' => $user->packages()->count(),
            'pending_packages' => $user->packages()->where('status', 'pending')->count(),
            'collected_packages' => $user->packages()->where('status', 'collected')->count(),
        ];

        return view('student.dashboard', compact('packages', 'stats'));
    }
}
