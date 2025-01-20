<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Package Management
    Route::resource('packages', PackageController::class);
    Route::post('packages/{package}/mark-collected', [PackageController::class, 'markAsCollected'])->name('packages.mark-collected');
});

// Redirect /home to admin dashboard
Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        // If not admin, logout and redirect with message
        Auth::logout();
        return redirect()->route('login')->with('error', 'Access denied. Admin only system.');
    }
    return redirect()->route('login');
})->name('home');
