<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function index(Request $request)
    {
        // Mark discarded packages before displaying
        Package::markDiscardedPackages();
        
        $query = Package::query();

        // Get only packages for the staff's shop
        $query->where('shop_id', Auth::guard('staff')->user()->shop_id);

        // If filter_date is provided, get all packages for that date
        if ($request->has('filter_date')) {
            $query->whereDate('delivery_date', $request->filter_date);
        }

        // Order by delivery date and daily number
        $query->orderBy('delivery_date', 'desc')
              ->orderBy('daily_number', 'asc');

        // Get the packages with pagination
        $packages = $query->paginate(10)->withQueryString();

        return view('staff.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('staff.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'tracking_number' => 'required|string|unique:packages',
            'delivery_date' => 'required|date',
        ]);

        try {
            $package = DB::transaction(function () use ($validated, $request) {
                // Get the last daily number for the delivery date
                $lastDailyNumber = Package::whereDate('delivery_date', $validated['delivery_date'])
                    ->max('daily_number');

                // Get the staff's shop_id
                $shop_id = Auth::guard('staff')->user()->shop_id;

                // Create the package
                $package = Package::create([
                    'name' => $validated['name'],
                    'phone_number' => $validated['phone_number'],
                    'tracking_number' => $validated['tracking_number'],
                    'delivery_date' => $validated['delivery_date'],
                    'daily_number' => ($lastDailyNumber ?? 0) + 1,
                    'status' => 'pending',
                    'shop_id' => $shop_id
                ]);

                return $package;
            });

            // Send Telegram notification outside the transaction
            try {
                $this->telegramService->sendPackageNotification($package);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification: ' . $e->getMessage());
                // Don't throw the error, just log it
            }

            return redirect()->route('staff.packages.index')
                ->with('success', 'Package created successfully.')
                ->with('dailyNumber', $package->daily_number);

        } catch (\Exception $e) {
            Log::error('Error creating package: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create package. ' . $e->getMessage()]);
        }
    }

    public function edit(Package $package)
    {
        return view('staff.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'tracking_number' => 'required|string|unique:packages,tracking_number,' . $package->id,
            'delivery_date' => 'required|date',
        ]);

        try {
            $package->update($validated);
            return redirect()->route('staff.packages.index')
                ->with('success', 'Package updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating package: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update package.']);
        }
    }

    public function destroy(Package $package)
    {
        try {
            $package->delete();
            return redirect()->route('staff.packages.index')
                ->with('success', 'Package deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting package: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete package.']);
        }
    }

    public function calendar(Request $request)
    {
        $query = Package::query()
            ->where('shop_id', Auth::guard('staff')->user()->shop_id)
            ->select('delivery_date', DB::raw('count(*) as count'))
            ->groupBy('delivery_date');

        // If month is selected, filter by that month
        if ($request->has('month')) {
            $month = $request->month;
            $query->whereYear('delivery_date', substr($month, 0, 4))
                  ->whereMonth('delivery_date', substr($month, 5, 2));
        }

        $dates = $query->orderBy('delivery_date', 'desc')->get();

        return view('staff.packages.calendar', compact('dates'));
    }

    public function calendarEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $packages = Package::whereBetween('delivery_date', [$start, $end])
            ->get()
            ->map(function ($package) {
                return [
                    'id' => $package->id,
                    'title' => $package->name . ' (' . $package->tracking_number . ')',
                    'start' => $package->delivery_date->format('Y-m-d'),
                    'url' => route('staff.packages.edit', $package),
                    'backgroundColor' => $this->getStatusColor($package->status),
                ];
            });

        return response()->json($packages);
    }

    protected function getStatusColor($status)
    {
        return [
            'pending' => '#ffc107',    // warning
            'collected' => '#28a745',  // success
            'discarded' => '#dc3545',  // danger
        ][$status] ?? '#6c757d';       // secondary (default)
    }

    public function markCollected(Package $package)
    {
        if ($package->status !== 'pending') {
            return back()->with('error', 'Package is not in pending status');
        }

        $package->update([
            'status' => 'collected',
            'collected_at' => now()
        ]);

        return back()->with('success', 'Package marked as collected successfully');
    }

    public function bulkCreate()
    {
        return view('staff.packages.bulk-create');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'packages' => 'required|array',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.phone_number' => 'required|string',
            'packages.*.tracking_number' => 'required|string|unique:packages,tracking_number',
            'packages.*.delivery_date' => 'required|date',
        ]);

        $errors = [];
        $successCount = 0;
        $dailyNumbers = [];

        DB::transaction(function () use ($request, &$errors, &$successCount, &$dailyNumbers) {
            foreach ($request->packages as $index => $packageData) {
                try {
                    // Get the last daily number for the delivery date
                    $lastDailyNumber = isset($dailyNumbers[$packageData['delivery_date']]) 
                        ? $dailyNumbers[$packageData['delivery_date']]
                        : Package::whereDate('delivery_date', $packageData['delivery_date'])->max('daily_number');

                    $dailyNumber = ($lastDailyNumber ?? 0) + 1;
                    $dailyNumbers[$packageData['delivery_date']] = $dailyNumber;

                    $package = Package::create([
                        'name' => $packageData['name'],
                        'phone_number' => $packageData['phone_number'],
                        'tracking_number' => $packageData['tracking_number'],
                        'delivery_date' => $packageData['delivery_date'],
                        'daily_number' => $dailyNumber,
                        'status' => 'pending'
                    ]);

                    // Send Telegram notification
                    $this->telegramService->sendPackageNotification($package);

                    $successCount++;
                    $dailyNumbers[] = $dailyNumber;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }
        });

        if (count($errors) > 0) {
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('staff.packages.index')
            ->with('success', "Successfully added {$successCount} packages.");
    }

    public function printView($date)
    {
        $packages = Package::whereDate('delivery_date', $date)
            ->orderBy('daily_number')
            ->get();

        return view('staff.packages.print', compact('packages', 'date'));
    }

    public function sendReminder(Package $package)
    {
        if ($package->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only send reminders for pending packages'
            ]);
        }

        $success = $this->telegramService->sendReminderNotification($package);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Reminder sent successfully' : 'Failed to send reminder'
        ]);
    }
} 