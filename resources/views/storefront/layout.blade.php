<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kenali Batik Rubung Kuning di Jember, jelajahi koleksi batik, dan hubungi tim untuk pesanan Anda.">
    <title>@yield('title', 'Batik Rubung Kuning | Galeri Batik Jember')</title>
    <link rel="stylesheet" href="{{ asset('css/company-profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <a class="skip-link" href="#main">Lewati ke konten utama</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></span>
                <span>Batik Rubung Kuning</span>
            </a>
            <nav class="desktop-nav" aria-label="Navigasi utama">
                <a href="{{ route('company.catalog') }}">Koleksi</a>
                <a href="{{ route('company.home') }}#store-info">Tentang Kami</a>
                <a href="{{ route('company.home') }}#download-app">Aplikasi</a>
            </nav>
            <a class="button button-primary header-contact" href="{{ route('company.home') }}#contact">Hubungi Toko</a>
            <details class="mobile-nav">
                <summary>Menu</summary>
                <nav aria-label="Navigasi seluler">
                    <a href="{{ route('company.catalog') }}">Koleksi</a>
                    <a href="{{ route('company.home') }}#store-info">Tentang Kami</a>
                    <a href="{{ route('company.home') }}#download-app">Aplikasi</a>
                    <a href="{{ route('company.home') }}#contact">Hubungi Toko</a>
                </nav>
            </details>
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    @include('storefront.footer')

    <script>
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
