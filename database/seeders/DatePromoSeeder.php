<?php

namespace Database\Seeders;

use App\Models\DatePromo;
use Illuminate\Database\Seeder;

class DatePromoSeeder extends Seeder
{
    public function run(): void
    {
        DatePromo::create([
            'name' => 'Promo 10.10',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'start_datetime' => now()->setDate(now()->year, 10, 10)->setTime(0, 0),
            'end_datetime' => now()->setDate(now()->year, 10, 10)->setTime(23, 59),
            'is_active' => true,
        ]);

        DatePromo::create([
            'name' => 'Promo 12.12',
            'discount_type' => 'percentage',
            'discount_value' => 25,
            'start_datetime' => now()->setDate(now()->year, 12, 12)->setTime(0, 0),
            'end_datetime' => now()->setDate(now()->year, 12, 12)->setTime(23, 59),
            'is_active' => true,
        ]);
    }
}
