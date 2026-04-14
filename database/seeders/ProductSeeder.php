<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        Product::truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $products = [
            // Electronics (high stock)
            ['name' => 'Wireless Bluetooth Headphones', 'quantity' => 25, 'category' => 'Electronics'],
            ['name' => 'Smart LED TV 55"', 'quantity' => 12, 'category' => 'Electronics'],
            ['name' => 'USB-C Charging Hub', 'quantity' => 3, 'category' => 'Electronics'],

            // Furniture (mixed stock)
            ['name' => 'Ergonomic Office Chair', 'quantity' => 8, 'category' => 'Furniture'],
            ['name' => 'Standing Desk Converter', 'quantity' => 15, 'category' => 'Furniture'],
            ['name' => 'Bookshelf 5-Tier', 'quantity' => 2, 'category' => 'Furniture'],

            // Clothing (mixed stock)
            ['name' => 'Men\'s Casual T-Shirt', 'quantity' => 50, 'category' => 'Clothing'],
            ['name' => 'Women\'s Running Jacket', 'quantity' => 7, 'category' => 'Clothing'],
            ['name' => 'Denim Jeans Slim Fit', 'quantity' => 4, 'category' => 'Clothing'],

            // Food & Beverage (high and low)
            ['name' => 'Organic Green Tea (100 bags)', 'quantity' => 30, 'category' => 'Food & Beverage'],
            ['name' => 'Cold Brew Coffee Concentrate', 'quantity' => 6, 'category' => 'Food & Beverage'],
            ['name' => 'Protein Granola Bar Pack', 'quantity' => 1, 'category' => 'Food & Beverage'],

            // Sports (varied)
            ['name' => 'Yoga Mat Premium', 'quantity' => 20, 'category' => 'Sports'],
            ['name' => 'Adjustable Dumbbell Set', 'quantity' => 5, 'category' => 'Sports'],
            ['name' => 'Resistance Bands Kit', 'quantity' => 2, 'category' => 'Sports'],
        ];

        $categoryIds = Category::pluck('id', 'name');

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'category_id' => $categoryIds[$product['category']],
            ]);
        }
    }
}
