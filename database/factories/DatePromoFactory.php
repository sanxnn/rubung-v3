<?php

namespace Database\Factories;

use App\Models\DatePromo;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatePromoFactory extends Factory
{
    protected $model = DatePromo::class;

    public function definition(): array
    {
        $types = ['percentage', 'fixed'];
        $type = fake()->randomElement($types);
        $startDate = now()->addDays(fake()->numberBetween(1, 30));

        return [
            'name' => 'Promo ' . fake()->randomElement(['Tanggal Cantik', '10.10', '11.11', '12.12']),
            'discount_type' => $type,
            'discount_value' => $type === 'percentage'
                ? fake()->numberBetween(10, 30) // 10% - 30%
                : fake()->numberBetween(20000, 150000), // 20rb - 150rb
            'start_datetime' => $startDate->setTime(0, 0),
            'end_datetime' => $startDate->copy()->setTime(23, 59),
            'is_active' => true,
        ];
    }
}
