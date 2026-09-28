<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Models\Product;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category:id,name,slug',
            'variants' => function ($query) {
                $query
                    ->select('id', 'product_id', 'name', 'sku', 'price')
                    ->whereHas('bundleComponents');
            },
            'variants.bundleComponents' => function ($query) {
                $query->select(
                    'id',
                    'parent_variant_id',
                    'component_variant_id',
                    'quantity'
                );
            },
            'variants.bundleComponents.componentVariant:id,product_id,name,sku,price,stock',
            'variants.bundleComponents.componentVariant.product:id,name,slug',
        ])
            ->whereHas('variants', function ($query) {
                $query->whereHas('bundleComponents');
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bundles = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return BundleResource::collection($bundles)
            ->additional([
                'message' => 'Data bundling berhasil diambil.',
            ]);
    }

    public function show(Product $product)
    {
        $product->load([
            'category:id,name,slug',
            'variants' => function ($query) {
                $query
                    ->select('id', 'product_id', 'name', 'sku', 'price')
                    ->whereHas('bundleComponents');
            },
            'variants.bundleComponents' => function ($query) {
                $query->select(
                    'id',
                    'parent_variant_id',
                    'component_variant_id',
                    'quantity'
                );
            },
            'variants.bundleComponents.componentVariant:id,product_id,name,sku,price,stock',
            'variants.bundleComponents.componentVariant.product:id,name,slug',
        ]);

        if ($product->variants->isEmpty()) {
            return response()->json([
                'message' => 'Bundling tidak ditemukan.',
            ], 404);
        }

        return (new BundleResource($product))
            ->additional([
                'message' => 'Detail bundling berhasil diambil.',
            ]);
    }
}
