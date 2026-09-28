<?php

namespace Database\Factories;

use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductBundleItemFactory extends Factory
{
    protected $model = ProductBundleItem::class;

    public function definition(): array
    {
        return [
            'parent_variant_id' => ProductVariant::factory(),
            'component_variant_id' => ProductVariant::factory(),
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}
