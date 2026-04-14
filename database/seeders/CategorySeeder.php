<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        Category::truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $categories = [
            'Electronics',
            'Furniture',
            'Clothing',
            'Food & Beverage',
            'Sports',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
