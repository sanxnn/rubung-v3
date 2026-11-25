<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DatePromo;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Voucher;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_late_webhooks_cannot_reverse_a_settled_payment(): void
    {
        config()->set('midtrans.server_key', 'test-server-key');

        $order = Order::factory()->create([
            'status' => 'pending_payment',
            'final_amount' => 150000,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'payment_status' => 'pending',
            'amount' => 150000,
            'paid_at' => null,
        ]);

        $this->postJson('/api/payments/midtrans/webhook', $this->notification($order, 'settlement'))
            ->assertOk();

        $paidAt = $order->payment()->firstOrFail()->paid_at;
        $this->assertSame('paid', $order->fresh()->status);

        foreach (['pending', 'expire', 'settlement'] as $status) {
            $this->postJson('/api/payments/midtrans/webhook', $this->notification($order, $status))
                ->assertOk();
        }

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('success', $order->payment()->firstOrFail()->payment_status);
        $this->assertEquals($paidAt, $order->payment()->firstOrFail()->paid_at);
    }

    public function test_report_totals_exclude_unpaid_orders_and_respect_date_filters(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->create([
            'status' => 'pending_payment',
            'final_amount' => 100000,
            'discount_voucher' => 20000,
            'shipping_cost' => 15000,
        ]);
        Order::factory()->create([
            'status' => 'paid',
            'final_amount' => 250000,
            'discount_date_cantik' => 10000,
            'discount_voucher' => 5000,
            'shipping_cost' => 20000,
        ]);
        Order::factory()->create([
            'status' => 'paid',
            'final_amount' => 300000,
            'discount_voucher' => 40000,
            'shipping_cost' => 25000,
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.index', ['start_date' => today()->toDateString()]))
            ->assertOk()
            ->assertViewHas('totalOrders', 2)
            ->assertViewHas('totalRevenue', 250000)
            ->assertViewHas('totalDiscount', fn ($total) => (int) $total === 15000)
            ->assertViewHas('totalShipping', 20000);

        $this->get(route('admin.reports.index', ['status' => 'pending_payment']))
            ->assertOk()
            ->assertViewHas('totalDiscount', fn ($total) => (int) $total === 0);
    }

    public function test_webhook_with_invalid_signature_does_not_change_order(): void
    {
        config()->set('midtrans.server_key', 'test-server-key');
        $order = Order::factory()->create(['status' => 'pending_payment']);
        $notification = $this->notification($order, 'settlement');
        $notification['signature_key'] = str_repeat('0', 128);

        $this->postJson('/api/payments/midtrans/webhook', $notification)->assertOk();

        $this->assertSame('pending_payment', $order->fresh()->status);
        $this->assertNull($order->payment);
    }

    public function test_customer_can_cancel_an_order_before_payment_is_created(): void
    {
        $order = Order::factory()->create(['status' => 'pending_payment']);

        $this->actingAs($order->user, 'sanctum')
            ->postJson('/api/orders/'.$order->id.'/cancel')
            ->assertOk();

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_seeders_can_run_twice_without_duplicate_catalog_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $counts = [
            Category::count(),
            Subcategory::count(),
            Product::count(),
            Voucher::count(),
            DatePromo::count(),
        ];

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($counts, [
            Category::count(),
            Subcategory::count(),
            Product::count(),
            Voucher::count(),
            DatePromo::count(),
        ]);
    }

    public function test_seeded_catalog_and_admin_dashboard_are_available(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->getJson('/api/categories')->assertOk();
        $this->getJson('/api/products')->assertOk();
        $this->getJson('/api/bundles')->assertOk();

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    private function notification(Order $order, string $status): array
    {
        $amount = number_format($order->final_amount, 2, '.', '');
        $statusCode = '200';

        return [
            'order_id' => $order->order_number,
            'transaction_id' => 'test-transaction-'.$order->id,
            'transaction_status' => $status,
            'status_code' => $statusCode,
            'gross_amount' => $amount,
            'payment_type' => 'bank_transfer',
            'signature_key' => hash('sha512', $order->order_number.$statusCode.$amount.config('midtrans.server_key')),
        ];
    }
}
