<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $subtotal = fake()->numberBetween(100000, 1000000);
        $shipping = fake()->numberBetween(10000, 50000);

        return [
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'subtotal' => $subtotal,
            'discount_date_cantik' => 0,
            'discount_voucher' => 0,
            'shipping_cost' => $shipping,
            'final_amount' => $subtotal + $shipping,
            'status' => fake()->randomElement(['pending_payment', 'paid', 'processing', 'packing', 'shipped', 'completed']),
            'shipping_courier' => fake()->optional()->randomElement(['JNE', 'J&T', 'SiCepat', 'Pos Indonesia']),
            'resi_number' => fake()->optional()->numerify('RESI#########'),
            'snapshot_recipient_name' => $address->recipient_name,
            'snapshot_phone' => $address->phone,
            'snapshot_province' => $address->province,
            'snapshot_city' => $address->city,
            'snapshot_postal_code' => $address->postal_code,
            'snapshot_detail' => $address->detail,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
