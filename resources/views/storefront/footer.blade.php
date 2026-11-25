<footer class="site-footer">
    <div class="shell footer-grid">
        <div>
            <a class="brand" href="{{ route('company.home') }}">
                <span class="brand-icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></span>
                <span>Batik Rubung Kuning</span>
            </a>
            <p>Koleksi batik dan layanan pesanan khusus dari Jember, Jawa Timur.</p>
            @if ($publicEmail = config('services.storefront.public_email'))
                <a class="text-link" href="mailto:{{ $publicEmail }}">{{ $publicEmail }}</a>
            @endif
        </div>
        <nav aria-label="Navigasi footer">
            <strong>Jelajahi</strong>
            <a href="{{ route('company.home') }}#home">Beranda</a>
            <a href="{{ route('company.home') }}#catalog">Pilihan Batik</a>
            <a href="{{ route('company.catalog') }}">Semua Produk</a>
            <a href="{{ route('company.home') }}#store-info">Tentang Kami</a>
            <a href="{{ route('company.home') }}#download-app">Aplikasi</a>
            <a href="{{ route('company.home') }}#contact">Hubungi Toko</a>
        </nav>
        <div>
            <strong>Aplikasi Pelanggan</strong>
            @include('storefront.app-badges')
        </div>
    </div>
    <div class="shell footer-inner">
        <p>© {{ date('Y') }} Batik Rubung Kuning</p>
        <a href="{{ route('login') }}">Login Admin</a>
    </div>
</footer>
