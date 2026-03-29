<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create products with real values
        $products = [
            ['name' => 'Laptop', 'slug' => Str::slug('Laptop'), 'price' => 1000, 'quantity' => 50, 'product_categories_id' => 1, 'product_suppliers_id' => 1, 'image' => 'laptop.jpg'],
            ['name' => 'Sofa', 'slug' => Str::slug('Sofa'), 'price' => 500, 'quantity' => 20, 'product_categories_id' => 2, 'product_suppliers_id' => 2, 'image' => 'sofa.jpg'],
            ['name' => 'T-Shirt', 'slug' => Str::slug('T-Shirt'), 'price' => 20, 'quantity' => 100, 'product_categories_id' => 3, 'product_suppliers_id' => 3, 'image' => 'tshirt.jpg'],
            ['name' => 'Novel', 'slug' => Str::slug('Novel'), 'price' => 15, 'quantity' => 40, 'product_categories_id' => 4, 'product_suppliers_id' => 1, 'image' => 'novel.jpg'],
            ['name' => 'Action Figure', 'slug' => Str::slug('Action Figure'), 'price' => 25, 'quantity' => 30, 'product_categories_id' => 5, 'product_suppliers_id' => 2, 'image' => 'action_figure.jpg'],
        ];
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
