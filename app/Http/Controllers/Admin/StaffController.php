<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function index()
    {
        try {
            // Debug log to check if method is being called
            \Log::info('Fetching staff members');

            // Get all staff members with their related shop information
            $staffMembers = Staff::with('shop')->get();

            // Debug log to check what was retrieved
            \Log::info('Staff members retrieved', [
                'count' => $staffMembers->count(),
                'data' => $staffMembers->toArray()
            ]);

            // Let's also check the database directly
            $rawCount = DB::table('staff')->count();
            \Log::info('Raw staff count from database', ['count' => $rawCount]);

            return view('admin.staff.index', compact('staffMembers'));

        } catch (\Exception $e) {
            Log::error('Error fetching staff members', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Failed to retrieve staff members: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $shops = Shop::all();
        return view('admin.staff.create', compact('shops'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:staff',
                'email' => 'required|email|unique:staff',
                'password' => 'required|min:4',
                'phone_number' => 'nullable|string',
                'shop_id' => 'required|exists:shops,id',
            ]);

            \Log::info('Creating staff member', ['name' => $validated['name']]);

            $staff = Staff::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone_number' => $validated['phone_number'],
                'shop_id' => $validated['shop_id'],
                'is_active' => $request->has('is_active')
            ]);

            \Log::info('Staff member created', ['staff_id' => $staff->id]);

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member created successfully');

        } catch (\Exception $e) {
            Log::error('Failed to create staff member', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create staff member: ' . $e->getMessage()]);
        }
    }

    public function edit(Staff $staff)
    {
        $shops = Shop::all();
        return view('admin.staff.edit', compact('staff', 'shops'));
    }

    public function update(Request $request, Staff $staff)
    {
        // Validate all fields except password
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:staff,username,' . $staff->id,
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone_number' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'is_active' => 'boolean',
            'password' => 'nullable|min:4' // Password is optional but must be at least 4 chars if provided
        ]);

        try {
            // Remove password from validated data if it's empty
            if (empty($validated['password'])) {
                unset($validated['password']);
            } else {
                // Hash the new password if one was provided
                $validated['password'] = Hash::make($validated['password']);
            }

            $validated['is_active'] = $request->has('is_active');

            // Update the staff member
            $staff->update($validated);

            Log::info('Staff member updated successfully', ['staff_id' => $staff->id]);

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update staff member', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update staff member: ' . $e->getMessage()]);
        }
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully');
    }
}
