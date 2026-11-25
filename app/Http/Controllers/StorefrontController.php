<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $sales = OrderItem::query()
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['paid', 'processing', 'packing', 'shipped', 'completed'])
                    ->whereHas('payment', fn ($payment) => $payment->where('payment_status', 'success'))
                    ->whereDoesntHave('refundRequests', fn ($refund) => $refund->where('status', 'completed'));
            })
            ->selectRaw('product_variants.product_id, SUM(order_items.quantity) as units_sold')
            ->groupBy('product_variants.product_id');

        $featured = Product::with(['category', 'subcategory'])
            ->withMin('variants', 'price')
            ->whereHas('category', fn ($category) => $category->where('slug', 'batik'))
            ->leftJoinSub($sales, 'sales', 'sales.product_id', '=', 'products.id')
            ->addSelect('sales.units_sold')
            ->orderByDesc('sales.units_sold')
            ->orderByDesc('products.id')
            ->limit(3)
            ->get();

        return view('company-profile', compact('featured'));
    }

    public function catalog(Request $request): View
    {
        $search = trim($request->string('q')->limit(100, '')->toString());
        $category = $request->string('category')->limit(100, '')->toString();

        $products = Product::with(['category', 'subcategory'])
            ->withMin('variants', 'price')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($category !== '', fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category)))
            ->orderByDesc('id')
            ->paginate(12)
            ->appends(array_filter(['q' => $search, 'category' => $category], fn ($value) => $value !== ''));

        $categories = Category::orderBy('name')->get(['name', 'slug']);

        return view('storefront.catalog', compact('products', 'categories', 'search', 'category'));
    }
}
