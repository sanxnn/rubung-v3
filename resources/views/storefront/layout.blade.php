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
<body id="page-top">
    @php
        $onHome = request()->routeIs('company.home');
        $homeSection = $onHome ? '' : route('company.home');
    @endphp
    <a class="skip-link" href="#main">{{ __('storefront.skip') }}</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="brand" id="storefront-brand" href="{{ $homeSection }}#page-top">
                <span class="brand-icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></span>
                <span>Batik Rubung Kuning</span>
            </a>
            <nav class="desktop-nav" aria-label="{{ __('storefront.primary_nav') }}">
                <a href="{{ $homeSection }}#home" @if ($onHome) aria-current="page" @endif>{{ __('storefront.home') }}</a>
                <a href="{{ route('company.catalog') }}" @if (! $onHome) aria-current="page" @endif>{{ __('storefront.collections') }}</a>
                <a href="{{ $homeSection }}#store-info">{{ __('storefront.about') }}</a>
                <a href="{{ $homeSection }}#download-app">{{ __('storefront.app') }}</a>
            </nav>
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
            <a class="button button-primary header-contact" href="{{ $homeSection }}#contact">{{ __('storefront.contact') }}</a>
            <details class="mobile-nav">
                <summary>{{ __('storefront.menu') }}</summary>
                <nav aria-label="{{ __('storefront.mobile_nav') }}">
                    <a href="{{ $homeSection }}#home" @if ($onHome) aria-current="page" @endif>{{ __('storefront.home') }}</a>
                    <a href="{{ route('company.catalog') }}" @if (! $onHome) aria-current="page" @endif>{{ __('storefront.collections') }}</a>
                    <a href="{{ $homeSection }}#store-info">{{ __('storefront.about') }}</a>
                    <a href="{{ $homeSection }}#download-app">{{ __('storefront.app') }}</a>
                    <a href="{{ $homeSection }}#contact">{{ __('storefront.contact') }}</a>
                </nav>
            </details>
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    @include('storefront.footer')

    <a class="back-to-top" href="#page-top" aria-label="{{ __('storefront.back_to_top') }}"
        title="{{ __('storefront.back_to_top') }}" hidden>
        <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
    </a>

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
        const backToTop = document.querySelector('.back-to-top');
        const updateBackToTop = () => { backToTop.hidden = window.scrollY < 480; };

        updateBackToTop();
        window.addEventListener('scroll', updateBackToTop, { passive: true });
        backToTop.addEventListener('click', () => {
            document.getElementById('storefront-brand').focus({ preventScroll: true });
        });

        document.querySelector('[data-contact-feedback]')?.focus();
    </script>
</body>
</html>
