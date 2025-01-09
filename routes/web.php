<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Package Management
    Route::resource('packages', PackageController::class);
    Route::post('packages/{package}/mark-collected', [PackageController::class, 'markAsCollected'])
        ->name('packages.mark-collected');
});

// Student Routes
Route::prefix('student')->middleware(['auth'])->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
});

// Redirect /home to the appropriate dashboard based on user role
Route::get('/home', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
})->name('home');

//students
Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
Route::get('/students/create',[App\Http\Controllers\StudentController::class, 'create'])->name('students.create');
Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
Route::get('/students/{student}', [App\Http\Controllers\StudentController::class, 'show'])->name('students.show');
Route::get('/students/{student}/edit', [App\Http\Controllers\StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [App\Http\Controllers\StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{student}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');

//subjects
Route::get('/subjects', [App\Http\Controllers\SubjectController::class, 'index'])->name('subjects.index');
Route::get('/subjects/create',[App\Http\Controllers\SubjectController::class, 'create'])->name('subjects.create');
Route::post('/subjects', [App\Http\Controllers\SubjectController::class, 'store'])->name('subjects.store');
Route::get('/subjects/{subject}', [App\Http\Controllers\SubjectController::class, 'show'])->name('subjects.show');
Route::get('/subjects/{subject}/edit', [App\Http\Controllers\SubjectController::class, 'edit'])->name('subjects.edit');
Route::put('/subjects/{subject}', [App\Http\Controllers\SubjectController::class, 'update'])->name('subjects.update');
Route::delete('/subjects/{subject}', [App\Http\Controllers\SubjectController::class, 'destroy'])->name('subjects.destroy');

//timetable
Route::resource('timetables', App\Http\Controllers\TimetableController::class);
Route::get('/timetables', [App\Http\Controllers\TimetableController::class, 'index'])->name('timetables.index');
Route::get('/timetables/create',[App\Http\Controllers\TimetableController::class, 'create'])->name('timetables.create');
Route::post('/timetables', [App\Http\Controllers\TimetableController::class, 'store'])->name('timetables.store');
Route::get('/timetables/{timetable}', [App\Http\Controllers\TimetableController::class, 'show'])->name('timetables.show');
Route::get('/timetables/{timetable}/edit', [App\Http\Controllers\TimetableController::class, 'edit'])->name('timetables.edit');
Route::put('/timetables/{timetable}', [App\Http\Controllers\TimetableController::class, 'update'])->name('timetables.update');
Route::delete('/timetables/{timetable}', [App\Http\Controllers\TimetableController::class, 'destroy'])->name('timetables.destroy');

//group
Route::get('/groups', [App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/create',[App\Http\Controllers\GroupController::class, 'create'])->name('groups.create');
Route::post('/groups', [App\Http\Controllers\GroupController::class, 'store'])->name('groups.store');
Route::get('/groups/{group}', [App\Http\Controllers\GroupController::class, 'show'])->name('groups.show');
Route::get('/groups/{group}/edit', [App\Http\Controllers\GroupController::class, 'edit'])->name('groups.edit');
Route::put('/groups/{group}', [App\Http\Controllers\GroupController::class, 'update'])->name('groups.update');
Route::delete('/groups/{group}', [App\Http\Controllers\GroupController::class, 'destroy'])->name('groups.destroy');

//halls
Route::get('/halls', [App\Http\Controllers\HallController::class, 'index'])->name('halls.index');
Route::get('/halls/create', [App\Http\Controllers\HallController::class, 'create'])->name('halls.create');
Route::post('/halls', [App\Http\Controllers\HallController::class, 'store'])->name('halls.store');
Route::get('/halls/{hall}', [App\Http\Controllers\HallController::class, 'show'])->name('halls.show');
Route::get('/halls/{hall}/edit', [App\Http\Controllers\HallController::class, 'edit'])->name('halls.edit');
Route::put('/halls/{hall}', [App\Http\Controllers\HallController::class, 'update'])->name('halls.update');
Route::delete('/halls/{hall}', [App\Http\Controllers\HallController::class, 'destroy'])->name('halls.destroy'); // Ensure this line is present

//days
// Route::resource('days', App\Http\Controllers\DayController::class);
Route::get('/days', [App\Http\Controllers\DayController::class, 'index'])->name('days.index'); // Display a listing of the days
Route::get('/days/create', [App\Http\Controllers\DayController::class, 'create'])->name('days.create'); // Show the form for creating a new day
Route::post('/days', [App\Http\Controllers\DayController::class, 'store'])->name('days.store'); // Store a newly created day
Route::get('/days/{day}', [App\Http\Controllers\DayController::class, 'show'])->name('days.show'); // Display the specified day
Route::get('/days/{day}/edit', [App\Http\Controllers\DayController::class, 'edit'])->name('days.edit'); // Show the form for editing the specified day
Route::put('/days/{day}', [App\Http\Controllers\DayController::class, 'update'])->name('days.update'); // Update the specified day
Route::put('/days/{day}/edit', [App\Http\Controllers\DayController::class, 'update'])->name('days.update'); // Update the specified day
Route::delete('/days/{day}', [App\Http\Controllers\DayController::class, 'destroy'])->name('days.destroy'); // Remove the specified day