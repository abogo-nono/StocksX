<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default tenant for development
        Tenant::firstOrCreate(
            ['email' => 'demo@stocksx.com'],
            [
                'name' => 'Demo Company',
                'email' => 'demo@stocksx.com',
                'phone' => '+1234567890',
                'address' => '123 Business Street, City, Country',
                'main_color' => '#0891b2',
            ]
        );

        // Create additional sample tenants if needed
        Tenant::firstOrCreate(
            ['email' => 'acme@example.com'],
            [
                'name' => 'ACME Corporation',
                'email' => 'acme@example.com',
                'phone' => '+1987654321',
                'address' => '456 Corporate Avenue, Business City',
                'main_color' => '#059669',
            ]
        );
    }
}
