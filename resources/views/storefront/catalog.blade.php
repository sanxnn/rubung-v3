@extends('storefront.layout')

@section('title', __('storefront.catalog_title'))

@section('content')
    <div class="section shell catalog-layout">
        <aside class="catalog-sidebar" aria-labelledby="catalog-filters-title">
            <div class="catalog-sidebar-heading">
                <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                <h2 id="catalog-filters-title">{{ __('storefront.filters') }}</h2>
            </div>
            <form class="catalog-filters" action="{{ route('company.catalog') }}" method="get">
                @if ($errors->any())
                    <ul class="catalog-filter-errors notice notice-error" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="field">
                    <label for="catalog-search">{{ __('storefront.product_name') }}</label>
                    <input id="catalog-search" name="q" value="{{ $search }}" type="search" maxlength="100"
                        placeholder="{{ __('storefront.search_placeholder') }}">
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
                <div class="field">
                    <label for="catalog-subcategory">{{ __('storefront.subcategory') }}</label>
                    <select id="catalog-subcategory" name="subcategory">
                        <option value="">{{ __('storefront.all_subcategories') }}</option>
                        @foreach ($categories->where(fn ($option) => $option->subcategories->isNotEmpty()) as $option)
                            <optgroup label="{{ $option->name }}" data-category="{{ $option->slug }}">
                                @foreach ($option->subcategories as $child)
                                    <option value="{{ $child->id }}" @selected((string) $subcategory === (string) $child->id)>{{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <fieldset class="catalog-price">
                    <legend>{{ __('storefront.price_range') }}</legend>
                    <p id="catalog-price-note">{{ __('storefront.price_filter_note') }}</p>
                    <div class="catalog-price-fields">
                        <div class="field">
                            <label for="catalog-min-price">{{ __('storefront.min_price') }}</label>
                            <input id="catalog-min-price" type="number" name="min_price" min="0" step="1"
                                inputmode="numeric" value="{{ $minimum }}" aria-describedby="catalog-price-note">
                        </div>
                        <div class="field">
                            <label for="catalog-max-price">{{ __('storefront.max_price') }}</label>
                            <input id="catalog-max-price" type="number" name="max_price" min="0" step="1"
                                inputmode="numeric" value="{{ $maximum }}" aria-describedby="catalog-price-note">
                        </div>
                    </div>
                </fieldset>
                <button class="button button-primary" type="submit">{{ __('storefront.apply_filters') }}</button>
                @if ($search !== '' || $category !== '' || $subcategory !== null || $minimum !== null || $maximum !== null)
                    <a class="button button-secondary" href="{{ route('company.catalog') }}">{{ __('storefront.reset') }}</a>
                @endif
            </form>
        </aside>

        <section class="catalog-results" aria-labelledby="catalog-page-title">
            <div class="section-heading">
                <h1 id="catalog-page-title">{{ __('storefront.catalog') }}</h1>
                <p>{{ __('storefront.catalog_intro') }}</p>
            </div>
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
    </div>

    <script>
        const catalogCategory = document.getElementById('catalog-category');
        const catalogSubcategory = document.getElementById('catalog-subcategory');

        function filterSubcategories() {
            for (const group of catalogSubcategory.querySelectorAll('optgroup')) {
                group.disabled = Boolean(catalogCategory.value) && group.dataset.category !== catalogCategory.value;
                group.hidden = group.disabled;
            }

            if (catalogSubcategory.selectedOptions[0]?.parentElement.disabled) {
                catalogSubcategory.value = '';
            }
        }

        catalogCategory.addEventListener('change', filterSubcategories);
        filterSubcategories();
    </script>
@endsection
