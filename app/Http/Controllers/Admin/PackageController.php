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
        $query = Package::query();

        // If date is provided, filter packages for that date
        if ($request->has('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        // Get all packages sorted by delivery date
        $packages = $query->orderBy('delivery_date', 'desc')
                         ->get()
                         ->groupBy(function($package) {
                             return $package->delivery_date->format('Y-m-d');
                         });

        // Process each day's packages
        $processedPackages = collect();
        foreach ($packages as $date => $dayPackages) {
            // Add date header
            $processedPackages->push((object)[
                'is_date_header' => true,
                'date' => $date
            ]);

            // Add packages with daily numbers
            $counter = 1;
            foreach ($dayPackages as $package) {
                $package->dailyNumber = $counter++;
                $processedPackages->push($package);
            }
        }

        // Calculate pagination
        $perPage = 15; // Increased to account for date headers
        $page = request()->get('page', 1);
        
        // Get the slice of items for the current page
        $items = $processedPackages->forPage($page, $perPage);
        
        // Count actual packages (excluding headers) in the current page
        $actualPackagesInPage = $items->filter(function($item) {
            return !isset($item->is_date_header);
        })->count();
        
        // If we have less than 10 actual packages and there are more items available,
        // increase the per page count to try to get more packages
        while ($actualPackagesInPage < 10 && $items->count() < $processedPackages->count()) {
            $perPage += 5;
            $items = $processedPackages->forPage($page, $perPage);
            $actualPackagesInPage = $items->filter(function($item) {
                return !isset($item->is_date_header);
            })->count();
            
            // Safety check to prevent infinite loop
            if ($perPage > 50) break;
        }

        // Create paginator with adjusted items
        $packages = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $processedPackages->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

        // Get count for the delivery date
        $deliveryDate = \Carbon\Carbon::parse($validated['delivery_date'])->format('Y-m-d');
        $dayCount = Package::whereDate('delivery_date', $deliveryDate)->count();
        $dailyNumber = $dayCount + 1;

        $package = Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package added. Please label the parcel by #' . $dailyNumber)
            ->with('dailyNumber', $dailyNumber);
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|unique:packages,tracking_number,' . $package->id,
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'delivery_date' => 'required|date',
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

            // Set default month to current month
            $selectedMonth = $request->month ?? now()->format('Y-m');
            $date = \Carbon\Carbon::parse($selectedMonth);
            $query->whereYear('delivery_date', $date->year)
                  ->whereMonth('delivery_date', $date->month);

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
