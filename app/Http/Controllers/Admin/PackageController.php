<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->telegramService = $telegramService;
    }

    public function index(Request $request)
    {
        $query = Package::query();

        // If date is provided, filter packages for that date
        if ($request->has('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        // Get all packages sorted by delivery date and daily number
        $packages = $query->orderBy('delivery_date', 'desc')
                         ->orderBy('daily_number', 'asc')
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

            // Add packages (daily number is now from database)
            foreach ($dayPackages as $package) {
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

        // Get next daily number for the delivery date
        $deliveryDate = \Carbon\Carbon::parse($validated['delivery_date'])->format('Y-m-d');
        $maxDailyNumber = Package::whereDate('delivery_date', $deliveryDate)
            ->max('daily_number') ?? 0;
        $dailyNumber = $maxDailyNumber + 1;

        // Add daily number to validated data
        $validated['daily_number'] = $dailyNumber;

        $package = Package::create($validated);

        // Send Telegram notification
        try {
            $this->telegramService->sendPackageNotification($package);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            // Continue execution even if notification fails
        }

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

    public function bulkCreate()
    {
        return view('admin.packages.bulk-create');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'packages' => 'required|array|min:1',
            'packages.*.tracking_number' => 'required|string|distinct|unique:packages,tracking_number',
            'packages.*.name' => 'required|string',
            'packages.*.phone_number' => 'required|string',
            'packages.*.delivery_date' => 'required|date',
        ]);

        $successCount = 0;
        $errors = [];

        \DB::transaction(function () use ($request, &$successCount, &$errors) {
            foreach ($request->packages as $index => $packageData) {
                try {
                    // Get next daily number for the delivery date
                    $deliveryDate = \Carbon\Carbon::parse($packageData['delivery_date'])->format('Y-m-d');
                    $maxDailyNumber = Package::whereDate('delivery_date', $deliveryDate)
                        ->max('daily_number') ?? 0;
                    $dailyNumber = $maxDailyNumber + 1;

                    // Create package
                    $package = Package::create([
                        'tracking_number' => $packageData['tracking_number'],
                        'name' => $packageData['name'],
                        'phone_number' => $packageData['phone_number'],
                        'delivery_date' => $packageData['delivery_date'],
                        'daily_number' => $dailyNumber
                    ]);

                    // Send Telegram notification
                    try {
                        $this->telegramService->sendPackageNotification($package);
                    } catch (\Exception $e) {
                        Log::error('Failed to send Telegram notification for package #' . $dailyNumber . ': ' . $e->getMessage());
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error processing package " . ($index + 1) . ": " . $e->getMessage();
                }
            }
        });

        if ($successCount > 0) {
            $message = $successCount . ' package(s) added successfully.';
            if (!empty($errors)) {
                $message .= ' However, there were some errors: ' . implode(', ', $errors);
                return redirect()->route('admin.packages.bulk-create')
                    ->with('warning', $message);
            }
            return redirect()->route('admin.packages.index')
                ->with('success', $message);
        }

        return redirect()->route('admin.packages.bulk-create')
            ->with('error', 'Failed to add packages. ' . implode(', ', $errors));
    }
}
