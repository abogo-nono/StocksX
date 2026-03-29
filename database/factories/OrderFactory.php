<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
            'user_id' => 1,
            'client_id' => $this->faker->boolean(80) ? Client::factory() : null,
            'total' => $this->faker->randomFloat(2, 1, 1000),
            'delivered' => $this->faker->boolean(),
            'created_at' => Carbon::now()->startOfYear()->addDays(rand(0, 364)), // Random date within the current year
            'updated_at' => Carbon::now()->startOfYear()->addDays(rand(0, 364)), // Optional: Match updated_at to created_at
        ];
    }
}
