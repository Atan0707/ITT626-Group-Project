<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Shop;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('shop')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $shops = Shop::where('is_active', true)->get();
        return view('admin.staff.create', compact('shops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff',
            'phone_number' => 'nullable|string|max:20',
            'shop_id' => 'required|exists:shops,id',
            'is_active' => 'boolean'
        ]);

        Staff::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member added successfully');
    }

    public function edit(Staff $staff)
    {
        $shops = Shop::where('is_active', true)->get();
        return view('admin.staff.edit', compact('staff', 'shops'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone_number' => 'nullable|string|max:20',
            'shop_id' => 'required|exists:shops,id',
            'is_active' => 'boolean'
        ]);

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
