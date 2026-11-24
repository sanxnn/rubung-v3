<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Jelajahi koleksi Batik Rubung Kuning dan hubungi kami untuk kebutuhan batik pribadi atau seragam.">
    <title>Batik Rubung Kuning | Koleksi Batik &amp; Pesanan Khusus</title>
    <script>
        (() => {
            let theme;
            try { theme = localStorage.getItem('brk-landing-theme'); } catch {}
            document.documentElement.dataset.theme =
                theme === 'dark' || theme === 'light' ? theme :
                (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('landing/company-profile.css') }}">
    <script defer src="{{ asset('landing/company-profile.js') }}"></script>
</head>
<body id="top">
    <a class="skip" href="#main">Lewati ke konten</a>
    <header class="header">
        <div class="wrap header-inner">
            <a class="brand" href="#top">
                <span class="mark" aria-hidden="true">RK</span>
                <span>Batik Rubung Kuning</span>
            </a>
            <nav aria-label="Navigasi utama">
                <a href="#catalog">Koleksi</a>
                <a href="#store-info">Layanan</a>
                <a href="#download-app">Aplikasi</a>
            </nav>
            <div class="header-actions">
                <button class="theme-toggle" type="button" aria-label="Mode gelap" aria-pressed="false" hidden>
                    <span class="moon" aria-hidden="true">☾</span>
                    <span class="sun" aria-hidden="true">☀</span>
                </button>
                <a class="button small secondary" href="#contact">Hubungi kami <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </header>

    <main id="main">
        <section class="hero">
            <div class="wrap hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow"><span></span> KOLEKSI BATIK &amp; PESANAN KHUSUS</p>
                    <h1>Batik untuk setiap <em>cerita.</em></h1>
                    <p class="intro">Temukan motif untuk keseharian, acara istimewa, dan seragam. Jelajahi koleksi Batik Rubung Kuning atau ceritakan kebutuhan pesananmu.</p>
                    <div class="actions">
                        <a class="button primary" href="#catalog">Jelajahi koleksi <span aria-hidden="true">↗</span></a>
                        <a class="button secondary" href="#contact">Tanya pesanan khusus</a>
                    </div>
                </div>
                <div class="showcase">
                    <div class="showcase-top"><span class="mark" aria-hidden="true">RK</span> <span>Batik Rubung Kuning</span><a href="#catalog" aria-label="Lihat koleksi">↗</a></div>
                    <div class="showcase-art motif">
                        @if ($products->first()?->image)
                            <img src="{{ url('/api/storage-image/'.$products->first()->image) }}" alt="{{ $products->first()->name }}" fetchpriority="high" data-product-image>
                        @endif
                    </div>
                    <div class="showcase-bottom">
                        <div><span>Koleksi pilihan</span>
                        <strong>{{ $products->first()?->name ?? 'Batik Rubung Kuning' }}</strong></div>
                        <a class="round-link" href="#catalog" aria-label="Jelajahi koleksi">↗</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section wrap" id="catalog">
            <div class="section-heading"><div><p class="eyebrow">KATALOG</p><h2>Koleksi batik pilihan.</h2></div><p>Jelajahi beberapa produk dari katalog Batik Rubung Kuning.</p></div>
            @if ($products->isEmpty())
                <div class="empty"><p>Ingin tahu motif dan produk yang tersedia?</p><a href="#contact">Tanyakan ke tim kami ↗</a></div>
            @else
                <div class="cards">
                    @foreach ($products as $product)
                        <article class="card">
                            <div class="card-art motif">
                                @if ($product->image)
                                    <img src="{{ url('/api/storage-image/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy" data-product-image>
                                @endif
                            </div>
                            <div class="card-body">
                                @if ($product->category)
                                    <p>{{ $product->category->name }}</p>
                                @endif
                                <h3>{{ $product->name }}</h3>
                                <p>Mulai Rp{{ number_format($product->variants_min_price, 0, ',', '.') }}</p>
                                <a class="text-link" href="#contact" data-product="{{ $product->name }}">Tanya produk <span aria-hidden="true">↗</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="band" id="store-info">
            <div class="section wrap">
                <div class="section-heading"><div><p class="eyebrow">LAYANAN</p><h2>Belanja sesuai kebutuhanmu.</h2></div></div>
                <div class="cards services">
                    <article class="card card-body"><h3>Koleksi batik</h3><p>Temukan motif dan pilihan produk untuk dipakai sendiri atau dijadikan hadiah.</p><a class="text-link" href="#catalog">Lihat koleksi ↗</a></article>
                    <article class="card card-body"><h3>Pesanan khusus</h3><p>Diskusikan motif, kebutuhan seragam, atau pesanan dalam jumlah besar.</p><a class="text-link" href="#contact">Diskusikan pesanan ↗</a></article>
                    <article class="card card-body"><h3>Bantuan pemesanan</h3><p>Tanyakan ketersediaan produk, cara pesan, atau pengiriman.</p><a class="text-link" href="#contact">Hubungi tim ↗</a></article>
                </div>
            </div>
        </section>

        <section class="section wrap" id="download-app">
            <div class="app-panel">
                <div><p class="eyebrow">APLIKASI RUBUNG KUNING</p><h2>Koleksi dan pesanan dalam satu aplikasi.</h2><p>Lihat katalog, ajukan pesanan khusus, dan cek status pesanan dari ponsel.</p></div>
                <a class="button primary" href="#contact">Tanya akses aplikasi <span aria-hidden="true">↗</span></a>
            </div>
        </section>

        <section class="section wrap contact" id="contact">
            <div><p class="eyebrow">KONTAK</p><h2>Ada yang ingin kamu tanyakan?</h2><p>Kirim detail kebutuhanmu. Tim Batik Rubung Kuning dapat membalas melalui email yang kamu cantumkan.</p></div>
            <div class="card card-body">
                @if (session('contact_success'))
                    <p class="notice" role="status">{{ session('contact_success') }}</p>
                @endif
                @if ($errors->any())
                    <div class="notice error" role="alert"><ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul></div>
                @endif
                <form action="{{ route('contact.send') }}#contact" method="POST">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" hidden aria-hidden="true">
                    <div class="form-row">
                        <label>Nama<input name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="100"></label>
                        <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="100"></label>
                    </div>
                    <label>Pesan<textarea id="contact-message" name="message" rows="5" required minlength="10" maxlength="5000">{{ old('message') }}</textarea></label>
                    <button type="submit" class="button primary">Kirim pesan <span aria-hidden="true">↗</span></button>
                </form>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="wrap footer-inner">
            <a class="brand" href="#top"><span class="mark" aria-hidden="true">RK</span> Batik Rubung Kuning</a>
            <p>© {{ now()->year }} Batik Rubung Kuning</p>
            <a href="#top">Kembali ke atas ↑</a>
        </div>
    </footer>
</body>
</html>
