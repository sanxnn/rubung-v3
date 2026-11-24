<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderVoucherPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_voucher_preview_uses_the_customer_cart_and_preserves_quota(): void
    {
        $cart = Cart::factory()->create();
        $variant = ProductVariant::factory()->create(['price' => 75000]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);
        $voucher = Voucher::factory()->create([
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'min_purchase' => 100000,
            'max_discount' => null,
            'start_date' => now()->subDay(),
            'usage_limit' => 1,
        ]);

        $this->actingAs($cart->user, 'sanctum')->postJson('/api/vouchers/validate', [
            'code' => $voucher->code,
            'subtotal' => 1000000,
        ])->assertUnprocessable();

        $item->update(['quantity' => 2]);

        $this->postJson('/api/vouchers/validate', ['code' => $voucher->code])
            ->assertOk()
            ->assertJsonPath('data.subtotal', 150000)
            ->assertJsonPath('data.subtotal_after_voucher', 140000);
        $this->assertSame(0, $voucher->fresh()->used_count);
    }
}
