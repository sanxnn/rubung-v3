<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],

            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'stock' => $variant->available_bundle_stock,

                    'components' => $variant->bundleComponents->map(function ($component) {
                        return [
                            'variant_id' => $component->componentVariant->id,
                            'name' => $component->componentVariant->name,
                            'sku' => $component->componentVariant->sku,
                            'quantity' => $component->quantity,
                            'stock' => $component->componentVariant->stock,

                            'product' => [
                                'id' => $component->componentVariant->product->id,
                                'name' => $component->componentVariant->product->name,
                                'slug' => $component->componentVariant->product->slug,
                            ],
                        ];
                    }),
                ];
            }),
        ];
    }
}
