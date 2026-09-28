<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('08##########'), // Format Indonesia: 08xxxxxxxxxx
            'password' => Hash::make('password'), // Default password untuk testing
            'role' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
            'name' => 'Admin Batik Rubung Kuning',
            'email' => 'admin@batikrubungkuning.com',
        ]);
    }
}
