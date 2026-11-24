<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_with_an_empty_catalog(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Ingin tahu motif dan produk yang tersedia?')
            ->assertSee('Mode gelap');
    }

    public function test_landing_page_displays_catalog_products_and_variant_price(): void
    {
        $product = Product::factory()->create([
            'name' => 'Batik Tulis Melati',
            'image' => 'products/melati.jpg',
        ]);
        ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 250000]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Batik Tulis Melati')
            ->assertSee('Rp250.000')
            ->assertSee('/api/storage-image/products/melati.jpg');
    }
}
