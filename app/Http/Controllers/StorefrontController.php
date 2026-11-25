<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'subcategory' => ['nullable', 'integer', 'min:1'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0', Rule::when($request->filled('min_price'), 'gte:min_price')],
        ], [
            'max_price.gte' => __('storefront.invalid_price_range'),
            'min_price.integer' => __('storefront.invalid_price'),
            'max_price.integer' => __('storefront.invalid_price'),
            'min_price.min' => __('storefront.invalid_price'),
            'max_price.min' => __('storefront.invalid_price'),
        ]);
        $search = trim($filters['q'] ?? '');
        $category = $filters['category'] ?? '';
        $subcategory = $filters['subcategory'] ?? null;
        $minimum = $filters['min_price'] ?? null;
        $maximum = $filters['max_price'] ?? null;

        $startingPrice = ProductVariant::query()->selectRaw('MIN(price)')
            ->whereColumn('product_variants.product_id', 'products.id');

        $products = Product::with(['category', 'subcategory'])
            ->withMin('variants', 'price')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($category !== '', fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category)))
            ->when($subcategory !== null, fn ($query) => $query->where('subcategory_id', $subcategory))
            ->when($minimum !== null, fn ($query) => $query->where(clone $startingPrice, '>=', (int) $minimum))
            ->when($maximum !== null, fn ($query) => $query->where(clone $startingPrice, '<=', (int) $maximum))
            ->orderByDesc('id')
            ->paginate(12)
            ->appends(array_filter($filters, fn ($value) => $value !== null && $value !== ''));

        $categories = Category::with(['subcategories' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')->get(['id', 'name', 'slug']);

        return view('storefront.catalog', compact(
            'products', 'categories', 'search', 'category', 'subcategory', 'minimum', 'maximum'
        ));
    }
}
