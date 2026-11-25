@extends('storefront.layout')

@section('content')
        <section id="home" class="hero-section" aria-labelledby="hero-title">
          <div class="hero shell">
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
          </div>
        </section>

        <section id="catalog" class="section section-soft" aria-labelledby="catalog-title">
            <div class="shell">
                <div class="section-heading section-heading-row">
                    <div>
                        <h2 id="catalog-title">{{ $featured->first()?->units_sold > 0 ? 'Batik Terlaris' : 'Pilihan Batik' }}</h2>
                        <p>Produk dengan penjualan tertinggi ditampilkan lebih dulu.</p>
                    </div>
                    <a class="button button-secondary" href="{{ route('company.catalog') }}">Lihat Produk Selengkapnya</a>
                </div>
                @if ($featured->isEmpty())
                    <p class="empty-state">Koleksi Batik belum tersedia.</p>
                @else
                    <div class="collection-grid">
                        @foreach ($featured as $product)
                            @include('storefront.product-card', ['showSales' => true])
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section id="store-info" class="section shell split" aria-labelledby="store-title">
            <div class="section-heading">
                <h2 id="store-title">Tentang Rubung Kuning</h2>
                <p>Batik Rubung Kuning menyediakan koleksi batik untuk pelanggan perorangan dan kebutuhan kelompok. Dari pilihan kain hingga pesanan kelompok, Anda dapat mendiskusikan motif, ukuran, dan kebutuhan sebelum memesan.</p>
                <p>Kami melayani pemesanan dari Jember. Untuk kunjungan, tanyakan lokasi dan jadwal melalui formulir kontak agar tim dapat membantu menyiapkannya.</p>
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
                <li>
                    <span class="feature-icon"><i class="fa-solid fa-truck" aria-hidden="true"></i></span>
                    <div><strong>Pengiriman</strong><p>Ongkir dihitung saat checkout berdasarkan alamat yang dipilih. Resi dapat dilihat pada detail pesanan setelah dikirim.</p></div>
                </li>
            </ul>
        </section>

        <section id="download-app" class="shell" aria-labelledby="app-title">
            <div class="app-panel">
                <div class="section-heading">
                    <h2 id="app-title">Katalog dan pesanan dalam satu aplikasi</h2>
                    <p>Aplikasi pelanggan memuat katalog, checkout, riwayat pesanan, dan informasi pengiriman.</p>
                </div>
                <div class="app-download">
                    @include('storefront.app-badges')
                    @unless (config('services.storefront.google_play_url') && config('services.storefront.app_store_url'))
                        <a class="text-link" href="#contact">Tanyakan akses aplikasi</a>
                    @endunless
                </div>
            </div>
        </section>

        <section id="contact" class="section shell split contact-section" aria-labelledby="contact-title">
            <div class="section-heading">
                <h2 id="contact-title">Hubungi Toko</h2>
                <p>Kirim pertanyaan tentang koleksi, pengiriman, atau kebutuhan pesanan khusus. Untuk pesanan yang sudah dibuat, sertakan nomor pesanan agar tim dapat menelusurinya.</p>
                <ul class="contact-info">
                    <li><span class="feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                        <div><strong>Jember, Jawa Timur</strong><p>Hubungi tim sebelum merencanakan kunjungan.</p></div></li>
                    <li><span class="feature-icon"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
                        <div><strong>Layanan pelanggan</strong><p>Kami membalas melalui email yang Anda masukkan.</p>
                            @if ($publicEmail = config('services.storefront.public_email'))
                                <a class="text-link" href="mailto:{{ $publicEmail }}">{{ $publicEmail }}</a>
                            @endif
                        </div></li>
                </ul>
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
@endsection
