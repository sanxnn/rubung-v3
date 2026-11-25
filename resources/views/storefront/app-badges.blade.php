<div class="app-badges" aria-label="{{ __('storefront.app_stores') }}">
    @foreach ([
        ['Google Play', 'fa-google-play', config('services.storefront.google_play_url')],
        ['App Store', 'fa-apple', config('services.storefront.app_store_url')],
    ] as [$storeName, $icon, $storeUrl])
        @if ($storeUrl)
            <a class="store-badge" href="{{ $storeUrl }}">
                <i class="fa-brands {{ $icon }}" aria-hidden="true"></i>
                <span><small>{{ __('storefront.download') }}</small>{{ $storeName }}</span>
            </a>
        @else
            <span class="store-badge store-badge-unavailable">
                <i class="fa-brands {{ $icon }}" aria-hidden="true"></i>
                <span><small>{{ __('storefront.unavailable') }}</small>{{ $storeName }}</span>
            </span>
        @endif
    @endforeach
</div>
