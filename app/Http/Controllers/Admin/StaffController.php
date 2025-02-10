<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('shop')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $shops = Shop::all();
        return view('admin.staff.create', compact('shops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:staff',
            'email' => 'required|email|unique:staff',
            'password' => 'required|min:6',
            'phone_number' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        Staff::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully');
    }

    public function edit(Staff $staff)
    {
        $shops = Shop::all();
        return view('admin.staff.edit', compact('staff', 'shops'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:staff,username,' . $staff->id,
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone_number' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'is_active' => 'boolean'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_active'] = $request->has('is_active');

        $staff->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully');
    }
}
