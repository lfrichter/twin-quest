<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $statuses = ['active', 'inactive', 'discontinued'];

        Product::truncate();

        // Create 50 products
        for ($i = 1; $i <= 50; $i++) {
            Product::create([
                'name' => 'Product '.$i,
                'description' => 'Description of Product '.$i,
                'price' => rand(10, 1000) + (rand(0, 99) / 100),
                'category_id' => $categories->random()->id,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
