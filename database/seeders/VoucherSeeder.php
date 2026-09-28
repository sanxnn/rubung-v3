<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::create([
            'code' => 'BATIKHEMAT',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_purchase' => 100000,
            'max_discount' => 50000,
            'usage_limit' => 100,
            'used_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'NEWUSER',
            'discount_type' => 'fixed',
            'discount_value' => 25000,
            'min_purchase' => 150000,
            'usage_limit' => null,
            'used_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);
    }
}
