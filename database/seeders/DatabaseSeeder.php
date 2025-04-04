<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        // User::factory(2)->create();

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

        // Create product suppliers with real values
        $suppliers = [
            ['name' => 'Supplier A', 'email' => 'supplierA@example.com', 'phone' => '1234567890', 'category_id' => 1],
            ['name' => 'Supplier B', 'email' => 'supplierB@example.com', 'phone' => '0987654321', 'category_id' => 2],
            ['name' => 'Supplier C', 'email' => 'supplierC@example.com', 'phone' => '1122334455', 'category_id' => 3],
        ];
        foreach ($suppliers as $supplier) {
            ProductSupplier::create($supplier);
        }

        // Create products with real values
        $products = [
            ['name' => 'Laptop', 'slug' => Str::slug("Laptop"), 'price' => 1000, 'quantity' => 50, 'product_categories_id' => 1, 'product_suppliers_id' => 1, 'image' => 'laptop.jpg'],
            ['name' => 'Sofa', 'slug' => Str::slug("Sofa"), 'price' => 500, 'quantity' => 20, 'product_categories_id' => 2, 'product_suppliers_id' => 2, 'image' => 'sofa.jpg'],
            ['name' => 'T-Shirt', 'slug' => Str::slug("T-Shirt"), 'price' => 20, 'quantity' => 100, 'product_categories_id' => 3, 'product_suppliers_id' => 3, 'image' => 'tshirt.jpg'],
            ['name' => 'Novel', 'slug' => Str::slug("Novel"), 'price' => 15, 'quantity' => 40, 'product_categories_id' => 4, 'product_suppliers_id' => 1, 'image' => 'novel.jpg'],
            ['name' => 'Action Figure', 'slug' => Str::slug("Action Figure"), 'price' => 25, 'quantity' => 30, 'product_categories_id' => 5, 'product_suppliers_id' => 2, 'image' => 'action_figure.jpg'],
        ];
        foreach ($products as $product) {
            Product::create($product);
        }

        // Generate orders using factories
        $orders = Order::factory(873)->create();
    }
}
