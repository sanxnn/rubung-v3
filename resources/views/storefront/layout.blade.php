<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('storefront.meta') }}">
    <title>@yield('title', __('storefront.home_title'))</title>
    <script>
        (() => {
            try {
                const preference = localStorage.getItem('rubung-storefront-theme');
                if (preference === 'light' || preference === 'dark') {
                    document.documentElement.dataset.theme = preference;
                }
            } catch {
                // The system theme is still available when browser storage is disabled.
            }
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/company-profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <a class="skip-link" href="#main">{{ __('storefront.skip') }}</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></span>
                <span>Batik Rubung Kuning</span>
            </a>
            <nav class="desktop-nav" aria-label="{{ __('storefront.primary_nav') }}">
                <a href="{{ route('company.catalog') }}">{{ __('storefront.collections') }}</a>
                <a href="{{ route('company.home') }}#store-info">{{ __('storefront.about') }}</a>
                <a href="{{ route('company.home') }}#download-app">{{ __('storefront.app') }}</a>
            </nav>
            <a class="button button-primary header-contact" href="{{ route('company.home') }}#contact">{{ __('storefront.contact') }}</a>
            <div class="storefront-preferences">
                <nav class="language-switcher" aria-label="{{ __('storefront.language') }}">
                    @foreach (['id' => 'Bahasa Indonesia', 'en' => 'English'] as $language => $label)
                        <a href="{{ request()->fullUrlWithQuery(['lang' => $language]) }}"
                            lang="{{ $language }}" hreflang="{{ $language }}" aria-label="{{ $label }}"
                            @if (app()->getLocale() === $language) aria-current="true" @endif>{{ strtoupper($language) }}</a>
                    @endforeach
                </nav>
                <button class="theme-toggle" type="button" aria-label="{{ __('storefront.theme') }}"
                    title="{{ __('storefront.theme') }}" aria-pressed="false" hidden>
                    <i class="fa-solid fa-moon theme-moon" aria-hidden="true"></i>
                    <i class="fa-solid fa-sun theme-sun" aria-hidden="true"></i>
                </button>
            </div>
            <details class="mobile-nav">
                <summary>{{ __('storefront.menu') }}</summary>
                <nav aria-label="{{ __('storefront.mobile_nav') }}">
                    <a href="{{ route('company.catalog') }}">{{ __('storefront.collections') }}</a>
                    <a href="{{ route('company.home') }}#store-info">{{ __('storefront.about') }}</a>
                    <a href="{{ route('company.home') }}#download-app">{{ __('storefront.app') }}</a>
                    <a href="{{ route('company.home') }}#contact">{{ __('storefront.contact') }}</a>
                </nav>
            </details>
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    @include('storefront.footer')

    <script>
        const themeButton = document.querySelector('.theme-toggle');
        const storefrontRoot = document.documentElement;
        const systemTheme = matchMedia('(prefers-color-scheme: dark)');
        const darkThemeIsActive = () => storefrontRoot.dataset.theme
            ? storefrontRoot.dataset.theme === 'dark'
            : systemTheme.matches;
        const updateThemeButton = () => themeButton.setAttribute('aria-pressed', String(darkThemeIsActive()));

        updateThemeButton();
        themeButton.hidden = false;
        systemTheme.addEventListener('change', updateThemeButton);
        themeButton.addEventListener('click', () => {
            const theme = darkThemeIsActive() ? 'light' : 'dark';
            storefrontRoot.dataset.theme = theme;
            updateThemeButton();
            try {
                localStorage.setItem('rubung-storefront-theme', theme);
            } catch {
                // Keep the selection for the current page.
            }
        });

        const mobileNav = document.querySelector('.mobile-nav');
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => { mobileNav.open = false; });
        });
        mobileNav.addEventListener('keydown', event => {
            if (event.key === 'Escape' && mobileNav.open) {
                mobileNav.open = false;
                mobileNav.querySelector('summary').focus();
            }
        });
        document.querySelector('[data-contact-feedback]')?.focus();
    </script>
</body>
</html>
