<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Produk Batik (dengan subkategori)
        $batikCategory = Category::where('name', 'Batik')->first();
        $subcategories = Subcategory::where('category_id', $batikCategory->id)->get();

        foreach ($subcategories as $subcategory) {
            // 3 produk per subkategori
            for ($i = 0; $i < 3; $i++) {
                $product = Product::create([
                    'category_id' => $batikCategory->id,
                    'subcategory_id' => $subcategory->id,
                    'name' => $subcategory->name . ' ' . fake()->randomElement(['Parung Jember', 'Osing Banyuwangi', 'Mega Mendung', 'Kawung', 'Parang']),
                    'slug' => Str::slug($subcategory->name . ' ' . fake()->unique()->word()),
                    'description' => fake()->paragraph(),
                ]);

                // 2 varian per produk (ukuran berbeda)
                $sizes = ['100x100 cm', '120x150 cm'];
                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $size,
                        'sku' => strtoupper('BATIK-' . $product->id . '-' . Str::slug($size)),
                        'price' => $size === '100x100 cm' ? 200000 : 265000,
                        'stock' => 0, // Batik PO, tidak pakai stok
                    ]);
                }
            }
        }

        // 2. Produk Bahan (tanpa subkategori)
        $bahanCategory = Category::where('name', 'Bahan')->first();

        $bahanProducts = ['Canting', 'Kain Mori', 'Lilin Batik', 'Pewarna Naptol'];
        foreach ($bahanProducts as $name) {
            $product = Product::create([
                'category_id' => $bahanCategory->id,
                'subcategory_id' => null,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => fake()->paragraph(),
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'name' => 'Satuan',
                'sku' => strtoupper('BAHAN-' . $product->id),
                'price' => fake()->numberBetween(20000, 100000),
                'stock' => fake()->numberBetween(10, 50),
            ]);
        }

        // Paket Mahasiswa (Bundle)
        $paketProduct = Product::create([
            'category_id' => $bahanCategory->id,
            'subcategory_id' => null,
            'name' => 'Paket Mahasiswa',
            'slug' => 'paket-mahasiswa',
            'description' => 'Paket lengkap untuk pemula: 1 Canting, 1 Kain, 2 Lilin',
        ]);

        $paketVariant = ProductVariant::create([
            'product_id' => $paketProduct->id,
            'name' => 'Paket',
            'sku' => 'BAHAN-PAKET-MAHASISWA',
            'price' => 150000,
            'stock' => 999, // Virtual bundle, stok dihitung dari komponen
        ]);

        // Link ke komponen (Canting, Kain, Lilin)
        $componentProducts = Product::where('category_id', $bahanCategory->id)
            ->where('name', '!=', 'Paket Mahasiswa')
            ->take(3)
            ->get();

        $quantities = [1, 1, 2]; // 1 Canting, 1 Kain, 2 Lilin
        foreach ($componentProducts as $index => $componentProduct) {
            $componentVariant = $componentProduct->variants->first();
            if ($componentVariant) {
                ProductBundleItem::create([
                    'parent_variant_id' => $paketVariant->id,
                    'component_variant_id' => $componentVariant->id,
                    'quantity' => $quantities[$index] ?? 1,
                ]);
            }
        }

        // 3. Produk Olahan Limbah (tanpa subkategori)
        $limbahCategory = Category::where('name', 'Olahan Limbah')->first();

        $limbahProducts = ['Dompet Batik', 'Pita Batik', 'Selendang Batik', 'Tas Batik'];
        foreach ($limbahProducts as $name) {
            $product = Product::create([
                'category_id' => $limbahCategory->id,
                'subcategory_id' => null,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => fake()->paragraph(),
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'name' => 'Satu Ukuran',
                'sku' => strtoupper('LIMBAH-' . $product->id),
                'price' => fake()->numberBetween(50000, 200000),
                'stock' => fake()->numberBetween(5, 30),
            ]);
        }
    }
}
