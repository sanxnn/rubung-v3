<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        // 1. Admin
        User::firstOrCreate(['email' => 'admin@batikrubungkuning.com'], User::factory()->admin()->make([
            'name' => 'Admin Batik Rubung Kuning',
            'email' => 'admin@batikrubungkuning.com',
            'phone' => '081234567890',
        ])->getAttributes());

        // 2. Customer Dummy (untuk testing)
        if (User::where('role', 'customer')->exists()) {
            return;
        }

        $customers = User::factory()->count(5)->create();

        // Beri setiap customer 2 alamat (1 primary)
        foreach ($customers as $customer) {
            Address::factory()->primary()->create(['user_id' => $customer->id]);
            Address::factory()->create(['user_id' => $customer->id, 'is_primary' => false]);
        }
    }
}
