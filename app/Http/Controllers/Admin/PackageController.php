<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $packages = Package::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $students = User::where('role', 'student')->get();
        return view('admin.packages.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|unique:packages',
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'delivery_date' => 'required|date',
        ]);

        $package = Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $students = User::where('role', 'student')->get();
        return view('admin.packages.edit', compact('package', 'students'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|unique:packages,tracking_number,' . $package->id,
            'student_id' => 'required|exists:users,student_id',
            'notes' => 'nullable|string',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function markAsCollected(Package $package)
    {
        $package->markAsCollected();

        return redirect()->back()
            ->with('success', 'Package marked as collected.');
    }
}
