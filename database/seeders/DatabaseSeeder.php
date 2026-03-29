<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users


        $user = $this->createUser();

        $this->call([
            ShieldSeeder::class,
            ProductCategorySeeder::class,
            ProductSupplierSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);


        $this->assignRole($user);


    }

    public function createUser(): User
    {
       return User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.site',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

    }

    /**
     * @param $user
     * @return void
     */
    public function assignRole($user): void
    {
        $user->assignRole('super_admin');
    }
}
