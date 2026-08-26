<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name' => 'KFC Megamall',
                'address' => 'EDSA, Mandaluyong',
                'lat' => 14.5842000,
                'lng' => 121.0565000,
                'is_active' => true,
            ],
            [
                'name' => 'KFC SM Mall of Asia',
                'address' => 'Pasay',
                'lat' => 14.5352000,
                'lng' => 120.9822000,
                'is_active' => true,
            ],
            [
                'name' => 'KFC Trinoma',
                'address' => 'Quezon City',
                'lat' => 14.6539000,
                'lng' => 121.0323000,
                'is_active' => true,
            ],
        ];

        foreach ($stores as $store) {
            Restaurant::query()->updateOrCreate(
                ['name' => $store['name']],
                $store
            );
        }
    }
}
