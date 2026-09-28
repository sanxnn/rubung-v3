<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function create(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(404);
        }

        if ($order->status !== 'pending_payment') {
            return response()->json([
                'message' =>
                    'Pesanan tidak menunggu pembayaran.',
            ], 422);
        }

        if ($order->final_amount <= 0) {
            return response()->json([
                'message' =>
                    'Total pembayaran tidak valid.',
            ], 422);
        }

        $existingPayment = $order->payment;

        if (
            $existingPayment &&
            $existingPayment->payment_status === 'pending'
        ) {
            return response()->json([
                'message' =>
                    'Pembayaran sudah dibuat.',
                'data' => [
                    'payment_status' =>
                        $existingPayment->payment_status,
                    'snap_token' => null,
                ],
            ]);
        }

        $serverKey = config('midtrans.server_key');

        if (!$serverKey) {
            return response()->json([
                'message' =>
                    'Konfigurasi pembayaran belum tersedia.',
            ], 500);
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction =
            config('midtrans.is_production');

        Config::$isSanitized = true;
        Config::$is3ds = true;

        $order->load([
            'user',
            'items',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ITEM DETAILS
        |--------------------------------------------------------------------------
        |
        | Total item Midtrans harus sama dengan final_amount.
        |
        */

        $itemDetails = [];

        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => (string) (
                    $item->product_variant_id
                    ?? $item->id
                ),

                'price' => $item->unit_price,

                'quantity' => $item->quantity,

                'name' => mb_substr(
                    $item->product_name .
                    ' - ' .
                    $item->variant_name,
                    0,
                    50
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DATE CANTIK DISCOUNT
        |--------------------------------------------------------------------------
        */

        if ($order->discount_date_cantik > 0) {
            $itemDetails[] = [
                'id' => 'discount_date_cantik',
                'price' => -$order->discount_date_cantik,
                'quantity' => 1,
                'name' => 'Diskon Tanggal Cantik',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VOUCHER DISCOUNT
        |--------------------------------------------------------------------------
        */

        if ($order->discount_voucher > 0) {
            $itemDetails[] = [
                'id' => 'discount_voucher',
                'price' => -$order->discount_voucher,
                'quantity' => 1,
                'name' => 'Diskon Voucher',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SHIPPING
        |--------------------------------------------------------------------------
        */

        if ($order->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'shipping',
                'price' => $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE TOTAL ITEM
        |--------------------------------------------------------------------------
        */

        $itemTotal = collect($itemDetails)->sum(
            fn ($item) =>
                $item['price'] * $item['quantity']
        );

        if ($itemTotal !== $order->final_amount) {
            return response()->json([
                'message' =>
                    'Total pembayaran tidak sesuai.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | MIDTRANS PARAMS
        |--------------------------------------------------------------------------
        */

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->final_amount,
            ],

            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],

            'item_details' => $itemDetails,
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'message' =>
                'Pembayaran berhasil dibuat.',

            'data' => [
                'order_number' =>
                    $order->order_number,

                'amount' =>
                    $order->final_amount,

                'snap_token' =>
                    $snapToken,
            ],
        ]);
    }

    public function notification(Request $request)
    {
        Config::$serverKey =
            config('midtrans.server_key');

        Config::$isProduction =
            config('midtrans.is_production');

        /*
        |--------------------------------------------------------------------------
        | MIDTRANS NOTIFICATION
        |--------------------------------------------------------------------------
        */

        $notification =
            new \Midtrans\Notification();

        $orderNumber =
            $notification->order_id;

        $transactionStatus =
            $notification->transaction_status;

        $fraudStatus =
            $notification->fraud_status;

        /*
        |--------------------------------------------------------------------------
        | FIND ORDER
        |--------------------------------------------------------------------------
        */

        $order = Order::query()
            ->where(
                'order_number',
                $orderNumber
            )
            ->first();

        if (!$order) {
            return response()->json([
                'message' =>
                    'Order tidak ditemukan.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY SIGNATURE
        |--------------------------------------------------------------------------
        */

        $expectedSignature = hash(
            'sha512',
            $notification->order_id .
            $notification->status_code .
            $notification->gross_amount .
            config('midtrans.server_key')
        );

        if (
            !hash_equals(
                $expectedSignature,
                $notification->signature_key
            )
        ) {
            return response()->json([
                'message' =>
                    'Signature tidak valid.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY AMOUNT
        |--------------------------------------------------------------------------
        */

        $grossAmount = (int) round(
            (float) $notification->gross_amount
        );

        if ($grossAmount !== $order->final_amount) {
            return response()->json([
                'message' =>
                    'Nominal pembayaran tidak sesuai.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        if (
            $transactionStatus === 'settlement' ||
            (
                $transactionStatus === 'capture' &&
                $fraudStatus === 'accept'
            )
        ) {
            DB::transaction(function () use (
                $order,
                $notification
            ) {
                $order = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                /*
                | Jangan menurunkan status order yang
                | sudah diproses lebih lanjut.
                */

                Payment::updateOrCreate(
                    [
                        'order_id' => $order->id,
                    ],
                    [
                        'midtrans_transaction_id' =>
                            $notification->transaction_id,

                        'payment_type' =>
                            $notification->payment_type
                            ?? 'unknown',

                        'payment_status' =>
                            'success',

                        'amount' =>
                            $order->final_amount,

                        'paid_at' =>
                            now(),
                    ]
                );

                if (
                    $order->status ===
                    'pending_payment'
                ) {
                    $order->update([
                        'status' => 'paid',
                    ]);
                }
            });

            return response()->json([
                'message' =>
                    'Pembayaran berhasil.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PENDING
        |--------------------------------------------------------------------------
        */

        if ($transactionStatus === 'pending') {
            Payment::updateOrCreate(
                [
                    'order_id' => $order->id,
                ],
                [
                    'midtrans_transaction_id' =>
                        $notification->transaction_id,

                    'payment_type' =>
                        $notification->payment_type
                        ?? 'unknown',

                    'payment_status' =>
                        'pending',

                    'amount' =>
                        $order->final_amount,

                    'paid_at' =>
                        null,
                ]
            );

            return response()->json([
                'message' =>
                    'Pembayaran masih pending.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FAILED / EXPIRED
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $transactionStatus,
                [
                    'deny',
                    'cancel',
                    'expire',
                ],
                true
            )
        ) {
            DB::transaction(function () use (
                $order,
                $notification,
                $transactionStatus
            ) {
                $order = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                Payment::updateOrCreate(
                    [
                        'order_id' => $order->id,
                    ],
                    [
                        'midtrans_transaction_id' =>
                            $notification->transaction_id,

                        'payment_type' =>
                            $notification->payment_type
                            ?? 'unknown',

                        'payment_status' =>
                            $transactionStatus === 'expire'
                                ? 'expire'
                                : 'failed',

                        'amount' =>
                            $order->final_amount,

                        'paid_at' =>
                            null,
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | RESTORE ONLY IF STILL PENDING
                |--------------------------------------------------------------------------
                */

                if (
                    $order->status ===
                    'pending_payment'
                ) {
                    $restored =
                        $this->restoreOrderStock($order);

                    if ($restored) {
                        $this->restoreVoucherUsage($order);
                    }

                    $order->update([
                        'status' => 'cancelled',
                    ]);
                }
            });

            return response()->json([
                'message' =>
                    'Pembayaran gagal atau expired.',
            ]);
        }

        return response()->json([
            'message' =>
                'Notification diterima.',
        ]);
    }

    private function restoreOrderStock(
        Order $order
    ): bool {
        /*
        |--------------------------------------------------------------------------
        | SUDAH DIRESTORE
        |--------------------------------------------------------------------------
        */

        if ($order->stock_released_at !== null) {
            return false;
        }

        $order->load('items');

        foreach ($order->items as $item) {
            if (
                !$item->product_variant_id ||
                $item->stock_quantity <= 0
            ) {
                continue;
            }

            $variant = ProductVariant::query()
                ->lockForUpdate()
                ->find(
                    $item->product_variant_id
                );

            if (!$variant) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | BUNDLE
            |--------------------------------------------------------------------------
            */

            $components = ProductBundleItem::query()
                ->where(
                    'parent_variant_id',
                    $variant->id
                )
                ->get();

            if ($components->isNotEmpty()) {
                foreach ($components as $component) {
                    $componentVariant =
                        ProductVariant::query()
                            ->lockForUpdate()
                            ->find(
                                $component->component_variant_id
                            );

                    if (!$componentVariant) {
                        continue;
                    }

                    $restoreQuantity =
                        $component->quantity *
                        $item->stock_quantity;

                    $componentVariant->increment(
                        'stock',
                        $restoreQuantity
                    );
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PRODUK BIASA
            |--------------------------------------------------------------------------
            */

            $variant->increment(
                'stock',
                $item->stock_quantity
            );
        }

        $order->update([
            'stock_released_at' => now(),
        ]);

        return true;
    }

    private function restoreVoucherUsage(
        Order $order
    ): void {
        if (!$order->voucher_id) {
            return;
        }

        $voucher = Voucher::query()
            ->lockForUpdate()
            ->find($order->voucher_id);

        if (!$voucher) {
            return;
        }

        if ($voucher->used_count > 0) {
            $voucher->decrement(
                'used_count'
            );
        }
    }
}
