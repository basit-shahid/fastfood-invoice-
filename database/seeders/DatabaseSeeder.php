<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create owner
        User::create([
            'name' => 'Owner User',
            'email' => 'owner@fastfood.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'phone' => '1234567890',
            'is_active' => true,
        ]);
        
        // Create manager
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@fastfood.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '1234567891',
            'is_active' => true,
        ]);
        
        // Create cashier
        User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@fastfood.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'phone' => '1234567892',
            'is_active' => true,
        ]);
        
        // Create sample menu items
        $menuItems = [
            [
                'name' => 'Classic Burger',
                'description' => 'Juicy beef patty with lettuce, tomato, and special sauce',
                'price' => 250.00,
                'category' => 'Burgers',
                'preparation_time' => 10,
                'is_available' => true,
            ],
            [
                'name' => 'Cheese Burger',
                'description' => 'Classic burger with melted cheese',
                'price' => 280.00,
                'category' => 'Burgers',
                'preparation_time' => 10,
                'is_available' => true,
            ],
            [
                'name' => 'French Fries',
                'description' => 'Crispy golden fries served with ketchup',
                'price' => 120.00,
                'category' => 'Sides',
                'preparation_time' => 5,
                'is_available' => true,
            ],
            [
                'name' => 'Chicken Wings',
                'description' => 'Spicy chicken wings with dip',
                'price' => 350.00,
                'category' => 'Appetizers',
                'preparation_time' => 15,
                'is_available' => true,
            ],
            [
                'name' => 'Soft Drink',
                'description' => 'Regular soft drink',
                'price' => 60.00,
                'category' => 'Beverages',
                'preparation_time' => 2,
                'is_available' => true,
            ],
            [
                'name' => 'Chocolate Shake',
                'description' => 'Rich chocolate milkshake',
                'price' => 180.00,
                'category' => 'Beverages',
                'preparation_time' => 5,
                'is_available' => true,
            ],
        ];
        
        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }
    }
}