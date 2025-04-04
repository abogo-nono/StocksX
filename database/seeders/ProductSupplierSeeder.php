<?php

namespace Database\Seeders;

use App\Models\ProductSupplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductSupplier::truncate()->cascade();
        ProductSupplier::factory(5)->create();
    }
}
