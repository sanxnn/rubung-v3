@extends('storefront.layout')

@section('title', 'Katalog Produk | Batik Rubung Kuning')

@section('content')
    <section class="section shell" aria-labelledby="catalog-page-title">
        <div class="section-heading">
            <h1 id="catalog-page-title">Katalog Produk</h1>
            <p>Jelajahi semua kategori atau cari produk berdasarkan nama.</p>
        </div>
        <form class="catalog-filters" action="{{ route('company.catalog') }}" method="get">
            <div class="field">
                <label for="catalog-search">Nama produk</label>
                <input id="catalog-search" name="q" value="{{ $search }}" type="search" maxlength="100">
            </div>
            <div class="field">
                <label for="catalog-category">Kategori</label>
                <select id="catalog-category" name="category">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $option)
                        <option value="{{ $option->slug }}" @selected($category === $option->slug)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="button button-primary" type="submit">Cari Produk</button>
            @if ($search !== '' || $category !== '')
                <a class="button button-secondary" href="{{ route('company.catalog') }}">Reset Filter</a>
            @endif
        </form>
        @if ($products->isEmpty())
            <p class="empty-state">Produk tidak ditemukan.</p>
        @else
            <p class="result-count">{{ number_format($products->total(), 0, ',', '.') }} produk</p>
            <div class="collection-grid">
                @foreach ($products as $product)
                    @include('storefront.product-card')
                @endforeach
            </div>
        @endif
        @if ($products->hasPages())
            <nav class="catalog-pagination" aria-label="Halaman katalog">
                @if ($products->previousPageUrl())
                    <a class="button button-secondary" href="{{ $products->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                @endif
                <span>Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }}</span>
                @if ($products->nextPageUrl())
                    <a class="button button-secondary" href="{{ $products->nextPageUrl() }}" rel="next">Berikutnya</a>
                @endif
            </nav>
        @endif
    </section>
@endsection
