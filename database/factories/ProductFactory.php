<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $category = Category::factory()->create();

        return [
            'category_id' => $category->id,
            'subcategory_id' => $category->type === 'batik'
                ? Subcategory::factory()->create(['category_id' => $category->id])->id
                : null,
            'name' => fake()->sentence(3),
            'slug' => Str::slug(fake()->sentence(3)),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(640, 480, 'fashion', true, 'batik'), // <--- BARU: Generate gambar dummy
        ];
    }

    public function batik(): static
    {
        return $this->afterCreating(function (Product $product) {
            $category = Category::where('type', 'batik')->first()
                ?? Category::factory()->batik()->create();

            $subcategory = Subcategory::where('category_id', $category->id)->first()
                ?? Subcategory::factory()->create(['category_id' => $category->id]);

            $product->update([
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'name' => 'Batik ' . fake()->randomElement(['Parung Jember', 'Osing Banyuwangi', 'Mega Mendung']),
            ]);
        });
    }

    public function bahan(): static
    {
        return $this->afterCreating(function (Product $product) {
            $category = Category::where('type', 'bahan')->first()
                ?? Category::factory()->bahan()->create();

            $product->update([
                'category_id' => $category->id,
                'subcategory_id' => null,
                'name' => fake()->randomElement(['Canting', 'Kain', 'Lilin', 'Paket Mahasiswa', 'Paket Dosen']),
            ]);
        });
    }

    public function olahanLimbah(): static
    {
        return $this->afterCreating(function (Product $product) {
            $category = Category::where('type', 'olahan_limbah')->first()
                ?? Category::factory()->olahanLimbah()->create();

            $product->update([
                'category_id' => $category->id,
                'subcategory_id' => null,
                'name' => fake()->randomElement(['Dompet', 'Pita', 'Selendang', 'Tas']),
            ]);
        });
    }
}
