<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            Category::factory()->batik()->make(),
            Category::factory()->bahan()->make(),
            Category::factory()->olahanLimbah()->make(),
            Category::factory()->bundling()->make(),
        ] as $category) {
            Category::firstOrCreate(['slug' => $category->slug], $category->getAttributes());
        }
    }
}
