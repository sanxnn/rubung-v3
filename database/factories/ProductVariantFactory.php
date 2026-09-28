<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['100x100 cm', '120x150 cm', 'S', 'M', 'L', 'XL', 'Paket']),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'price' => fake()->numberBetween(50000, 500000), // 50rb - 500rb
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
