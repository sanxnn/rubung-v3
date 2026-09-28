<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;

    public function definition(): array
    {
        $statuses = ['pending_payment', 'paid', 'processing', 'packing', 'shipped', 'completed'];

        return [
            'order_id' => Order::factory(),
            'old_status' => fake()->randomElement($statuses),
            'new_status' => fake()->randomElement($statuses),
            'changed_by' => User::factory()->admin(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
