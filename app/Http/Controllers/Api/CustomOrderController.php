<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomOrderResource;
use App\Models\Address;
use App\Models\CustomOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomOrderController extends Controller
{
    /**
     * Daftar custom order milik customer.
     */
    public function index(Request $request)
    {
        $customOrders = CustomOrder::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'attachments',
                'order',
            ])
            ->latest()
            ->paginate(10);

        return CustomOrderResource::collection(
            $customOrders
        );
    }

    /**
     * Detail custom order.
     */
    public function show(
        Request $request,
        CustomOrder $customOrder
    ) {
        if (
            $customOrder->user_id !==
            $request->user()->id
        ) {
            abort(404);
        }

        $customOrder->load([
            'attachments',
            'order',
        ]);

        return new CustomOrderResource(
            $customOrder
        );
    }

    /**
     * Customer membuat custom order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => [
                'required',
                'string',
                'min:10',
            ],
        ]);

        $customOrder = DB::transaction(function () use (
            $request,
            $validated
        ) {
            return CustomOrder::create([
                'user_id' =>
                    $request->user()->id,

                'reference_number' =>
                    $this->generateReferenceNumber(),

                'description' =>
                    $validated['description'],

                'estimated_price' =>
                    null,

                'estimated_time' =>
                    null,

                'status' =>
                    'pending_review',

                'admin_notes' =>
                    null,

                'order_id' =>
                    null,
            ]);
        });

        $customOrder->load([
            'attachments',
            'order',
        ]);

        return (new CustomOrderResource(
            $customOrder
        ))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Customer membatalkan custom order.
     *
     * pending_review -> cancelled
     * quoted         -> cancelled
     */
    public function cancel(
        Request $request,
        CustomOrder $customOrder
    ) {
        if (
            $customOrder->user_id !==
            $request->user()->id
        ) {
            abort(404);
        }

        if (!in_array(
            $customOrder->status,
            [
                'pending_review',
                'quoted',
            ],
            true
        )) {
            return response()->json([
                'message' =>
                    'Custom Order tidak dapat dibatalkan pada status ini.',
            ], 422);
        }

        $customOrder->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' =>
                'Custom Order berhasil dibatalkan.',

            'data' =>
                new CustomOrderResource(
                    $customOrder->fresh([
                        'attachments',
                        'order',
                    ])
                ),
        ]);
    }

    /**
     * Customer menerima quotation.
     *
     * quoted -> approved
     *
     * Setelah approved, langsung membuat Order
     * dengan pembayaran FULL.
     */
    public function approve(
        Request $request,
        CustomOrder $customOrder
    ) {
        if (
            $customOrder->user_id !==
            $request->user()->id
        ) {
            abort(404);
        }

        if ($customOrder->status !== 'quoted') {
            return response()->json([
                'message' =>
                    'Custom Order belum memiliki harga yang dapat disetujui.',
            ], 422);
        }

        if (
            !$customOrder->estimated_price ||
            $customOrder->estimated_price <= 0
        ) {
            return response()->json([
                'message' =>
                    'Harga Custom Order tidak valid.',
            ], 422);
        }

        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                'exists:addresses,id',
            ],

            'shipping_cost' => [
                'required',
                'integer',
                'min:0',
            ],

            'shipping_courier' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $user = $request->user();

        $address = Address::query()
            ->where(
                'id',
                $validated['address_id']
            )
            ->where(
                'user_id',
                $user->id
            )
            ->first();

        if (!$address) {
            return response()->json([
                'message' =>
                    'Alamat tidak ditemukan.',
            ], 404);
        }

        try {
            $order = DB::transaction(
                function () use (
                    $customOrder,
                    $user,
                    $address,
                    $validated
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | LOCK CUSTOM ORDER
                    |--------------------------------------------------------------------------
                    */

                    $customOrder =
                        CustomOrder::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $customOrder->id
                            );

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ULANG STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $customOrder->status !==
                        'quoted'
                    ) {
                        throw new \Exception(
                            'Custom Order sudah diproses.'
                        );
                    }

                    if (
                        !$customOrder->estimated_price ||
                        $customOrder->estimated_price <= 0
                    ) {
                        throw new \Exception(
                            'Harga Custom Order tidak valid.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | HARGA FULL CUSTOM ORDER
                    |--------------------------------------------------------------------------
                    */

                    $subtotal =
                        $customOrder->estimated_price;

                    $finalAmount =
                        $subtotal +
                        $validated['shipping_cost'];

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ORDER
                    |--------------------------------------------------------------------------
                    */

                    $order = Order::create([
                        'user_id' =>
                            $user->id,

                        'address_id' =>
                            $address->id,

                        'order_number' =>
                            $this->generateOrderNumber(),

                        'subtotal' =>
                            $subtotal,

                        'discount_date_cantik' =>
                            0,

                        'date_promo_id' =>
                            null,

                        'discount_voucher' =>
                            0,

                        'voucher_id' =>
                            null,

                        'shipping_cost' =>
                            $validated['shipping_cost'],

                        'final_amount' =>
                            $finalAmount,

                        'status' =>
                            'pending_payment',

                        'stock_reserved_at' =>
                            null,

                        'stock_released_at' =>
                            null,

                        'shipping_courier' =>
                            $validated['shipping_courier']
                            ?? null,

                        'snapshot_recipient_name' =>
                            $address->recipient_name,

                        'snapshot_phone' =>
                            $address->phone,

                        'snapshot_province' =>
                            $address->province,

                        'snapshot_city' =>
                            $address->city,

                        'snapshot_postal_code' =>
                            $address->postal_code,

                        'snapshot_detail' =>
                            $address->detail,

                        'notes' =>
                            $validated['notes']
                            ?? null,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CUSTOM ORDER ITEM
                    |--------------------------------------------------------------------------
                    */

                    $order->items()->create([
                        'product_variant_id' =>
                            null,

                        'product_name' =>
                            'Custom Order',

                        'variant_name' =>
                            $customOrder->reference_number,

                        'quantity' =>
                            1,

                        'stock_quantity' =>
                            0,

                        'unit_price' =>
                            $subtotal,

                        'subtotal' =>
                            $subtotal,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE CUSTOM ORDER
                    |--------------------------------------------------------------------------
                    */

                    $customOrder->update([
                        'status' =>
                            'approved',

                        'order_id' =>
                            $order->id,
                    ]);

                    return $order;
                }
            );

            $order->load([
                'items',
                'payment',
                'datePromo',
                'voucher',
                'customOrder',
            ]);

            return response()->json([
                'message' =>
                    'Custom Order berhasil disetujui. Silakan lakukan pembayaran penuh.',

                'data' =>
                    new \App\Http\Resources\OrderResource(
                        $order
                    ),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                    $e->getMessage(),
            ], 422);
        }
    }

    private function generateReferenceNumber(): string
    {
        do {
            $number =
                'CO-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(
                    Str::random(5)
                );

        } while (
            CustomOrder::where(
                'reference_number',
                $number
            )->exists()
        );

        return $number;
    }

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'BRK-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(
                    Str::random(5)
                );

        } while (
            Order::where(
                'order_number',
                $number
            )->exists()
        );

        return $number;
    }
}
