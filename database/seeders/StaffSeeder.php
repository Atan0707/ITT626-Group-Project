<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run()
    {
        Staff::create([
            'name' => 'Test Staff',
            'username' => 'teststaff',
            'email' => 'teststaff@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '0123456789',
            'shop_id' => 1, // Make sure this shop_id exists
            'is_active' => true
        ]);
    }
} 