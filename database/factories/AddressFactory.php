<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $labels = ['Rumah', 'Kantor', 'Kos', 'Rumah Orang Tua'];
        $provinces = ['Jawa Timur', 'Jawa Tengah', 'DKI Jakarta', 'Bali'];
        $cities = ['Jember', 'Banyuwangi', 'Surabaya', 'Malang', 'Denpasar'];

        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement($labels),
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'province' => fake()->randomElement($provinces),
            'city' => fake()->randomElement($cities),
            'district' => fake()->city(),
            'postal_code' => fake()->numerify('#####'), // 5 digit
            'detail' => fake()->address(),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
