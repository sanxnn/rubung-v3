<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $order = Order::factory()->create();

        return [
            'order_id' => $order->id,
            'midtrans_transaction_id' => 'MID-' . fake()->unique()->uuid(),
            'payment_type' => fake()->randomElement(['gopay', 'bank_transfer', 'credit_card', 'shopeepay']),
            'payment_status' => fake()->randomElement(['pending', 'success', 'failed', 'expire']),
            'amount' => $order->final_amount,
            'paid_at' => fake()->optional()->dateTime(),
        ];
    }
}
