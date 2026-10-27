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

       if ($request->filled('category_id')) {
            // Langsung filter berdasarkan kolom category_id di tabel products
            $query->where('category_id', $request->category_id);
        }

        // 3. (Opsional) Filter Subcategory jika nanti dibutuhkan
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
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
