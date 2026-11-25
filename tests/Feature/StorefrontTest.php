<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RefundRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_renders_when_catalog_is_empty(): void
    {
        $this->get('/')->assertOk()->assertSee('Koleksi Batik belum tersedia.');
        $this->get('/katalog')->assertOk()->assertSee('Produk tidak ditemukan.');
    }

    public function test_home_ranks_batik_by_paid_non_refunded_units_and_catalog_includes_all_categories(): void
    {
        $batik = Category::factory()->batik()->create();
        $bahan = Category::factory()->bahan()->create();
        $most = $this->variant($batik, 'Batik Mawar');
        $second = $this->variant($batik, 'Batik Melati');
        $third = $this->variant($batik, 'Batik Anggrek');
        $other = $this->variant($batik, 'Batik Kenanga');
        $material = $this->variant($bahan, 'Bahan Katun');

        $this->recordSale($most, 4);
        $this->recordSale($second, 3);
        $this->recordSale($third, 2);
        $this->recordSale($other, 1);
        $this->recordSale($other, 90, status: 'cancelled');
        $this->recordSale($other, 80, refunded: true);
        $this->recordSale($other, 60, paymentStatus: 'pending');
        $this->recordSale($material, 100);

        $this->get('/')->assertOk()
            ->assertSeeInOrder(['Batik Mawar', 'Batik Melati', 'Batik Anggrek'])
            ->assertSee('4 terjual')->assertSee('Lihat Produk Selengkapnya')
            ->assertDontSee('Batik Kenanga')->assertDontSee('Bahan Katun');

        $this->get('/katalog')->assertOk()->assertSee('Batik Kenanga')->assertSee('Bahan Katun');
        $this->get('/katalog?category=bahan&q=Katun')->assertOk()
            ->assertSee('Bahan Katun')->assertDontSee('Batik Mawar');
    }

    private function variant(Category $category, string $name): ProductVariant
    {
        $product = Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
        ]);

        return ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 150000]);
    }

    private function recordSale(
        ProductVariant $variant,
        int $quantity,
        string $status = 'paid',
        bool $refunded = false,
        string $paymentStatus = 'success',
    ): void {
        $order = Order::factory()->create([
            'status' => $status,
            'order_number' => 'STOREFRONT-'.(Order::count() + 1),
        ]);
        Payment::factory()->create(['order_id' => $order->id, 'payment_status' => $paymentStatus]);
        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $variant->product->name,
            'variant_name' => $variant->name,
            'quantity' => $quantity,
            'unit_price' => $variant->price,
            'subtotal' => $quantity * $variant->price,
        ]);

        if ($refunded) {
            RefundRequest::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'reason' => 'Pengembalian untuk pengujian katalog.',
                'evidence_path' => 'refunds/uji.webp',
                'amount' => $order->final_amount,
                'status' => 'completed',
            ]);
        }
    }
}
