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
    public function create(
        Request $request,
        Order $order
    ) {
        /*
        |--------------------------------------------------------------------------
        | OWNERSHIP
        |--------------------------------------------------------------------------
        */

        if (
            $order->user_id !==
            $request->user()->id
        ) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $order->status !==
            'pending_payment'
        ) {
            return response()->json([
                'message' =>
                    'Pesanan tidak menunggu pembayaran.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | AMOUNT
        |--------------------------------------------------------------------------
        */

        if (
            $order->final_amount <= 0
        ) {
            return response()->json([
                'message' =>
                    'Total pembayaran tidak valid.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | EXISTING PAYMENT
        |--------------------------------------------------------------------------
        */

        $existingPayment =
            $order->payment;

        if (
            $existingPayment &&
            $existingPayment->payment_status ===
            'pending' &&
            $existingPayment->snap_token
        ) {
            return response()->json([
                'message' =>
                    'Pembayaran sudah dibuat.',

                'data' => [
                    'payment_status' =>
                        $existingPayment
                            ->payment_status,

                    'amount' =>
                        $existingPayment
                            ->amount,

                    'snap_token' =>
                        $existingPayment
                            ->snap_token,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | MIDTRANS CONFIG
        |--------------------------------------------------------------------------
        */

        $serverKey =
            config('midtrans.server_key');

        if (!$serverKey) {
            return response()->json([
                'message' =>
                    'Konfigurasi pembayaran belum tersedia.',
            ], 500);
        }

        Config::$serverKey =
            $serverKey;

        Config::$isProduction =
            config(
                'midtrans.is_production'
            );

        Config::$isSanitized = true;

        Config::$is3ds = true;

        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER
        |--------------------------------------------------------------------------
        */

        $order->load([
            'user',
            'items',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ITEM DETAILS
        |--------------------------------------------------------------------------
        */

        $itemDetails = [];

        foreach (
            $order->items as $item
        ) {
            $itemDetails[] = [
                'id' => (string) (
                    $item->product_variant_id
                    ?? $item->id
                ),

                'price' =>
                    (int) $item->unit_price,

                'quantity' =>
                    (int) $item->quantity,

                'name' =>
                    mb_substr(
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
        | DISCOUNT DATE CANTIK
        |--------------------------------------------------------------------------
        */

        if (
            $order->discount_date_cantik > 0
        ) {
            $itemDetails[] = [
                'id' =>
                    'discount_date_cantik',

                'price' =>
                    -$order
                        ->discount_date_cantik,

                'quantity' => 1,

                'name' =>
                    'Diskon Tanggal Cantik',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DISCOUNT VOUCHER
        |--------------------------------------------------------------------------
        */

        if (
            $order->discount_voucher > 0
        ) {
            $itemDetails[] = [
                'id' =>
                    'discount_voucher',

                'price' =>
                    -$order
                        ->discount_voucher,

                'quantity' => 1,

                'name' =>
                    'Diskon Voucher',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SHIPPING
        |--------------------------------------------------------------------------
        */

        if (
            $order->shipping_cost > 0
        ) {
            $itemDetails[] = [
                'id' =>
                    'shipping',

                'price' =>
                    $order->shipping_cost,

                'quantity' => 1,

                'name' =>
                    'Ongkos Kirim J&T',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE TOTAL
        |--------------------------------------------------------------------------
        */

        $itemTotal =
            collect($itemDetails)
                ->sum(
                    fn ($item) =>
                        $item['price'] *
                        $item['quantity']
                );

        if (
            $itemTotal !==
            (int) $order->final_amount
        ) {
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
                'order_id' =>
                    $order->order_number,

                'gross_amount' =>
                    (int) $order->final_amount,
            ],

            'customer_details' => [
                'first_name' =>
                    $order->user->name,

                'email' =>
                    $order->user->email,

                'phone' =>
                    $order->user->phone,
            ],

            'item_details' =>
                $itemDetails,
        ];

        /*
        |--------------------------------------------------------------------------
        | CREATE SNAP TOKEN
        |--------------------------------------------------------------------------
        */

        try {
            $snapToken =
                Snap::getSnapToken(
                    $params
                );
        } catch (
            \Throwable $e
        ) {
            return response()->json([
                'message' =>
                    'Gagal membuat pembayaran Midtrans.',
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE PAYMENT
        |--------------------------------------------------------------------------
        */

        Payment::updateOrCreate(
            [
                'order_id' =>
                    $order->id,
            ],
            [
                'midtrans_transaction_id' =>
                    null,

                'snap_token' =>
                    $snapToken,

                'payment_type' =>
                    'pending',

                'payment_status' =>
                    'pending',

                'amount' =>
                    $order->final_amount,

                'paid_at' =>
                    null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

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

    /*
    |--------------------------------------------------------------------------
    | MIDTRANS WEBHOOK
    |--------------------------------------------------------------------------
    */

    public function notification(
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | MIDTRANS CONFIG
        |--------------------------------------------------------------------------
        */

        Config::$serverKey =
            config('midtrans.server_key');

        Config::$isProduction =
            config('midtrans.is_production');

        /*
        |--------------------------------------------------------------------------
        | GET JSON PAYLOAD
        |--------------------------------------------------------------------------
        */

        $payload =
            $request->all();

        /*
        |--------------------------------------------------------------------------
        | EMPTY REQUEST
        |--------------------------------------------------------------------------
        |
        | Dipakai ketika endpoint dicek oleh Midtrans.
        |
        */

        if (empty($payload)) {
            return response()->json([
                'message' =>
                    'Notification endpoint aktif.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | REQUIRED DATA
        |--------------------------------------------------------------------------
        */

        $requiredFields = [
            'order_id',
            'transaction_status',
            'status_code',
            'gross_amount',
            'signature_key',
        ];

        foreach (
            $requiredFields as $field
        ) {
            if (
                !isset($payload[$field]) ||
                $payload[$field] === ''
            ) {
                return response()->json([
                    'message' =>
                        'Data notification tidak lengkap.',
                ], 200);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PAYLOAD DATA
        |--------------------------------------------------------------------------
        */

        $orderNumber =
            $payload['order_id'];

        $transactionStatus =
            $payload['transaction_status'];

        $fraudStatus =
            $payload['fraud_status'] ?? null;

        $transactionId =
            $payload['transaction_id'] ?? null;

        $paymentType =
            $payload['payment_type'] ?? 'unknown';

        /*
        |--------------------------------------------------------------------------
        | FIND ORDER
        |--------------------------------------------------------------------------
        */

        $order =
            Order::query()
                ->where(
                    'order_number',
                    $orderNumber
                )
                ->first();

        if (!$order) {
            return response()->json([
                'message' =>
                    'Order tidak ditemukan.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY SIGNATURE
        |--------------------------------------------------------------------------
        */

        $expectedSignature =
            hash(
                'sha512',
                $payload['order_id'] .
                $payload['status_code'] .
                $payload['gross_amount'] .
                config('midtrans.server_key')
            );

        if (
            !hash_equals(
                $expectedSignature,
                $payload['signature_key']
            )
        ) {
            return response()->json([
                'message' =>
                    'Signature tidak valid.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY AMOUNT
        |--------------------------------------------------------------------------
        */

        $grossAmount =
            (int) round(
                (float) $payload['gross_amount']
            );

        if (
            $grossAmount !==
            (int) $order->final_amount
        ) {
            return response()->json([
                'message' =>
                    'Nominal pembayaran tidak sesuai.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS / SETTLEMENT
        |--------------------------------------------------------------------------
        */

        if (
            $transactionStatus === 'settlement' ||
            (
                $transactionStatus === 'capture' &&
                $fraudStatus === 'accept'
            )
        ) {
            DB::transaction(
                function () use (
                    $order,
                    $transactionId,
                    $paymentType
                ) {
                    $order =
                        Order::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $order->id
                            );

                    Payment::updateOrCreate(
                        [
                            'order_id' =>
                                $order->id,
                        ],
                        [
                            'midtrans_transaction_id' =>
                                $transactionId,

                            'payment_type' =>
                                $paymentType,

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
                            'status' =>
                                'paid',
                        ]);
                    }
                }
            );

            return response()->json([
                'message' =>
                    'Pembayaran berhasil.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | PENDING
        |--------------------------------------------------------------------------
        */

        if (
            $transactionStatus === 'pending'
        ) {
            Payment::updateOrCreate(
                [
                    'order_id' =>
                        $order->id,
                ],
                [
                    'midtrans_transaction_id' =>
                        $transactionId,

                    'payment_type' =>
                        $paymentType,

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
            ], 200);
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
            DB::transaction(
                function () use (
                    $order,
                    $transactionId,
                    $paymentType,
                    $transactionStatus
                ) {
                    $order =
                        Order::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $order->id
                            );

                    Payment::updateOrCreate(
                        [
                            'order_id' =>
                                $order->id,
                        ],
                        [
                            'midtrans_transaction_id' =>
                                $transactionId,

                            'payment_type' =>
                                $paymentType,

                            'payment_status' =>
                                $transactionStatus ===
                                'expire'
                                    ? 'expire'
                                    : 'failed',

                            'amount' =>
                                $order->final_amount,

                            'paid_at' =>
                                null,
                        ]
                    );

                    if (
                        $order->status ===
                        'pending_payment'
                    ) {
                        $restored =
                            $this->restoreOrderStock(
                                $order
                            );

                        if ($restored) {
                            $this->restoreVoucherUsage(
                                $order
                            );
                        }

                        $order->update([
                            'status' =>
                                'cancelled',
                        ]);
                    }
                }
            );

            return response()->json([
                'message' =>
                    'Pembayaran gagal atau expired.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | OTHER STATUS
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' =>
                'Notification diterima.',
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE STOCK
    |--------------------------------------------------------------------------
    */

    private function restoreOrderStock(
        Order $order
    ): bool {
        if (
            $order->stock_released_at !==
            null
        ) {
            return false;
        }

        $order->load('items');

        foreach (
            $order->items as $item
        ) {
            if (
                !$item->product_variant_id ||
                $item->stock_quantity <= 0
            ) {
                continue;
            }

            $variant =
                ProductVariant::query()
                    ->lockForUpdate()
                    ->find(
                        $item->product_variant_id
                    );

            if (!$variant) {
                continue;
            }

            $components =
                ProductBundleItem::query()
                    ->where(
                        'parent_variant_id',
                        $variant->id
                    )
                    ->get();

            /*
            |--------------------------------------------------------------------------
            | BUNDLE
            |--------------------------------------------------------------------------
            */

            if (
                $components->isNotEmpty()
            ) {
                foreach (
                    $components as $component
                ) {
                    $componentVariant =
                        ProductVariant::query()
                            ->lockForUpdate()
                            ->find(
                                $component
                                    ->component_variant_id
                            );

                    if (
                        !$componentVariant
                    ) {
                        continue;
                    }

                    $restoreQuantity =
                        $component->quantity *
                        $item->stock_quantity;

                    $componentVariant
                        ->increment(
                            'stock',
                            $restoreQuantity
                        );
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NORMAL PRODUCT
            |--------------------------------------------------------------------------
            */

            $variant->increment(
                'stock',
                $item->stock_quantity
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MARK STOCK RELEASED
        |--------------------------------------------------------------------------
        */

        $order->update([
            'stock_released_at' =>
                now(),
        ]);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE VOUCHER
    |--------------------------------------------------------------------------
    */

    private function restoreVoucherUsage(
        Order $order
    ): void {
        if (
            !$order->voucher_id
        ) {
            return;
        }

        $voucher =
            Voucher::query()
                ->lockForUpdate()
                ->find(
                    $order->voucher_id
                );

        if (!$voucher) {
            return;
        }

        if (
            $voucher->used_count > 0
        ) {
            $voucher->decrement(
                'used_count'
            );
        }
    }
}
