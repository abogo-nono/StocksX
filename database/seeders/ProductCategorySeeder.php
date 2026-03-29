<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create product categories with real values
        $categories = [
            ['title' => 'Electronics', 'slug' => 'electronics'],
            ['title' => 'Furniture', 'slug' => 'furniture'],
            ['title' => 'Clothing', 'slug' => 'clothing'],
            ['title' => 'Books', 'slug' => 'books'],
            ['title' => 'Toys', 'slug' => 'toys'],
        ];
        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
