<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kenali Batik Rubung Kuning di Jember, jelajahi koleksi batik, dan hubungi tim untuk pesanan Anda.">
    <title>Batik Rubung Kuning | Galeri Batik Jember</title>
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
                <a href="#catalog">Koleksi</a>
                <a href="#store-info">Tentang Kami</a>
                <a href="#download-app">Aplikasi</a>
            </nav>
            <a class="button button-primary header-contact" href="#contact">Hubungi Toko</a>
            <details class="mobile-nav">
                <summary>Menu</summary>
                <nav aria-label="Navigasi seluler">
                    <a href="#catalog">Koleksi</a>
                    <a href="#store-info">Tentang Kami</a>
                    <a href="#download-app">Aplikasi</a>
                    <a href="#contact">Hubungi Toko</a>
                </nav>
            </details>
        </div>
    </header>

    <main id="main">
        <section class="hero shell" aria-labelledby="hero-title">
            <div class="hero-copy">
                <p class="location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Jember, Indonesia</p>
                <h1 id="hero-title">Batik dari Jember, <span>untuk setiap kesempatan.</span></h1>
                <p>Jelajahi koleksi batik Rubung Kuning untuk pemakaian sehari-hari, acara, dan pesanan khusus.</p>
                <div class="actions">
                    <a class="button button-primary" href="#catalog">Jelajahi Koleksi</a>
                    <a class="button button-secondary" href="#contact">Tanya Pesanan</a>
                </div>
            </div>
            <figure class="hero-visual">
                <img src="{{ asset('images/batik-company-profile.webp') }}"
                    alt="Ilustrasi kain batik biru indigo dan canting" width="1200" height="900" fetchpriority="high">
                <figcaption>Ilustrasi kain batik dan canting</figcaption>
            </figure>
        </section>

        <section id="catalog" class="section section-soft" aria-labelledby="catalog-title">
            <div class="shell">
                <div class="section-heading">
                    <h2 id="catalog-title">Koleksi Batik</h2>
                    <p>Temukan pilihan berdasarkan teknik, kebutuhan, dan cara Anda memakai batik.</p>
                </div>
                <div class="collection-grid">
                    <article class="collection-card">
                        <span class="feature-icon"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i></span>
                        <h3>Batik Tulis</h3>
                        <p>Kain dengan motif yang digambar menggunakan canting. Tanyakan pilihan motif dan ukuran kepada tim kami.</p>
                    </article>
                    <article class="collection-card">
                        <span class="feature-icon"><i class="fa-solid fa-stamp" aria-hidden="true"></i></span>
                        <h3>Batik Cap</h3>
                        <p>Pilihan kain bermotif berulang untuk koleksi pribadi, seragam, dan kebutuhan kelompok.</p>
                    </article>
                    <article class="collection-card">
                        <span class="feature-icon"><i class="fa-solid fa-shirt" aria-hidden="true"></i></span>
                        <h3>Busana &amp; Aksesori</h3>
                        <p>Pilihan siap pakai dan pelengkap berbahan batik untuk berbagai kesempatan.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="store-info" class="section shell split" aria-labelledby="store-title">
            <div class="section-heading">
                <h2 id="store-title">Tentang Rubung Kuning</h2>
                <p>Batik Rubung Kuning menyediakan koleksi batik untuk pelanggan perorangan dan kebutuhan kelompok. Tim kami dapat membantu memilih motif, bahan, dan ukuran sebelum Anda memesan.</p>
            </div>
            <ul class="service-card">
                <li>
                    <span class="feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                    <div><strong>Lokasi</strong><p>Jember, Jawa Timur</p></div>
                </li>
                <li>
                    <span class="feature-icon"><i class="fa-solid fa-box" aria-hidden="true"></i></span>
                    <div><strong>Pesanan khusus</strong><p>Sampaikan kebutuhan motif, ukuran, atau jumlah untuk dibahas bersama tim.</p></div>
                </li>
            </ul>
        </section>

        <section id="download-app" class="shell" aria-labelledby="app-title">
            <div class="app-panel">
                <div class="section-heading">
                    <h2 id="app-title">Katalog dan pesanan dalam satu aplikasi</h2>
                    <p>Aplikasi pelanggan memuat katalog, checkout, riwayat pesanan, dan informasi pengiriman.</p>
                </div>
                <a class="button button-secondary" href="#contact">Tanyakan Akses Aplikasi</a>
            </div>
        </section>

        <section id="contact" class="section shell split contact-section" aria-labelledby="contact-title">
            <div class="section-heading">
                <h2 id="contact-title">Hubungi Toko</h2>
                <p>Tanyakan ketersediaan koleksi, rencana kunjungan, atau kebutuhan pesanan khusus melalui formulir ini.</p>
            </div>
            <form class="contact-form" action="{{ route('contact.send') }}" method="POST">
                @csrf
                <h3>Kirim pesan</h3>
                @if (session('contact_success'))
                    <p class="notice notice-success" role="status" tabindex="-1" data-contact-feedback>{{ session('contact_success') }}</p>
                @endif
                @if ($errors->any())
                    <div class="notice notice-error" role="alert" tabindex="-1" data-contact-feedback>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <input type="text" name="website" tabindex="-1" autocomplete="off" hidden>
                <div class="field-row">
                    <div class="field">
                        <label for="contact-name">Nama lengkap</label>
                        <input id="contact-name" type="text" name="name" value="{{ old('name') }}"
                            required maxlength="100" autocomplete="name" @error('name') aria-invalid="true" @enderror>
                    </div>
                    <div class="field">
                        <label for="contact-email">Email</label>
                        <input id="contact-email" type="email" name="email" value="{{ old('email') }}"
                            required maxlength="100" autocomplete="email" @error('email') aria-invalid="true" @enderror>
                    </div>
                </div>
                <div class="field">
                    <label for="contact-message">Pesan</label>
                    <textarea id="contact-message" name="message" rows="5" required minlength="10" maxlength="5000"
                        @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                </div>
                <button class="button button-primary" type="submit">Kirim Pesan</button>
            </form>
        </section>
    </main>

    <footer class="site-footer">
        <div class="shell footer-inner">
            <p>© {{ date('Y') }} Batik Rubung Kuning</p>
            <a href="{{ route('login') }}">Login Admin</a>
        </div>
    </footer>

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
