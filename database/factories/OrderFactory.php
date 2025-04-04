<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 3),
            'client_name' => $this->faker->name(),
            'client_phone' => $this->faker->phoneNumber(),
            'client_address' => $this->faker->address(),
            'total' => $this->faker->randomFloat(2, 1, 1000),
            'delivered' => $this->faker->boolean(),
            'created_at' => Carbon::now()->startOfYear()->addDays(rand(0, 364)), // Random date within the current year
            'updated_at' => Carbon::now()->startOfYear()->addDays(rand(0, 364)), // Optional: Match updated_at to created_at
        ];
    }
}
