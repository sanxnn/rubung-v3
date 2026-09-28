<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category:id,name,slug',
            'subcategory:id,name,slug',
            'variants:id,product_id,name,sku,price,stock',
        ])
            ->whereHas('variants', function ($query) {
                $query->whereDoesntHave('bundleComponents');
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
        }

        if ($request->filled('subcategory')) {
            $query->whereHas('subcategory', function ($query) use ($request) {
                $query->where('slug', $request->subcategory);
            });
        }

        $products = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'message' => 'Data produk berhasil diambil.',
            'data' => $products,
        ]);
    }

    public function show(Product $product)
    {
        $product->load([
            'category:id,name,slug',
            'subcategory:id,name,slug',
            'variants' => function ($query) {
                $query
                    ->select('id', 'product_id', 'name', 'sku', 'price', 'stock')
                    ->whereDoesntHave('bundleComponents');
            },
        ]);

        if ($product->variants->isEmpty()) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail produk berhasil diambil.',
            'data' => $product,
        ]);
    }
}
