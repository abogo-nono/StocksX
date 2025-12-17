<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DefaultTenantSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create default tenant for existing data
        Tenant::create([
            'id' => 1,
            'name' => 'Default Company',
            'email' => 'admin@stocksx.com',
            'phone' => '+1234567890',
            'main_color' => 'gray',
            'address' => '123 Main Street, City, Country',
        ]);
    }
}
