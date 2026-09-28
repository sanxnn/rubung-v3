<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubcategoryFactory extends Factory
{
    protected $model = Subcategory::class;

    public function definition(): array
    {
        $names = ['Batik Tulis', 'Batik Cap', 'Batik Kombinasi'];
        $name = fake()->randomElement($names);

        return [
            'category_id' => Category::factory()->batik(),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
