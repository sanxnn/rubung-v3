<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        $types = ['percentage', 'fixed'];
        $type = fake()->randomElement($types);

        return [
            'code' => strtoupper(fake()->unique()->bothify('VOUCHER-??##')),
            'discount_type' => $type,
            'discount_value' => $type === 'percentage'
                ? fake()->numberBetween(5, 50) // 5% - 50%
                : fake()->numberBetween(10000, 100000), // 10rb - 100rb
            'min_purchase' => fake()->numberBetween(50000, 200000),
            'max_discount' => $type === 'percentage' ? fake()->numberBetween(50000, 200000) : null,
            'usage_limit' => fake()->optional(0.3)->numberBetween(10, 100), // 30% chance null
            'used_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
        ];
    }
}
