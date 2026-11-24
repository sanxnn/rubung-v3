<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RefundFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_must_own_paid_order_and_supply_reason_and_evidence(): void
    {
        Storage::fake('local');
        $order = Order::factory()->create(['status' => 'pending_payment']);
        $customer = $order->user;
        $other = User::factory()->create();
        $url = '/api/orders/'.$order->id.'/refund';

        $this->actingAs($other, 'sanctum')->postJson($url, [
            'reason' => 'Produk rusak saat diterima oleh customer.',
            'evidence' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertNotFound();

        $this->actingAs($customer, 'sanctum')->postJson($url, [
            'reason' => 'Pendek',
        ])->assertUnprocessable()->assertJsonValidationErrors(['reason', 'evidence']);

        $this->postJson($url, [
            'reason' => 'Produk rusak saat diterima oleh customer.',
            'evidence' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertUnprocessable();

        $this->assertDatabaseCount('refund_requests', 0);
    }

    public function test_customer_submits_private_evidence_and_admin_can_reject_then_customer_resubmit(): void
    {
        Storage::fake('local');
        $order = $this->paidOrder();
        $customer = $order->user;
        $url = '/api/orders/'.$order->id.'/refund';

        $created = $this->actingAs($customer, 'sanctum')->postJson($url, [
            'reason' => 'Produk rusak pada sisi kanan saat diterima.',
            'evidence' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertCreated()->assertJsonPath('data.status', 'pending');

        $refund = RefundRequest::findOrFail($created->json('data.id'));
        Storage::disk('local')->assertExists($refund->evidence_path);
        $this->getJson('/api/refunds/'.$refund->id.'/evidence')->assertOk();
        $this->getJson('/api/orders/'.$order->id)->assertJsonPath('data.refund.status', 'pending');
        $this->postJson($url, [
            'reason' => 'Produk rusak pada sisi kanan saat diterima.',
            'evidence' => UploadedFile::fake()->image('bukti2.jpg'),
        ])->assertUnprocessable();

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/refunds/'.$refund->id.'/evidence')->assertNotFound();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.refunds.show', $refund))->assertOk();
        $this->get(route('admin.refunds.evidence', $refund))->assertOk();
        $this->post(route('admin.refunds.reject', $refund), [
            'admin_notes' => 'Foto belum menunjukkan kerusakan produk dengan jelas.',
        ])->assertRedirect();
        $this->assertSame('rejected', $refund->fresh()->status);

        $this->actingAs($customer, 'sanctum')->postJson($url, [
            'reason' => 'Foto baru menunjukkan kerusakan pada sisi kanan produk.',
            'evidence' => UploadedFile::fake()->image('bukti-baru.jpg'),
        ])->assertCreated();
        $this->assertDatabaseCount('refund_requests', 2);
    }

    public function test_manual_refund_completes_once_and_reduces_revenue(): void
    {
        Storage::fake('local');
        $order = $this->paidOrder();
        $refund = $this->submitRefund($order);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.refunds.approve', $refund), [
            'method' => 'manual',
            'admin_notes' => 'Pengajuan sesuai bukti.',
        ])->assertRedirect();
        $this->assertSame('approved', $refund->fresh()->status);

        $this->post(route('admin.refunds.complete', $refund), [
            'manual_reference' => 'TRF-20261118-001',
        ])->assertRedirect();
        $this->assertSame('completed', $refund->fresh()->status);
        $this->assertSame('TRF-20261118-001', $refund->fresh()->manual_reference);
        $this->post(route('admin.refunds.complete', $refund), [
            'manual_reference' => 'TRF-SECOND',
        ])->assertSessionHasErrors('status');

        $expectedRevenue = Order::whereIn('status', ['paid', 'processing', 'packing', 'shipped', 'completed'])
            ->sum('final_amount') - $order->final_amount;
        $this->get(route('admin.reports.index'))
            ->assertOk()->assertViewHas('totalRefunded', $order->final_amount)
            ->assertViewHas('totalRevenue', $expectedRevenue);
        $this->get(route('admin.reports.export'))->assertOk();
    }

    public function test_midtrans_sandbox_refund_waits_for_verified_bank_confirmation(): void
    {
        Storage::fake('local');
        config()->set('midtrans.server_key', 'sandbox-test-key');
        config()->set('midtrans.is_production', false);
        $order = $this->paidOrder('gopay');
        $refund = $this->submitRefund($order);
        $admin = User::factory()->admin()->create();
        $key = 'BRK-REF-'.$refund->id;

        Http::fake([
            '*api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'status_code' => '200', 'transaction_status' => 'settlement',
            ]),
            '*api.sandbox.midtrans.com/v2/*/refund' => Http::response([
                'status_code' => '200', 'transaction_status' => 'refund', 'refund_key' => $key,
            ]),
        ]);

        $this->actingAs($admin)->post(route('admin.refunds.approve', $refund), [
            'method' => 'midtrans',
        ])->assertRedirect();
        $this->assertSame('processing', $refund->fresh()->status);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.sandbox.midtrans.com')
            && $request->method() === 'POST'
            && $request['refund_key'] === $key
            && $request['amount'] === $order->final_amount);

        $notification = $this->notification($order, $key, false);
        $this->postJson('/api/payments/midtrans/webhook', $notification)->assertOk();
        $this->assertSame('processing', $refund->fresh()->status);

        $notification['refunds'][0]['bank_confirmed_at'] = '2026-11-18 12:00:00';
        $invalid = $notification;
        $invalid['signature_key'] = str_repeat('0', 128);
        $this->postJson('/api/payments/midtrans/webhook', $invalid)->assertOk();
        $this->assertSame('processing', $refund->fresh()->status);

        $this->postJson('/api/payments/midtrans/webhook', $notification)->assertOk();
        $this->postJson('/api/payments/midtrans/webhook', $notification)->assertOk();
        $this->assertSame('completed', $refund->fresh()->status);
        $this->assertSame('success', $order->payment->fresh()->payment_status);
    }

    public function test_unsupported_midtrans_method_stays_pending_and_can_be_approved_manually(): void
    {
        Storage::fake('local');
        $order = $this->paidOrder('bank_transfer');
        $refund = $this->submitRefund($order);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.refunds.approve', $refund), [
            'method' => 'midtrans',
        ])->assertSessionHasErrors('method');
        $this->assertSame('pending', $refund->fresh()->status);

        $this->post(route('admin.refunds.approve', $refund), [
            'method' => 'manual',
        ])->assertRedirect();
        $this->assertSame('approved', $refund->fresh()->status);
    }

    public function test_failed_midtrans_request_can_switch_to_manual_after_status_check(): void
    {
        Storage::fake('local');
        config()->set('midtrans.server_key', 'sandbox-test-key');
        config()->set('midtrans.is_production', false);
        $order = $this->paidOrder('gopay');
        $refund = $this->submitRefund($order);
        $admin = User::factory()->admin()->create();

        Http::fake([
            '*api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'status_code' => '200', 'transaction_status' => 'settlement',
            ]),
            '*api.sandbox.midtrans.com/v2/*/refund' => Http::response([
                'status_code' => '412', 'status_message' => 'Refund unavailable',
            ], 412),
        ]);

        $this->actingAs($admin)->post(route('admin.refunds.approve', $refund), [
            'method' => 'midtrans',
        ])->assertRedirect();
        $this->assertSame('approved', $refund->fresh()->status);

        $this->post(route('admin.refunds.manual', $refund))->assertRedirect();
        $this->assertSame('manual', $refund->fresh()->refund_method);
        $this->assertSame('approved', $refund->fresh()->status);
    }

    public function test_uncertain_midtrans_response_blocks_manual_fallback(): void
    {
        Storage::fake('local');
        config()->set('midtrans.server_key', 'sandbox-test-key');
        config()->set('midtrans.is_production', false);
        $order = $this->paidOrder('gopay');
        $refund = $this->submitRefund($order);
        $admin = User::factory()->admin()->create();

        Http::fake([
            '*api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'status_code' => '200', 'transaction_status' => 'settlement',
            ]),
            '*api.sandbox.midtrans.com/v2/*/refund' => Http::response([], 500),
        ]);

        $this->actingAs($admin)->post(route('admin.refunds.approve', $refund), [
            'method' => 'midtrans',
        ])->assertRedirect();

        $this->assertSame('processing', $refund->fresh()->status);
        $this->post(route('admin.refunds.manual', $refund))->assertUnprocessable();
        $this->assertSame('midtrans', $refund->fresh()->refund_method);
    }

    private function paidOrder(string $type = 'bank_transfer'): Order
    {
        $order = Order::factory()->create(['status' => 'paid', 'final_amount' => 150000]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'payment_type' => $type,
            'payment_status' => 'success',
            'amount' => 150000,
            'paid_at' => now(),
        ]);

        return $order;
    }

    private function submitRefund(Order $order): RefundRequest
    {
        $this->actingAs($order->user, 'sanctum')->postJson('/api/orders/'.$order->id.'/refund', [
            'reason' => 'Produk rusak saat diterima dan perlu pengembalian dana.',
            'evidence' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertCreated();

        return RefundRequest::firstOrFail();
    }

    private function notification(Order $order, string $key, bool $confirmed): array
    {
        $amount = number_format($order->final_amount, 2, '.', '');

        return [
            'order_id' => $order->order_number,
            'transaction_status' => 'refund',
            'status_code' => '200',
            'gross_amount' => $amount,
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.config('midtrans.server_key')),
            'refunds' => [[
                'refund_key' => $key,
                'refund_amount' => $amount,
                'bank_confirmed_at' => $confirmed ? '2026-11-18 12:00:00' : null,
            ]],
        ];
    }
}
