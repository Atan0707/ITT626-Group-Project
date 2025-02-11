<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;

class DefaultStaffSeeder extends Seeder
{
    public function run()
    {
        // First, make sure we have at least one shop
        $shop = Shop::first();
        if (!$shop) {
            $shop = Shop::create([
                'name' => 'Tanjung UiTM Kampus Jasin',
                'address' => 'UiTM Melaka Kampus Jasin, Jalan Lembah Kesang 1/1-2, Kampung Seri Mendapat, Merlimau, Malacca, Malaysia',
                'latitude' => 2.2634771,  // UiTM Jasin's coordinates
                'longitude' => 102.4510822,
                'phone' => '0123456789',
                'email' => 'tanjung@uitm.edu.my',
                'description' => 'UiTM Kampus Jasin Branch',
                'is_active' => true
            ]);
        }

        // Create default staff member
        Staff::create([
            'name' => 'Default Staff',
            'username' => 'staff',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '0123456789',
            'shop_id' => $shop->id,
            'is_active' => true
        ]);
    }
} 