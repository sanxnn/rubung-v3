<footer class="site-footer">
    <div class="shell footer-grid">
        <div>
            <a class="brand" href="{{ route('company.home') }}">
                <span class="brand-icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></span>
                <span>Batik Rubung Kuning</span>
            </a>
            <p>{{ __('storefront.footer_intro') }}</p>
            @if ($publicEmail = config('services.storefront.public_email'))
                <a class="text-link" href="mailto:{{ $publicEmail }}">{{ $publicEmail }}</a>
            @endif
        </div>
        <nav aria-label="{{ __('storefront.footer_nav') }}">
            <strong>{{ __('storefront.explore') }}</strong>
            <a href="{{ $homeSection }}#home">{{ __('storefront.home') }}</a>
            <a href="{{ $homeSection }}#catalog">{{ __('storefront.featured') }}</a>
            <a href="{{ route('company.catalog') }}">{{ __('storefront.all_products') }}</a>
            <a href="{{ $homeSection }}#store-info">{{ __('storefront.about') }}</a>
            <a href="{{ $homeSection }}#download-app">{{ __('storefront.app') }}</a>
            <a href="{{ $homeSection }}#contact">{{ __('storefront.contact') }}</a>
        </nav>
        <div>
            <strong>{{ __('storefront.customer_app') }}</strong>
            @include('storefront.app-badges')
        </div>
    </div>
    <div class="shell footer-inner">
        <p>© {{ date('Y') }} Batik Rubung Kuning</p>
        <a href="{{ route('login') }}">{{ __('storefront.admin_login') }}</a>
    </div>
</footer>
