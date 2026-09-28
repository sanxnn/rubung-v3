<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $batikCategory = Category::where('name', 'Batik')->first();

        if ($batikCategory) {
            $subcategories = ['Batik Tulis', 'Batik Cap', 'Batik Kombinasi'];

            foreach ($subcategories as $name) {
                Subcategory::create([
                    'category_id' => $batikCategory->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]);
            }
        }
    }
}
