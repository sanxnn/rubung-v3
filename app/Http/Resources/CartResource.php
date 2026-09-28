<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'items' => $this->items->map(function ($item) {
                $variant = $item->productVariant;

                $isBundle = $variant->bundleComponents->isNotEmpty();

                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,

                    'variant' => [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                        'price' => $variant->price,

                        'is_bundle' => $isBundle,

                        'stock' => $isBundle
                            ? $variant->available_bundle_stock
                            : $variant->stock,

                        'product' => [
                            'id' => $variant->product->id,
                            'name' => $variant->product->name,
                            'slug' => $variant->product->slug,
                            'image' => $variant->product->image,
                        ],
                    ],

                    'subtotal' => $variant->price * $item->quantity,
                ];
            }),

            'total_items' => $this->items->sum('quantity'),

            'total_amount' => $this->items->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            }),
        ];
    }
}
