<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DatePromo;
use App\Models\Order;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'items',
                'payment',
                'datePromo',
                'voucher',
                'customOrder',
            ])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
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

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
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

        $address = Address::query()
            ->where('id', $validated['address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'message' => 'Alamat tidak ditemukan.',
            ], 404);
        }

        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Keranjang kosong.',
            ], 422);
        }

        try {
            $order = DB::transaction(function () use (
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

                foreach ($cart->items as $cartItem) {
                    $variant = ProductVariant::query()
                        ->with([
                            'product',
                            'bundleComponents',
                        ])
                        ->lockForUpdate()
                        ->find($cartItem->product_variant_id);

                    if (!$variant) {
                        throw new \Exception(
                            'Produk pada keranjang sudah tidak tersedia.'
                        );
                    }

                    $isBundle = $variant->bundleComponents->isNotEmpty();

                    /*
                    |--------------------------------------------------------------------------
                    | BUNDLE
                    |--------------------------------------------------------------------------
                    */

                    if ($isBundle) {
                        $availableStock = $this->getBundleStock($variant);

                        if ($cartItem->quantity > $availableStock) {
                            throw new \Exception(
                                "Stok bundle {$variant->product->name} tidak mencukupi."
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PRODUK BIASA
                    |--------------------------------------------------------------------------
                    */

                    if (!$isBundle && $variant->stock > 0) {
                        if ($cartItem->quantity > $variant->stock) {
                            throw new \Exception(
                                "Stok {$variant->product->name} - {$variant->name} tidak mencukupi."
                            );
                        }
                    }

                    $itemSubtotal =
                        $variant->price * $cartItem->quantity;

                    $subtotal += $itemSubtotal;

                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN DATA PER ITEM
                    |--------------------------------------------------------------------------
                    */

                    $checkoutItems[] = [
                        'cart_item' => $cartItem,
                        'variant' => $variant,
                        'is_bundle' => $isBundle,
                        'stock_quantity' => (
                            $isBundle || $variant->stock > 0
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

                $datePromo = DatePromo::query()
                    ->where('is_active', true)
                    ->where('start_datetime', '<=', now())
                    ->where('end_datetime', '>=', now())
                    ->first();

                $discountDateCantik = $datePromo
                    ? $datePromo->calculateDiscount($subtotal)
                    : 0;

                /*
                |--------------------------------------------------------------------------
                | VOUCHER
                |--------------------------------------------------------------------------
                */

                $voucher = null;
                $discountVoucher = 0;

                if (!empty($validated['voucher_code'])) {
                    $voucher = Voucher::query()
                        ->where(
                            'code',
                            strtoupper(trim($validated['voucher_code']))
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$voucher) {
                        throw new \Exception(
                            'Voucher tidak ditemukan.'
                        );
                    }

                    if (!$voucher->isValid()) {
                        throw new \Exception(
                            'Voucher sudah tidak aktif atau kuotanya habis.'
                        );
                    }

                    if ($subtotal < $voucher->min_purchase) {
                        throw new \Exception(
                            'Minimal pembelian untuk voucher adalah Rp ' .
                            number_format(
                                $voucher->min_purchase,
                                0,
                                ',',
                                '.'
                            )
                        );
                    }

                    $discountVoucher =
                        $voucher->calculateDiscount($subtotal);

                    if ($discountVoucher <= 0) {
                        throw new \Exception(
                            'Voucher tidak dapat digunakan.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | TOTAL
                |--------------------------------------------------------------------------
                */

                $totalDiscount = min(
                    $discountDateCantik + $discountVoucher,
                    $subtotal
                );

                $finalAmount = max(
                    0,
                    $subtotal
                    - $totalDiscount
                    + $validated['shipping_cost']
                );

                /*
                |--------------------------------------------------------------------------
                | ORDER NUMBER
                |--------------------------------------------------------------------------
                */

                $orderNumber = $this->generateOrderNumber();

                /*
                |--------------------------------------------------------------------------
                | CREATE ORDER
                |--------------------------------------------------------------------------
                */

                $order = Order::create([
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'order_number' => $orderNumber,

                    'subtotal' => $subtotal,

                    'discount_date_cantik' => $discountDateCantik,
                    'date_promo_id' => $datePromo?->id,

                    'discount_voucher' => $discountVoucher,
                    'voucher_id' => $voucher?->id,

                    'shipping_cost' => $validated['shipping_cost'],

                    'final_amount' => $finalAmount,

                    'status' => 'pending_payment',

                    'stock_reserved_at' => now(),
                    'stock_released_at' => null,

                    'shipping_courier' =>
                        $validated['shipping_courier'] ?? null,

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
                        $validated['notes'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | CREATE ORDER ITEMS + STOCK
                |--------------------------------------------------------------------------
                */

                foreach ($checkoutItems as $checkoutItem) {
                    $cartItem = $checkoutItem['cart_item'];
                    $variant = $checkoutItem['variant'];
                    $isBundle = $checkoutItem['is_bundle'];
                    $stockQuantity = $checkoutItem['stock_quantity'];

                    $order->items()->create([
                        'product_variant_id' => $variant->id,
                        'product_name' => $variant->product->name,
                        'variant_name' => $variant->name,

                        'quantity' => $cartItem->quantity,

                        'stock_quantity' => $stockQuantity,

                        'unit_price' => $variant->price,

                        'subtotal' =>
                            $variant->price *
                            $cartItem->quantity,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | KURANGI STOCK
                    |--------------------------------------------------------------------------
                    */

                    if ($isBundle) {
                        $this->decreaseBundleStock(
                            $variant,
                            $cartItem->quantity
                        );
                    } elseif ($stockQuantity > 0) {
                        $variant->decrement(
                            'stock',
                            $cartItem->quantity
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | VOUCHER USED COUNT
                |--------------------------------------------------------------------------
                */

                if ($voucher) {
                    $voucher->increment('used_count');
                }

                /*
                |--------------------------------------------------------------------------
                | CLEAR CART
                |--------------------------------------------------------------------------
                */

                $cart->items()->delete();

                return $order;
            });

            $order->load([
                'items',
                'payment',
                'datePromo',
                'voucher',
            ]);

            return (new OrderResource($order))
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(404);
        }

        if ($order->status !== 'pending_payment') {
            return response()->json([
                'message' =>
                    'Pesanan tidak dapat dibatalkan pada status ini.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($order) {
                $order = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if ($order->status !== 'pending_payment') {
                    throw new \Exception(
                        'Pesanan sudah tidak dapat dibatalkan.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | RESTORE STOCK
                |--------------------------------------------------------------------------
                */

                $restored = $this->restoreOrderStock($order);

                /*
                |--------------------------------------------------------------------------
                | RESTORE VOUCHER
                |--------------------------------------------------------------------------
                */

                if ($restored) {
                    $this->restoreVoucherUsage($order);
                }

                /*
                |--------------------------------------------------------------------------
                | CANCEL ORDER
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'status' => 'cancelled',
                ]);
            });

            $order->refresh();

            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan.',
                'data' => new OrderResource(
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
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'BRK-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(Str::random(5));

        } while (
            Order::where('order_number', $number)->exists()
        );

        return $number;
    }

    private function getBundleStock(ProductVariant $parentVariant): int
    {
        $components = ProductBundleItem::query()
            ->where('parent_variant_id', $parentVariant->id)
            ->get();

        if ($components->isEmpty()) {
            return 0;
        }

        $stocks = [];

        foreach ($components as $component) {
            $componentVariant = ProductVariant::query()
                ->lockForUpdate()
                ->find($component->component_variant_id);

            if (!$componentVariant) {
                return 0;
            }

            if ($component->quantity <= 0) {
                return 0;
            }

            $stocks[] = intdiv(
                $componentVariant->stock,
                $component->quantity
            );
        }

        return min($stocks);
    }

    private function decreaseBundleStock(
        ProductVariant $parentVariant,
        int $quantity
    ): void {
        $components = ProductBundleItem::query()
            ->where('parent_variant_id', $parentVariant->id)
            ->get();

        foreach ($components as $component) {
            $componentVariant = ProductVariant::query()
                ->lockForUpdate()
                ->find($component->component_variant_id);

            if (!$componentVariant) {
                throw new \Exception(
                    'Komponen bundle tidak ditemukan.'
                );
            }

            $decrease =
                $component->quantity * $quantity;

            if ($componentVariant->stock < $decrease) {
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

    private function restoreOrderStock(Order $order): bool
    {
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
                ->find($item->product_variant_id);

            if (!$variant) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | BUNDLE
            |--------------------------------------------------------------------------
            */

            $components = ProductBundleItem::query()
                ->where('parent_variant_id', $variant->id)
                ->get();

            if ($components->isNotEmpty()) {
                foreach ($components as $component) {
                    $componentVariant = ProductVariant::query()
                        ->lockForUpdate()
                        ->find($component->component_variant_id);

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

    private function restoreVoucherUsage(Order $order): void
    {
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
            $voucher->decrement('used_count');
        }
    }
}
