<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Original Recipe Chicken',
                'description' => 'Colonel’s secret blend of 11 herbs and spices.',
                'category' => 'Chicken',
                'price' => 129.00,
                'image' => 'assets/images/og_chick.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Zinger Burger',
                'description' => 'Spicy chicken fillet burger with lettuce and mayo.',
                'category' => 'Burgers',
                'price' => 159.00,
                'image' => 'assets/images/zinger.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Famous Bowl',
                'description' => 'Mashed potato, gravy, corn, and crispy chicken.',
                'category' => 'Bowls',
                'price' => 149.00,
                'image' => 'assets/images/fambowl.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Twister',
                'description' => 'Tortilla wrap with crispy strips and fresh veggies.',
                'category' => 'Wraps',
                'price' => 119.00,
                'image' => 'assets/images/twister.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Regular Fries',
                'description' => 'Crispy golden fries.',
                'category' => 'Sides',
                'price' => 69.00,
                'image' => 'assets/images/fries.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Soft Drink',
                'description' => 'Chilled fountain drink.',
                'category' => 'Drinks',
                'price' => 49.00,
                'image' => 'assets/images/coke.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
