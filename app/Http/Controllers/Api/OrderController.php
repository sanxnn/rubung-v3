<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DatePromo;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->with([
                'items',
                'payment',
                'datePromo',
                'voucher',
                'customOrder',
            ])
            ->latest()
            ->paginate(10);

        return OrderResource::collection(
            $orders
        );
    }

    public function show(
        Request $request,
        Order $order
    ) {
        if (
            $order->user_id !==
            $request->user()->id
        ) {
            abort(404);
        }

        $order->load([
            'items',
            'payment',
            'datePromo',
            'voucher',
            'customOrder',
            'statusHistories',
        ]);

        return new OrderResource(
            $order
        );
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                'exists:addresses,id',
            ],

            'voucher_code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | ADDRESS
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        $cart = Cart::query()
            ->where(
                'user_id',
                $user->id
            )
            ->with('items')
            ->first();

        if (
            !$cart ||
            $cart->items->isEmpty()
        ) {
            return response()->json([
                'message' =>
                    'Keranjang kosong.',
            ], 422);
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER
            |--------------------------------------------------------------------------
            */

            $order = DB::transaction(
                function () use (
                    $validated,
                    $user,
                    $address,
                    $cart
                ) {
                    $subtotal = 0;

                    $checkoutItems = [];

                    /*
                    |--------------------------------------------------------------------------
                    | VALIDATE CART + LOCK STOCK
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $cart->items
                        as $cartItem
                    ) {
                        $variant =
                            ProductVariant::query()
                                ->with([
                                    'product',
                                    'bundleComponents',
                                ])
                                ->lockForUpdate()
                                ->find(
                                    $cartItem
                                        ->product_variant_id
                                );

                        if (!$variant) {
                            throw new \Exception(
                                'Produk pada keranjang sudah tidak tersedia.'
                            );
                        }

                        $isBundle =
                            $variant
                                ->bundleComponents
                                ->isNotEmpty();

                        /*
                        |--------------------------------------------------------------------------
                        | BUNDLE STOCK
                        |--------------------------------------------------------------------------
                        */

                        if ($isBundle) {
                            $availableStock =
                                $this->getBundleStock(
                                    $variant
                                );

                            if (
                                $cartItem->quantity >
                                $availableStock
                            ) {
                                throw new \Exception(
                                    "Stok bundle {$variant->product->name} tidak mencukupi."
                                );
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | PRODUK BIASA STOCK
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !$isBundle &&
                            $variant->stock > 0
                        ) {
                            if (
                                $cartItem->quantity >
                                $variant->stock
                            ) {
                                throw new \Exception(
                                    "Stok {$variant->product->name} - {$variant->name} tidak mencukupi."
                                );
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | SUBTOTAL
                        |--------------------------------------------------------------------------
                        */

                        $itemSubtotal =
                            $variant->price *
                            $cartItem->quantity;

                        $subtotal +=
                            $itemSubtotal;

                        /*
                        |--------------------------------------------------------------------------
                        | CHECKOUT ITEM
                        |--------------------------------------------------------------------------
                        */

                        $checkoutItems[] = [
                            'cart_item' =>
                                $cartItem,

                            'variant' =>
                                $variant,

                            'is_bundle' =>
                                $isBundle,

                            'stock_quantity' =>
                                (
                                    $isBundle ||
                                    $variant->stock > 0
                                )
                                    ? $cartItem->quantity
                                    : 0,
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DATE PROMO
                    |--------------------------------------------------------------------------
                    */

                    $datePromo =
                        DatePromo::query()
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'start_datetime',
                                '<=',
                                now()
                            )
                            ->where(
                                'end_datetime',
                                '>=',
                                now()
                            )
                            ->first();

                    $discountDateCantik =
                        $datePromo
                            ? $datePromo
                                ->calculateDiscount(
                                    $subtotal
                                )
                            : 0;

                    /*
                    |--------------------------------------------------------------------------
                    | VOUCHER
                    |--------------------------------------------------------------------------
                    */

                    $voucher = null;

                    $discountVoucher = 0;

                    if (
                        !empty(
                            $validated[
                                'voucher_code'
                            ]
                        )
                    ) {
                        $voucher =
                            Voucher::query()
                                ->where(
                                    'code',
                                    strtoupper(
                                        trim(
                                            $validated[
                                                'voucher_code'
                                            ]
                                        )
                                    )
                                )
                                ->lockForUpdate()
                                ->first();

                        if (!$voucher) {
                            throw new \Exception(
                                'Voucher tidak ditemukan.'
                            );
                        }

                        if (
                            !$voucher->isValid()
                        ) {
                            throw new \Exception(
                                'Voucher sudah tidak aktif atau kuotanya habis.'
                            );
                        }

                        if (
                            $subtotal <
                            $voucher->min_purchase
                        ) {
                            throw new \Exception(
                                'Minimal pembelian untuk voucher adalah Rp ' .
                                number_format(
                                    $voucher
                                        ->min_purchase,
                                    0,
                                    ',',
                                    '.'
                                )
                            );
                        }

                        $discountVoucher =
                            $voucher
                                ->calculateDiscount(
                                    $subtotal
                                );

                        if (
                            $discountVoucher <= 0
                        ) {
                            throw new \Exception(
                                'Voucher tidak dapat digunakan.'
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL DISCOUNT
                    |--------------------------------------------------------------------------
                    */

                    $totalDiscount =
                        min(
                            $discountDateCantik +
                            $discountVoucher,
                            $subtotal
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | RAJAONGKIR
                    |--------------------------------------------------------------------------
                    |
                    | Jember -> customer
                    | J&T
                    | 2 KG
                    |--------------------------------------------------------------------------
                    */

                    $rajaOngkir =
                        new RajaOngkirService();

                    $shipping =
                        $rajaOngkir
                            ->getCost($address);

                    $shippingCost =
                        (int) $shipping['cost'];

                    /*
                    |--------------------------------------------------------------------------
                    | FINAL AMOUNT
                    |--------------------------------------------------------------------------
                    */

                    $finalAmount = max(
                        0,
                        $subtotal -
                        $totalDiscount +
                        $shippingCost
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | ORDER NUMBER
                    |--------------------------------------------------------------------------
                    */

                    $orderNumber =
                        $this
                            ->generateOrderNumber();

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ORDER
                    |--------------------------------------------------------------------------
                    */

                    $order =
                        Order::create([
                            'user_id' =>
                                $user->id,

                            'address_id' =>
                                $address->id,

                            'order_number' =>
                                $orderNumber,

                            'subtotal' =>
                                $subtotal,

                            'discount_date_cantik' =>
                                $discountDateCantik,

                            'date_promo_id' =>
                                $datePromo?->id,

                            'discount_voucher' =>
                                $discountVoucher,

                            'voucher_id' =>
                                $voucher?->id,

                            'shipping_cost' =>
                                $shippingCost,

                            'final_amount' =>
                                $finalAmount,

                            'status' =>
                                'pending_payment',

                            'stock_reserved_at' =>
                                now(),

                            'stock_released_at' =>
                                null,

                            'shipping_courier' =>
                                'jnt',

                            'snapshot_recipient_name' =>
                                $address
                                    ->recipient_name,

                            'snapshot_phone' =>
                                $address->phone,

                            'snapshot_province' =>
                                $address->province,

                            'snapshot_city' =>
                                $address->city,

                            'snapshot_postal_code' =>
                                $address
                                    ->postal_code,

                            'snapshot_detail' =>
                                $address->detail,

                            'notes' =>
                                $validated[
                                    'notes'
                                ] ?? null,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | ORDER ITEMS + STOCK
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $checkoutItems
                        as $checkoutItem
                    ) {
                        $cartItem =
                            $checkoutItem[
                                'cart_item'
                            ];

                        $variant =
                            $checkoutItem[
                                'variant'
                            ];

                        $isBundle =
                            $checkoutItem[
                                'is_bundle'
                            ];

                        $stockQuantity =
                            $checkoutItem[
                                'stock_quantity'
                            ];

                        $order
                            ->items()
                            ->create([
                                'product_variant_id' =>
                                    $variant->id,

                                'product_name' =>
                                    $variant
                                        ->product
                                        ->name,

                                'variant_name' =>
                                    $variant
                                        ->name,

                                'quantity' =>
                                    $cartItem
                                        ->quantity,

                                'stock_quantity' =>
                                    $stockQuantity,

                                'unit_price' =>
                                    $variant
                                        ->price,

                                'subtotal' =>
                                    $variant->price *
                                    $cartItem
                                        ->quantity,
                            ]);

                        /*
                        |--------------------------------------------------------------------------
                        | DECREASE STOCK
                        |--------------------------------------------------------------------------
                        */

                        if ($isBundle) {
                            $this
                                ->decreaseBundleStock(
                                    $variant,
                                    $cartItem
                                        ->quantity
                                );
                        } elseif (
                            $stockQuantity > 0
                        ) {
                            $variant
                                ->decrement(
                                    'stock',
                                    $cartItem
                                        ->quantity
                                );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VOUCHER USED COUNT
                    |--------------------------------------------------------------------------
                    */

                    if ($voucher) {
                        $voucher->increment(
                            'used_count'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR CART
                    |--------------------------------------------------------------------------
                    */

                    $cart
                        ->items()
                        ->delete();

                    return $order;
                }
            );

            /*
            |--------------------------------------------------------------------------
            | CREATE MIDTRANS SNAP
            |--------------------------------------------------------------------------
            |
            | Dilakukan SETELAH DB transaction selesai.
            |--------------------------------------------------------------------------
            */

            $order->load([
                'user',
                'items',
                'payment',
                'datePromo',
                'voucher',
            ]);

            $snapToken =
                $this->createMidtransPayment(
                    $order
                );

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            $order->load([
                'items',
                'payment',
                'datePromo',
                'voucher',
            ]);

            return response()->json([
                'message' =>
                    'Pesanan berhasil dibuat.',

                'data' => [
                    'order' =>
                        new OrderResource(
                            $order
                        ),

                    'payment' => [
                        'payment_status' =>
                            'pending',

                        'amount' =>
                            $order
                                ->final_amount,

                        'snap_token' =>
                            $snapToken,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                    $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE MIDTRANS PAYMENT
    |--------------------------------------------------------------------------
    */

    private function createMidtransPayment(
        Order $order
    ): string {
        $serverKey =
            config('midtrans.server_key');

        if (!$serverKey) {
            throw new \Exception(
                'Konfigurasi Midtrans belum tersedia.'
            );
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
        | ITEM DETAILS
        |--------------------------------------------------------------------------
        */

        $itemDetails = [];

        foreach (
            $order->items
            as $item
        ) {
            $itemDetails[] = [
                'id' => (string) (
                    $item
                        ->product_variant_id
                    ?? $item->id
                ),

                'price' =>
                    (int) $item
                        ->unit_price,

                'quantity' =>
                    (int) $item
                        ->quantity,

                'name' =>
                    mb_substr(
                        $item
                            ->product_name .
                        ' - ' .
                        $item
                            ->variant_name,
                        0,
                        50
                    ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DATE CANTIK
        |--------------------------------------------------------------------------
        */

        if (
            $order
                ->discount_date_cantik > 0
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
        | VOUCHER
        |--------------------------------------------------------------------------
        */

        if (
            $order
                ->discount_voucher > 0
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
                    $order
                        ->shipping_cost,

                'quantity' => 1,

                'name' =>
                    'Ongkos Kirim J&T',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE MIDTRANS TOTAL
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
            throw new \Exception(
                'Total pembayaran tidak sesuai.'
            );
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
                    (int) $order
                        ->final_amount,
            ],

            'customer_details' => [
                'first_name' =>
                    $order
                        ->user
                        ->name,

                'email' =>
                    $order
                        ->user
                        ->email,

                'phone' =>
                    $order
                        ->user
                        ->phone,
            ],

            'item_details' =>
                $itemDetails,
        ];

        /*
        |--------------------------------------------------------------------------
        | GET SNAP TOKEN
        |--------------------------------------------------------------------------
        */

        $snapToken =
            Snap::getSnapToken(
                $params
            );

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

        return $snapToken;
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL ORDER
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        Order $order
    ) {
        if (
            $order->user_id !==
            $request->user()->id
        ) {
            abort(404);
        }

        if (
            $order->status !==
            'pending_payment'
        ) {
            return response()->json([
                'message' =>
                    'Pesanan tidak dapat dibatalkan pada status ini.',
            ], 422);
        }

        try {
            DB::transaction(
                function () use ($order) {
                    $order =
                        Order::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $order->id
                            );

                    if (
                        $order->status !==
                        'pending_payment'
                    ) {
                        throw new \Exception(
                            'Pesanan sudah tidak dapat dibatalkan.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE STOCK
                    |--------------------------------------------------------------------------
                    */

                    $restored =
                        $this
                            ->restoreOrderStock(
                                $order
                            );

                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE VOUCHER
                    |--------------------------------------------------------------------------
                    */

                    if ($restored) {
                        $this
                            ->restoreVoucherUsage(
                                $order
                            );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CANCEL
                    |--------------------------------------------------------------------------
                    */

                    $order->update([
                        'status' =>
                            'cancelled',
                    ]);
                }
            );

            $order->refresh();

            return response()->json([
                'message' =>
                    'Pesanan berhasil dibatalkan.',

                'data' =>
                    new OrderResource(
                        $order->load([
                            'items',
                            'payment',
                            'datePromo',
                            'voucher',
                        ])
                    ),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                    $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE ORDER NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'BRK-' .
                now()->format(
                    'YmdHis'
                ) .
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

    /*
    |--------------------------------------------------------------------------
    | GET BUNDLE STOCK
    |--------------------------------------------------------------------------
    */

    private function getBundleStock(
        ProductVariant $parentVariant
    ): int {
        $components =
            ProductBundleItem::query()
                ->where(
                    'parent_variant_id',
                    $parentVariant->id
                )
                ->get();

        if (
            $components->isEmpty()
        ) {
            return 0;
        }

        $stocks = [];

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

            if (!$componentVariant) {
                return 0;
            }

            if (
                $component->quantity <= 0
            ) {
                return 0;
            }

            $stocks[] =
                intdiv(
                    $componentVariant
                        ->stock,
                    $component
                        ->quantity
                );
        }

        return min($stocks);
    }

    /*
    |--------------------------------------------------------------------------
    | DECREASE BUNDLE STOCK
    |--------------------------------------------------------------------------
    */

    private function decreaseBundleStock(
        ProductVariant $parentVariant,
        int $quantity
    ): void {
        $components =
            ProductBundleItem::query()
                ->where(
                    'parent_variant_id',
                    $parentVariant->id
                )
                ->get();

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

            if (!$componentVariant) {
                throw new \Exception(
                    'Komponen bundle tidak ditemukan.'
                );
            }

            $decrease =
                $component->quantity *
                $quantity;

            if (
                $componentVariant->stock <
                $decrease
            ) {
                throw new \Exception(
                    "Stok komponen {$componentVariant->name} tidak mencukupi."
                );
            }

            $componentVariant->decrement(
                'stock',
                $decrease
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE ORDER STOCK
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
                        $item
                            ->product_variant_id
                    );

            if (!$variant) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | BUNDLE
            |--------------------------------------------------------------------------
            */

            $components =
                ProductBundleItem::query()
                    ->where(
                        'parent_variant_id',
                        $variant->id
                    )
                    ->get();

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
            | PRODUK BIASA
            |--------------------------------------------------------------------------
            */

            $variant->increment(
                'stock',
                $item->stock_quantity
            );
        }

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
