<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->getCart($request);

        return (new CartResource($cart))
            ->additional([
                'message' => 'Keranjang berhasil diambil.',
            ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        $variant = ProductVariant::with([
            'product',
            'bundleComponents.componentVariant',
        ])->findOrFail($validated['product_variant_id']);

        $isBundle = $variant->bundleComponents->isNotEmpty();

        $availableStock = $isBundle
            ? $variant->available_bundle_stock
            : $variant->stock;

        if ($availableStock > 0 && $validated['quantity'] > $availableStock) {
            return response()->json([
                'message' => 'Jumlah produk melebihi stok yang tersedia.',
                'available_stock' => $availableStock,
            ], 422);
        }

        /*
         * Untuk sementara, stok 0 tidak langsung diblokir.
         *
         * Ini penting karena struktur database saat ini belum
         * memberikan penanda yang jelas untuk produk Batik yang
         * menggunakan sistem PO dan tidak memakai stock.
         *
         * Validasi stok final akan dilakukan lagi saat checkout.
         */

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $cartItem = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            if ($availableStock > 0 && $newQuantity > $availableStock) {
                return response()->json([
                    'message' => 'Jumlah produk di keranjang melebihi stok yang tersedia.',
                    'available_stock' => $availableStock,
                    'current_quantity' => $cartItem->quantity,
                ], 422);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $validated['quantity'],
            ]);
        }

        $cart->load([
            'items.productVariant.product',
            'items.productVariant.bundleComponents.componentVariant.product',
        ]);

        return (new CartResource($cart))
            ->additional([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
            ]);
    }

    public function update(Request $request, int $cartItem)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getCart($request);

        $item = $cart->items()
            ->with([
                'productVariant.product',
                'productVariant.bundleComponents.componentVariant',
            ])
            ->where('id', $cartItem)
            ->first();

        if (!$item) {
            return response()->json([
                'message' => 'Item keranjang tidak ditemukan.',
            ], 404);
        }

        $variant = $item->productVariant;

        $isBundle = $variant->bundleComponents->isNotEmpty();

        $availableStock = $isBundle
            ? $variant->available_bundle_stock
            : $variant->stock;

        if ($availableStock > 0 && $validated['quantity'] > $availableStock) {
            return response()->json([
                'message' => 'Jumlah produk melebihi stok yang tersedia.',
                'available_stock' => $availableStock,
            ], 422);
        }

        $item->update([
            'quantity' => $validated['quantity'],
        ]);

        $cart->load([
            'items.productVariant.product',
            'items.productVariant.bundleComponents.componentVariant.product',
        ]);

        return (new CartResource($cart))
            ->additional([
                'message' => 'Jumlah produk berhasil diperbarui.',
            ]);
    }

    public function destroy(Request $request, int $cartItem)
    {
        $cart = $this->getCart($request);

        $item = $cart->items()
            ->where('id', $cartItem)
            ->first();

        if (!$item) {
            return response()->json([
                'message' => 'Item keranjang tidak ditemukan.',
            ], 404);
        }

        $item->delete();

        $cart->load([
            'items.productVariant.product',
            'items.productVariant.bundleComponents.componentVariant.product',
        ]);

        return (new CartResource($cart))
            ->additional([
                'message' => 'Produk berhasil dihapus dari keranjang.',
            ]);
    }

    public function clear(Request $request)
    {
        $cart = $this->getCart($request);

        $cart->items()->delete();

        $cart->load([
            'items.productVariant.product',
            'items.productVariant.bundleComponents.componentVariant.product',
        ]);

        return (new CartResource($cart))
            ->additional([
                'message' => 'Keranjang berhasil dikosongkan.',
            ]);
    }

    private function getCart(Request $request): Cart
    {
        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        return $cart->load([
            'items.productVariant.product',
            'items.productVariant.bundleComponents.componentVariant.product',
        ]);
    }
}
