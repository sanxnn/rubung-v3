<article class="collection-card product-card">
    @if ($product->image)
        <img class="product-media" src="{{ url('/api/storage-image/'.$product->image) }}" alt="{{ $product->name }}" width="480" height="360" loading="lazy">
    @else
        <div class="product-media product-placeholder" aria-hidden="true">
            <i class="fa-solid fa-image"></i>
        </div>
    @endif
    <div class="product-card-body">
        <p class="product-category">{{ $product->category?->name }}@if ($product->subcategory) · {{ $product->subcategory->name }}@endif</p>
        <h3>{{ $product->name }}</h3>
        @if ($product->description)
            <p>{{ Str::limit($product->description, 125) }}</p>
        @endif
        <div class="product-card-meta">
            @if ($product->variants_min_price !== null)
                <strong>{{ __('storefront.price', ['price' => number_format($product->variants_min_price, 0, ',', '.')]) }}</strong>
            @endif
            @if (($showSales ?? false) && $product->units_sold > 0)
                <span class="sales-badge">{{ __('storefront.sold', ['count' => $product->units_sold]) }}</span>
            @endif
        </div>
    </div>
</article>
