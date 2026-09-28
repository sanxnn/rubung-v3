<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Batik', 'Bahan', 'Olahan Limbah']),
            'slug' => Str::slug(fake()->randomElement(['batik', 'bahan', 'olahan-limbah'])),
            'description' => fake()->paragraph(),
            'has_subcategories' => fake()->boolean(),
        ];
    }

    public function batik(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Batik',
            'slug' => 'batik',
            'description' => 'Kategori khusus untuk produk dan kerajinan Batik.',
            'has_subcategories' => true, // Batik pasti punya subkategori
        ]);
    }

    public function bahan(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Bahan',
            'slug' => 'bahan',
            'description' => 'Kategori khusus untuk berbagai macam bahan baku.',
            'has_subcategories' => false,
        ]);
    }

    public function olahanLimbah(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Olahan Limbah',
            'slug' => 'olahan-limbah',
            'description' => 'Kategori khusus untuk produk olahan dari limbah.',
            'has_subcategories' => false,
        ]);
    }
}
