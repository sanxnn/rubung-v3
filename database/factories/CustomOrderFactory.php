<?php

namespace Database\Factories;

use App\Models\CustomOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomOrderFactory extends Factory
{
    protected $model = CustomOrder::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reference_number' => 'CO-' . date('Ymd') . '-' . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'description' => fake()->paragraph(),
            'estimated_price' => fake()->optional(0.7)->numberBetween(200000, 2000000), // 70% chance ada estimasi
            'estimated_time' => fake()->optional()->randomElement(['3 hari', '1 minggu', '2 minggu']),
            'status' => fake()->randomElement(['pending_review', 'quoted', 'approved', 'rejected']),
            'admin_notes' => fake()->optional()->sentence(),
            'order_id' => null,
        ];
    }
}
