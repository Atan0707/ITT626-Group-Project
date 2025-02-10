<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Packages
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
    Route::get('/packages/calendar', [PackageController::class, 'calendar'])->name('packages.calendar');
    Route::get('/packages/calendar/events', [PackageController::class, 'calendarEvents'])->name('packages.calendar.events');
    Route::post('/packages/{package}/mark-collected', [PackageController::class, 'markCollected'])->name('packages.mark-collected');
    Route::get('/packages/bulk-create', [PackageController::class, 'bulkCreate'])->name('packages.bulk-create');
    Route::post('/packages/bulk-store', [PackageController::class, 'bulkStore'])->name('packages.bulk-store');
    Route::get('/packages/print/{date}', [PackageController::class, 'printView'])->name('packages.print');
    Route::post('/packages/{package}/send-reminder', [PackageController::class, 'sendReminder'])->name('packages.send-reminder');
    
    // Shops
    Route::resource('shops', ShopController::class);

    // Staff Management Routes (explicit)
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
});

// Staff Auth Routes
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StaffLoginController::class, 'login']);
    Route::post('/logout', [StaffLoginController::class, 'logout'])->name('logout');
});

// Staff Protected Routes
Route::prefix('staff')->middleware('auth:staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    // Add other staff routes here
});

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
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