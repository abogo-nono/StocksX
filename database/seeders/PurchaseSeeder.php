<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\ProductSupplier;
use App\Models\Product;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first tenant and user for demo data
        $tenant = Tenant::first();
        $user = User::first();
        $supplier = ProductSupplier::first();

        if (!$tenant || !$user || !$supplier) {
            $this->command->info('Skipping purchase seeder - missing required data (tenant, user, or supplier)');
            return;
        }

        // Create sample purchases
        $purchases = [
            [
                'supplier_id' => $supplier->id,
                'date' => now()->subDays(15),
                'purchase_no' => 'PO-001',
                'status' => 'complete',
                'total_amount' => 1500.00,
                'created_by' => $user->id,
                'tenant_id' => $tenant->id,
            ],
            [
                'supplier_id' => $supplier->id,
                'date' => now()->subDays(7),
                'purchase_no' => 'PO-002',
                'status' => 'pending',
                'total_amount' => 2300.00,
                'created_by' => $user->id,
                'tenant_id' => $tenant->id,
            ],
            [
                'supplier_id' => $supplier->id,
                'date' => now()->subDays(3),
                'purchase_no' => 'PO-003',
                'status' => 'approved',
                'total_amount' => 850.00,
                'created_by' => $user->id,
                'tenant_id' => $tenant->id,
            ],
        ];

        foreach ($purchases as $purchaseData) {
            $purchase = Purchase::create($purchaseData);

            // Get some products for purchase details
            $products = Product::where('tenant_id', $tenant->id)->take(3)->get();

            foreach ($products as $index => $product) {
                $quantity = rand(5, 20);
                $unitcost = $product->buying_price ?? rand(10, 100);

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unitcost' => $unitcost,
                    'total' => $quantity * $unitcost,
                ]);
            }
        }

        $this->command->info('Purchase seeder completed successfully!');
    }
}
