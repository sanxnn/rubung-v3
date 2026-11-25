<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DatePromo;
use App\Models\Order;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class CheckoutConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_validates_combined_stock_for_bundle_and_regular_item(): void
    {
        config()->set('midtrans.server_key', null);
        $this->fakeShipping();
        $cart = Cart::factory()->create();
        $address = Address::factory()->create(['user_id' => $cart->user_id]);
        $bundle = ProductVariant::factory()->create(['stock' => 0]);
        $component = ProductVariant::factory()->create(['stock' => 3, 'product_id' => $bundle->product_id]);
        ProductBundleItem::create([
            'parent_variant_id' => $bundle->id,
            'component_variant_id' => $component->id,
            'quantity' => 2,
        ]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_variant_id' => $bundle->id, 'quantity' => 1]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_variant_id' => $component->id, 'quantity' => 2]);

        $this->actingAs($cart->user, 'sanctum')->postJson('/api/orders', ['address_id' => $address->id])
            ->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $cart->items()->count());
        $this->assertSame(3, $component->fresh()->stock);

        $component->update(['stock' => 4]);
        $this->postJson('/api/orders', ['address_id' => $address->id])->assertCreated();
        $this->assertSame(0, $component->fresh()->stock);
        $this->postJson('/api/orders', ['address_id' => $address->id])->assertUnprocessable();
        $this->assertDatabaseCount('orders', 1);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_snap_uses_only_applied_discount_for_new_and_legacy_orders(): void
    {
        config()->set('midtrans.server_key', 'test-key');
        $this->fakeShipping();
        $cart = Cart::factory()->create();
        $address = Address::factory()->create(['user_id' => $cart->user_id]);
        $variant = ProductVariant::factory()->create(['price' => 100000, 'stock' => 10]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id, 'quantity' => 1]);
        DatePromo::factory()->create([
            'discount_type' => 'fixed',
            'discount_value' => 70000,
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->addDay(),
        ]);
        $voucher = Voucher::factory()->create([
            'discount_type' => 'fixed', 'discount_value' => 50000, 'min_purchase' => 0,
        ]);
        Mockery::mock('alias:Midtrans\\Snap')->shouldReceive('getSnapToken')->twice()
            ->withArgs(fn (array $payload) => $payload['transaction_details']['gross_amount'] === 20000
                && collect($payload['item_details'])->firstWhere('id', 'discount_voucher')['price'] === -30000
                && collect($payload['item_details'])->sum(fn ($item) => $item['price'] * $item['quantity']) === 20000)
            ->andReturn('test-snap-token');

        $response = $this->actingAs($cart->user, 'sanctum')->postJson('/api/orders', [
            'address_id' => $address->id,
            'voucher_code' => $voucher->code,
        ])->assertCreated()
            ->assertJsonPath('data.order.pricing.discount_voucher', 30000)
            ->assertJsonPath('data.payment.snap_token', 'test-snap-token');

        $order = Order::findOrFail($response->json('data.order.id'));
        $this->assertSame(30000, $order->discount_voucher);
        $this->assertSame(1, $voucher->fresh()->used_count);

        $order->update(['discount_voucher' => 50000]);
        $order->payment()->delete();
        $this->postJson('/api/payments/'.$order->id.'/snap-token')->assertOk()
            ->assertJsonPath('data.snap_token', 'test-snap-token');
    }

    private function fakeShipping(): void
    {
        config()->set('services.rajongkir.api_key', 'test-key');
        config()->set('services.rajongkir.origin_search', 'Jember');
        Http::preventStrayRequests();
        Http::fake([
            '*domestic-destination*' => Http::response(['data' => [['id' => 1, 'label' => 'Jember']]]),
            '*domestic-cost*' => Http::response(['data' => [['cost' => 20000, 'service' => 'REG']]]),
        ]);
    }
}
