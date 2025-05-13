<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(Faker $faker): void
    {
        $categories = Category::all();

        $statuses = ['active', 'inactive', 'discontinued'];

        Product::truncate();

        // Create 50 products
        for ($i = 1; $i <= 50; $i++) {
            $name = $faker->unique()->words(2, true);
            Product::firstOrCreate(
                [
                    'name' => "Product {$name}",
                ],
                [
                    'description' => "Description of {$faker->sentence}",
                    'price' => rand(10, 1000) + (rand(0, 99) / 100),
                    'category_id' => $categories->random()->id,
                    'status' => $statuses[array_rand($statuses)],
                ]);
        }
    }
}
