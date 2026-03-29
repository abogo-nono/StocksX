<?php

namespace Database\Seeders;

use App\Models\ProductSupplier;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create product suppliers with real values
        $suppliers = [
            ['name' => 'Supplier A', 'email' => 'supplierA@example.com', 'phone' => '1234567890', 'category_id' => 1],
            ['name' => 'Supplier B', 'email' => 'supplierB@example.com', 'phone' => '0987654321', 'category_id' => 2],
            ['name' => 'Supplier C', 'email' => 'supplierC@example.com', 'phone' => '1122334455', 'category_id' => 3],
        ];
        foreach ($suppliers as $supplier) {
            ProductSupplier::create($supplier);
        }
    }
}
