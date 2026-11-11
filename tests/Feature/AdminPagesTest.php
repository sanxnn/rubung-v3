<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use App\Models\Category;
use App\Models\CustomOrder;
use App\Models\DatePromo;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Voucher;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_and_admin_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);

        foreach ([
            'admin.dashboard',
            'admin.profile',
            'admin.orders.index',
            'admin.custom-orders.index',
            'admin.categories.index',
            'admin.products.index',
            'admin.bundles.index',
            'admin.customers.index',
            'admin.promo.index',
            'admin.voucher.index',
            'admin.reports.index',
        ] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }

        $category = Category::where('slug', 'batik')->firstOrFail();
        $product = Product::where('slug', 'canting')->firstOrFail();
        $order = Order::factory()->create();

        $this->get(route('admin.subcategories.index', $category))->assertOk();
        $this->get(route('admin.variants.index', $product))->assertOk();
        $this->get(route('admin.orders.show', $order))->assertOk();
        $this->get(route('admin.dashboard'))->assertViewHas('stats', fn ($stats) => $stats['low_stock_items'] === 0);
    }

    public function test_landing_contact_form_validates_and_sends_message(): void
    {
        Mail::fake();
        config()->set('services.contact.email', 'owner@example.com');

        $this->from('/')->post(route('contact.send'), [
            'name' => 'Sari',
            'email' => 'sari@example.com',
            'message' => 'Saya ingin bertanya tentang produk batik.',
        ])->assertRedirect('/')->assertSessionHas('contact_success');

        Mail::assertSent(ContactMessage::class, fn ($message) => $message->hasTo('owner@example.com') && $message->contact['name'] === 'Sari');

        $this->from('/')->post(route('contact.send'), [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Ini adalah pesan otomatis.',
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');
    }

    public function test_category_subcategory_product_and_variant_crud(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->post(route('admin.categories.store'), [
            'name' => 'Kain Spesial',
            'description' => 'Kategori kain pilihan.',
            'has_subcategories' => 1,
        ])->assertRedirect(route('admin.categories.index'))->assertSessionHasNoErrors();

        $category = Category::where('name', 'Kain Spesial')->firstOrFail();
        $this->put(route('admin.categories.update', $category), [
            'name' => 'Kain Spesial',
            'description' => 'Kategori kain terbaru.',
            'has_subcategories' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->post(route('admin.subcategories.store', $category), [
            'name' => 'Kain Halus',
        ])->assertSessionHasNoErrors();

        $subcategory = Subcategory::where('name', 'Kain Halus')->firstOrFail();
        $this->put(route('admin.categories.update', $category), [
            'name' => 'Kain Spesial',
            'description' => 'Kategori kain terbaru.',
            'has_subcategories' => 0,
        ])->assertSessionHasErrors('has_subcategories');

        $this->put(route('admin.subcategories.update', [$category, $subcategory]), [
            'name' => 'Kain Halus',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Kain Premium',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $product = Product::where('name', 'Kain Premium')->firstOrFail();
        $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Kain Premium',
            'description' => 'Kain untuk produk pilihan.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->post(route('admin.variants.store', $product), [
            'name' => 'Ukuran Besar',
            'sku' => 'KAIN-BESAR',
            'price' => 125000,
            'stock' => 8,
        ])->assertSessionHasNoErrors();

        $variant = ProductVariant::where('sku', 'KAIN-BESAR')->firstOrFail();
        $this->put(route('admin.variants.update', [$product, $variant]), [
            'name' => 'Ukuran Besar',
            'sku' => 'KAIN-BESAR',
            'price' => 130000,
            'stock' => 7,
        ])->assertSessionHasNoErrors();
        $this->assertSame(130000, $variant->fresh()->price);

        $this->delete(route('admin.variants.destroy', [$product, $variant]))->assertSessionHasNoErrors();
        $this->delete(route('admin.products.destroy', $product))->assertSessionHasNoErrors();
        $this->delete(route('admin.subcategories.destroy', [$category, $subcategory]))->assertSessionHasNoErrors();
        $this->delete(route('admin.categories.destroy', $category))->assertSessionHasNoErrors();
    }

    public function test_bundle_crud_uses_seeded_component(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);

        $component = ProductVariant::whereHas('product', fn ($query) => $query->where('slug', 'canting'))
            ->firstOrFail();

        $payload = [
            'name' => 'Paket Pemula',
            'bundle_sku' => 'PAKET-PEMULA',
            'bundle_price' => 150000,
            'components' => [['variant_id' => $component->id, 'quantity' => 1]],
        ];

        $this->post(route('admin.bundles.store'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $bundle = Product::where('name', 'Paket Pemula')->firstOrFail();
        $this->assertSame('Bundling', $bundle->category->name);

        $this->put(route('admin.bundles.update', $bundle), [
            ...$payload,
            'bundle_price' => 160000,
        ])->assertSessionHasNoErrors();

        $this->assertSame(160000, $bundle->fresh()->variants()->firstOrFail()->price);
        $this->delete(route('admin.bundles.destroy', $bundle))->assertSessionHasNoErrors();
    }

    public function test_subcategory_slugs_stay_unique_across_categories(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $first = Category::factory()->batik()->create();
        $second = Category::factory()->batik()->create([
            'name' => 'Batik Lain',
            'slug' => 'batik-lain',
        ]);

        foreach ([$first, $second] as $category) {
            $this->post(route('admin.subcategories.store', $category), [
                'name' => 'Batik Tulis',
            ])->assertRedirect()->assertSessionHasNoErrors();
        }

        $this->assertSame(['batik-tulis', 'batik-tulis-2'],
            Subcategory::orderBy('id')->pluck('slug')->all());
    }

    public function test_product_image_replacement_and_component_deletion_guard(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('role', 'admin')->firstOrFail());
        $category = Category::where('slug', 'bahan')->firstOrFail();

        $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Produk Foto',
            'image' => UploadedFile::fake()->image('awal.jpg'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $product = Product::where('name', 'Produk Foto')->firstOrFail();
        $oldImage = $product->image;
        Storage::disk('public')->assertExists($oldImage);

        $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Produk Foto',
            'image' => UploadedFile::fake()->image('baru.jpg'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($product->fresh()->image);

        $componentProduct = Product::where('slug', 'canting')->firstOrFail();
        $this->delete(route('admin.products.destroy', $componentProduct))
            ->assertSessionHasErrors('delete');
        $this->assertModelExists($componentProduct);
    }

    public function test_pending_orders_protect_bundle_components_and_variants(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('role', 'admin')->firstOrFail());

        $bundle = Product::where('slug', 'paket-mahasiswa')->firstOrFail();
        $variant = $bundle->variants()->firstOrFail();
        $component = Product::where('slug', 'canting')->firstOrFail();
        $componentVariant = $component->variants()->firstOrFail();
        $order = Order::factory()->create(['status' => 'pending_payment']);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_name' => $bundle->name,
            'variant_name' => $variant->name,
            'quantity' => 1,
            'unit_price' => $variant->price,
            'subtotal' => $variant->price,
            'stock_quantity' => 1,
        ]);

        $this->delete(route('admin.bundles.destroy', $bundle))->assertSessionHasErrors('delete');
        $this->put(route('admin.bundles.update', $bundle), [])->assertSessionHasErrors('bundle');

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $componentVariant->id,
            'product_name' => $component->name,
            'variant_name' => $componentVariant->name,
            'quantity' => 1,
            'unit_price' => $componentVariant->price,
            'subtotal' => $componentVariant->price,
            'stock_quantity' => 1,
        ]);

        $this->delete(route('admin.variants.destroy', [$component, $componentVariant]))
            ->assertSessionHasErrors('delete');
        $this->assertModelExists($bundle);
        $this->assertModelExists($componentVariant);
    }

    public function test_admin_login_and_customer_crud(): void
    {
        $admin = User::factory()->admin()->create();
        $this->get('/dashboard')->assertRedirect('/login');

        $customerAccount = User::factory()->create();
        $this->post('/login', ['email' => $customerAccount->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->post(route('admin.customers.store'), [
            'name' => 'Pelanggan Baru',
            'email' => 'pelanggan@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('admin.customers.index'))->assertSessionHasNoErrors();

        $customer = User::where('email', 'pelanggan@example.com')->firstOrFail();
        $this->assertSame('customer', $customer->role);
        $this->assertNull($customer->email_verified_at);

        $this->put(route('admin.customers.update', $customer), [
            'name' => 'Pelanggan Lama',
            'email' => 'pelanggan@example.com',
        ])->assertRedirect(route('admin.customers.index'))->assertSessionHasNoErrors();

        $customer->email_verified_at = now();
        $customer->save();
        $customer->createToken('customer-token');
        $this->put(route('admin.customers.update', $customer), [
            'name' => 'Pelanggan Lama',
            'email' => 'alamatbaru@example.com',
        ])->assertRedirect(route('admin.customers.index'))->assertSessionHasNoErrors();
        $this->assertNull($customer->fresh()->email_verified_at);
        $this->assertSame(0, $customer->tokens()->count());

        $customOrder = CustomOrder::factory()->create(['user_id' => $customer->id]);
        $this->delete(route('admin.customers.destroy', $customer))
            ->assertSessionHasErrors('delete');
        $customOrder->delete();

        $this->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect(route('admin.customers.index'))->assertSessionHasNoErrors();
        $this->assertModelMissing($customer);
    }

    public function test_promo_and_voucher_crud(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->post(route('admin.date.store'), [
            'name' => 'Promo Besar',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'start_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'is_active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $promo = DatePromo::where('name', 'Promo Besar')->firstOrFail();
        $this->put(route('admin.date.update', $promo), [
            'name' => 'Promo Besar',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'start_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'is_active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->patch(route('admin.date.toggle', $promo))->assertRedirect();
        $this->assertFalse($promo->fresh()->is_active);
        $this->delete(route('admin.date.destroy', $promo))->assertRedirect();

        $this->post(route('admin.voucher.store'), [
            'code' => 'PROMOHEMAT',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'min_purchase' => 50000,
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addMonth()->format('Y-m-d'),
            'is_active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $voucher = Voucher::where('code', 'PROMOHEMAT')->firstOrFail();
        $this->put(route('admin.voucher.update', $voucher), [
            'code' => 'PROMOHEMAT',
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'min_purchase' => 50000,
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addMonth()->format('Y-m-d'),
            'is_active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->patch(route('admin.voucher.toggle', $voucher))->assertRedirect();
        $this->assertFalse($voucher->fresh()->is_active);
        $this->delete(route('admin.voucher.destroy', $voucher))->assertRedirect();
    }

    public function test_admin_cannot_cancel_or_reprice_an_order_outside_payment_flow(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $order = Order::factory()->create(['status' => 'pending_payment']);

        $this->from(route('admin.orders.show', $order))
            ->patch(route('admin.orders.status.update', $order), ['status' => 'cancelled'])
            ->assertSessionHasErrors('status');
        $this->assertSame('pending_payment', $order->fresh()->status);

        $order->update(['status' => 'paid']);
        $originalAmount = $order->final_amount;

        $this->patch(route('admin.orders.status.update', $order), ['status' => 'processing'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('processing', $order->fresh()->status);

        $this->patch(route('admin.orders.shipping.update', $order), [
            'shipping_courier' => 'jnt',
            'resi_number' => 'TEST123',
            'shipping_cost' => $order->shipping_cost + 1000,
        ])->assertSessionHasErrors('shipping_cost');
        $this->assertSame($originalAmount, $order->fresh()->final_amount);

        $this->patch(route('admin.orders.shipping.update', $order), [
            'shipping_courier' => 'jnt',
            'resi_number' => 'TEST123',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('TEST123', $order->fresh()->resi_number);
    }

    public function test_custom_order_detail_quote_and_rejection(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $customOrder = CustomOrder::factory()->create(['status' => 'pending_review']);

        $this->get(route('admin.custom-orders.show', $customOrder))
            ->assertOk()
            ->assertSee($customOrder->reference_number);

        $this->patch(route('admin.custom-orders.quote', $customOrder), [
            'estimated_price' => 250000,
            'estimated_time' => '7 hari',
        ])->assertRedirect(route('admin.custom-orders.index'))->assertSessionHasNoErrors();
        $this->assertSame('quoted', $customOrder->fresh()->status);

        $another = CustomOrder::factory()->create(['status' => 'pending_review']);
        $this->patch(route('admin.custom-orders.reject', $another), [
            'admin_notes' => 'Tidak dapat diproduksi.',
        ])->assertRedirect(route('admin.custom-orders.index'))->assertSessionHasNoErrors();
        $this->assertSame('rejected', $another->fresh()->status);
    }

    public function test_customer_cannot_use_admin_crud_and_variant_ownership_is_checked(): void
    {
        $category = Category::factory()->bahan()->create();
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Produk Terlarang',
            ])->assertRedirect(route('login'));
        $this->assertDatabaseMissing('products', ['name' => 'Produk Terlarang']);

        $this->actingAs(User::factory()->admin()->create());
        $first = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Pertama',
            'slug' => 'produk-pertama',
        ]);
        $second = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Kedua',
            'slug' => 'produk-kedua',
        ]);
        $variant = $first->variants()->create([
            'name' => 'Satuan',
            'sku' => 'PERTAMA',
            'price' => 10000,
            'stock' => 3,
        ]);

        $this->delete(route('admin.variants.destroy', [$second, $variant]))->assertForbidden();
        $this->assertModelExists($variant);
    }
}
