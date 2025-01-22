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

        $packages = $query->get();

        // Group packages by created_at date and assign daily numbers
        $packages = $packages->map(function($package) {
            $createdDate = $package->created_at->format('Y-m-d');
            $dailyNumber = Package::whereDate('created_at', $createdDate)
                ->where('created_at', '<=', $package->created_at)
                ->count();
            $package->dailyNumber = $dailyNumber;
            return $package;
        });

        // Paginate after processing
        $perPage = 10;
        $page = request()->get('page', 1);
        $packages = new \Illuminate\Pagination\LengthAwarePaginator(
            $packages->forPage($page, $perPage),
            $packages->count(),
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

        // Get today's count for sorting number
        $today = now()->format('Y-m-d');
        $todayCount = Package::whereDate('created_at', $today)->count();
        $dailyNumber = $todayCount + 1;

        $package = Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package added. Please label the parcel by #' . $dailyNumber)
            ->with('dailyNumber', $dailyNumber);
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
