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

    public function index(Request $request)
    {
        $query = Package::orderBy('created_at', 'desc');

        // If date is provided, filter packages for that date
        if ($request->has('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        $packages = $query->paginate(10);

        // If date is provided, add it to the view data
        $filterDate = $request->date ? \Carbon\Carbon::parse($request->date)->format('d M Y') : null;

        return view('admin.packages.index', compact('packages', 'filterDate'));
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

    public function calendar(Request $request)
    {
        try {
            $query = Package::selectRaw('delivery_date, COUNT(*) as count')
                ->whereNotNull('delivery_date');

            // If month is selected, filter by that month
            if ($request->has('month')) {
                $date = \Carbon\Carbon::parse($request->month);
                $query->whereYear('delivery_date', $date->year)
                      ->whereMonth('delivery_date', $date->month);
            }

            $dates = $query->groupBy('delivery_date')
                          ->orderBy('delivery_date', 'desc')
                          ->get();

            // Get list of months with packages for the dropdown
            $months = Package::selectRaw('DATE_FORMAT(delivery_date, "%Y-%m") as month')
                ->whereNotNull('delivery_date')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function($item) {
                    $date = \Carbon\Carbon::parse($item->month);
                    return [
                        'value' => $date->format('Y-m'),
                        'label' => $date->format('F Y')
                    ];
                });

            $selectedMonth = $request->month;

            return view('admin.packages.calendar', compact('dates', 'months', 'selectedMonth'));
        } catch (\Exception $e) {
            return view('admin.packages.calendar', [
                'dates' => collect([]),
                'months' => collect([]),
                'selectedMonth' => null
            ]);
        }
    }

    public function calendarEvents()
    {
        return response()->json([]);
    }
}
