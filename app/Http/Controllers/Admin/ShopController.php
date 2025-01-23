<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index() {
        $shops = Shop::all();
        return view('admin.shops.index', compact('shops'));
    }

    public function create() {
        return view('admin.shops.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        Shop::create($validated);

        return redirect()->route('admin.shops.index')->with('success', 'Shop created successfully');
    }

    public function edit(Shop $shop) {
        return view('admin.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $shop->update($validated);

        return redirect()->route('admin.shops.index')->with('success', 'Shop updated successfully');
    }

    public function destroy(Shop $shop) {
        $shop->delete();
        return redirect()->route('admin.shops.index')->with('success', 'Shop deleted successfully');
    }
} 