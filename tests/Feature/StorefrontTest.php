<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RefundRequest;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_catalog_filters_by_subcategory_and_displayed_starting_price(): void
    {
        $batik = Category::factory()->batik()->create();
        $tulis = Subcategory::create(['category_id' => $batik->id, 'name' => 'Tulis', 'slug' => 'tulis']);
        $cap = Subcategory::create(['category_id' => $batik->id, 'name' => 'Cap', 'slug' => 'cap']);
        $match = $this->variant($batik, 'Kain Sesuai');
        $cheap = $this->variant($batik, 'Kain Murah');
        $expensive = $this->variant($batik, 'Kain Mahal');
        $other = $this->variant($batik, 'Kain Cap');
        foreach ([$match, $cheap, $expensive] as $variant) {
            $variant->product->update(['subcategory_id' => $tulis->id]);
        }
        $other->product->update(['subcategory_id' => $cap->id]);
        $cheap->update(['price' => 90000]);
        ProductVariant::factory()->create(['product_id' => $cheap->product_id, 'price' => 150000]);
        $expensive->update(['price' => 250000]);

        $this->get(route('company.catalog', [
            'category' => 'batik', 'subcategory' => $tulis->id,
            'min_price' => 150000, 'max_price' => 150000,
        ]))->assertOk()->assertSee('Kain Sesuai')->assertDontSee('Kain Cap')
            ->assertDontSee('Kain Murah')->assertDontSee('Kain Mahal');

        $this->get(route('company.catalog', ['max_price' => 100000]))
            ->assertOk()->assertSee('Kain Murah')->assertDontSee('Kain Sesuai');

        $this->from('/katalog')->get('/katalog?min_price=200000&max_price=100000')
            ->assertRedirect('/katalog')->assertSessionHasErrors('max_price');
    }

    public function test_storefront_language_persists_between_pages_and_translates_contact_feedback(): void
    {
        $originalLocale = app()->getLocale();
        Mail::fake();
        config(['services.contact.email' => 'contact@example.test']);

        $this->get('/?lang=en')->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('e.g. Siti Aminah')
            ->assertSee('Tell us what you need or include your order number.')
            ->assertSee('aria-label="Dark mode"', false);
        $this->get('/katalog?q=Ikat')->assertOk()
            ->assertSee('Product Catalog')->assertSee('No products found.')
            ->assertSee('q=Ikat&amp;lang=id', false);

        $this->from('/?lang=en')->post('/contact', [])->assertSessionHasErrors([
            'name' => 'Enter your full name.',
            'message' => 'Enter a message between 10 and 5,000 characters.',
        ]);

        $this->from('/?lang=en')->post('/contact', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.test',
            'message' => 'I would like to ask about custom orders.',
        ])->assertRedirect('/?lang=en')
            ->assertSessionHas('contact_success', 'Your message has been sent.');

        Mail::assertSent(ContactMessage::class);
        $this->get('/?lang=id')->assertOk()
            ->assertSee('lang="id"', false)->assertSee('Contoh: Siti Aminah');
        $this->get('/katalog')->assertOk()->assertSee('Katalog Produk');
        $this->assertSame($originalLocale, app()->getLocale());
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
