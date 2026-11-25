@extends('storefront.layout')

@section('title', __('storefront.catalog_title'))

@section('content')
    <section class="section shell" aria-labelledby="catalog-page-title">
        <div class="section-heading">
            <h1 id="catalog-page-title">{{ __('storefront.catalog') }}</h1>
            <p>{{ __('storefront.catalog_intro') }}</p>
        </div>
        <form class="catalog-filters" action="{{ route('company.catalog') }}" method="get">
            <div class="field">
                <label for="catalog-search">{{ __('storefront.product_name') }}</label>
                <input id="catalog-search" name="q" value="{{ $search }}" type="search" maxlength="100">
            </div>
            <div class="field">
                <label for="catalog-category">{{ __('storefront.category') }}</label>
                <select id="catalog-category" name="category">
                    <option value="">{{ __('storefront.all_categories') }}</option>
                    @foreach ($categories as $option)
                        <option value="{{ $option->slug }}" @selected($category === $option->slug)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="button button-primary" type="submit">{{ __('storefront.search') }}</button>
            @if ($search !== '' || $category !== '')
                <a class="button button-secondary" href="{{ route('company.catalog') }}">{{ __('storefront.reset') }}</a>
            @endif
        </form>
        @if ($products->isEmpty())
            <p class="empty-state">{{ __('storefront.empty_products') }}</p>
        @else
            <p class="result-count">{{ __('storefront.product_count', ['count' => $products->total()]) }}</p>
            <div class="collection-grid">
                @foreach ($products as $product)
                    @include('storefront.product-card')
                @endforeach
            </div>
        @endif
        @if ($products->hasPages())
            <nav class="catalog-pagination" aria-label="{{ __('storefront.pagination') }}">
                @if ($products->previousPageUrl())
                    <a class="button button-secondary" href="{{ $products->previousPageUrl() }}" rel="prev">{{ __('storefront.previous') }}</a>
                @endif
                <span>{{ __('storefront.page_count', ['page' => $products->currentPage(), 'last' => $products->lastPage()]) }}</span>
                @if ($products->nextPageUrl())
                    <a class="button button-secondary" href="{{ $products->nextPageUrl() }}" rel="next">{{ __('storefront.next') }}</a>
                @endif
            </nav>
        @endif
    </section>
@endsection
