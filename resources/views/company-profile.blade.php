<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batik Rubung Kuning — Platform & Galeri Batik Profesional</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pure: '#FFFFFF',
                        surface: '#FFFDF7',
                        sunsoft: '#FFF8E1',
                        sunlight: '#FACC15',
                        amberbrand: '#EAB308',
                        golddeep: '#CA8A04',
                        ink: '#111827',
                        muted: '#4B5563',
                        line: '#FDE68A',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        display: ['"Playfair Display"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>

    {{-- Google Fonts: Plus Jakarta Sans (SaaS UI/Body) & Playfair Display (Heritage Accent) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        ::-webkit-scrollbar {
            width: 7px;
        }

        ::-webkit-scrollbar-track {
            background: #FFFFFF;
        }

        ::-webkit-scrollbar-thumb {
            background: #EAB308;
            border-radius: 9999px;
        }

        .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .saas-grid-bg {
            background-image:
                linear-gradient(to right, rgba(234, 179, 8, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(234, 179, 8, 0.08) 1px, transparent 1px);
            background-size: 36px 36px;
        }
    </style>
</head>

<body class="bg-pure text-ink font-sans antialiased selection:bg-sunlight selection:text-ink">

    {{-- ============================================================ --}}
    {{-- NAVBAR (SAAS CLEAN WHITE & YELLOW ACCENT)                     --}}
    {{-- ============================================================ --}}
    <header id="navbar" class="sticky top-0 z-50 bg-pure/90 backdrop-blur-md border-b border-amber-200/70 transition-shadow">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 h-20 flex items-center justify-between">
            <a href="#top" class="flex items-center gap-3 group">
                <div
                    class="w-10 h-10 rounded-xl bg-sunlight border border-amber-400 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                    <span class="font-display font-bold text-ink text-lg">RK</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-ink text-base tracking-tight">Batik Rubung Kuning</span>
                        <span
                            class="hidden sm:inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-sunsoft text-golddeep border border-amber-300">
                            Official
                        </span>
                    </div>
                    <p class="text-xs text-muted">Galeri &amp; Katalog Digital</p>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-muted">
                <a href="#metrics" class="hover:text-ink transition-colors">Statistik</a>
                <a href="#store-info" class="hover:text-ink transition-colors">Informasi Toko</a>
                <a href="#catalog" class="hover:text-ink transition-colors">Produk</a>
                <a href="#download-app" class="hover:text-ink transition-colors">Download Apps</a>
                <a href="#contact" class="hover:text-ink transition-colors">Kontak</a>
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                <a href="#download-app"
                    class="px-4 py-2 text-sm font-semibold text-ink bg-sunsoft hover:bg-sunlight border border-amber-300 rounded-lg transition-colors">
                    Unduh Aplikasi
                </a>
                <a href="#contact"
                    class="px-4 py-2 text-sm font-semibold text-ink bg-sunlight hover:bg-amberbrand rounded-lg shadow-sm transition-colors">
                    Hubungi Toko
                </a>
            </div>

            <button id="menuBtn" type="button" aria-label="Buka Menu"
                class="lg:hidden p-2 rounded-lg border border-amber-200 text-ink hover:bg-sunsoft transition-colors">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Mobile Drawer --}}
        <div id="mobileMenu" class="lg:hidden hidden bg-pure border-t border-amber-200/70">
            <div class="px-6 py-5 space-y-3 text-sm font-medium text-muted">
                <a href="#metrics" class="block py-1.5 hover:text-ink">Statistik Pengguna &amp; Produk</a>
                <a href="#store-info" class="block py-1.5 hover:text-ink">Informasi Toko</a>
                <a href="#catalog" class="block py-1.5 hover:text-ink">Katalog Produk</a>
                <a href="#download-app" class="block py-1.5 hover:text-ink">Download Apps</a>
                <a href="#contact" class="block py-1.5 hover:text-ink">Kontak &amp; Pesan</a>
                <div class="pt-3 flex gap-3 border-t border-amber-100">
                    <a href="#download-app"
                        class="flex-1 text-center py-2.5 rounded-lg bg-sunsoft border border-amber-300 font-semibold text-ink">
                        Apps
                    </a>
                    <a href="#contact"
                        class="flex-1 text-center py-2.5 rounded-lg bg-sunlight font-semibold text-ink">
                        Kontak
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main id="top">
        {{-- ============================================================ --}}
        {{-- HERO SECTION (SAAS SPLIT LAYOUT)                              --}}
        {{-- ============================================================ --}}
        <section class="relative overflow-hidden bg-pure saas-grid-bg border-b border-amber-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-24">
                <div class="grid lg:grid-cols-12 gap-12 items-center">
                    {{-- Left Content --}}
                    <div class="lg:col-span-6 space-y-6">
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sunsoft border border-amber-300 text-xs font-semibold text-golddeep">
                            <span class="w-2 h-2 rounded-full bg-amberbrand"></span>
                            Platform Katalog &amp; Pemesanan Batik Rubung Kuning
                        </div>

                        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-ink leading-[1.12]">
                            Batik Berkualitas Tinggi, <br>
                            <span class="font-display italic font-bold text-golddeep">Terhubung &amp; Terkelola</span>
                        </h1>

                        <p class="text-base sm:text-lg text-muted leading-relaxed max-w-xl">
                            Batik Rubung Kuning menghadirkan koleksi batik tulis, cap, busana siap pakai, dan
                            kebutuhan seragam korporat dengan pengalaman belanja digital yang cepat, transparan, dan
                            terintegrasi langsung dengan galeri kami.
                        </p>

                        <div class="flex flex-wrap items-center gap-3.5 pt-1">
                            <a href="#download-app"
                                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-sunlight hover:bg-amberbrand text-ink font-bold text-sm shadow-sm transition-all">
                                <i data-lucide="smartphone" class="w-4 h-4"></i>
                                Download Apps Sekarang
                            </a>
                            <a href="#catalog"
                                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-pure hover:bg-sunsoft text-ink font-semibold text-sm border border-amber-300 transition-colors">
                                Jelajahi Produk
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>

                        {{-- Quick Trust Strip --}}
                        <div class="pt-4 flex flex-wrap items-center gap-6 text-xs font-medium text-muted">
                            <span class="inline-flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-golddeep"></i>
                                Pengrajin Terverifikasi
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-golddeep"></i>
                                Katalog Stok Real-Time
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-golddeep"></i>
                                Dukungan Pesanan Korporat
                            </span>
                        </div>
                    </div>

                    {{-- Right Visual (Dummy SVG Dashboard + Batik Preview) --}}
                    <div class="lg:col-span-6">
                        <div class="bg-pure rounded-2xl border border-amber-200 shadow-xl shadow-amber-500/5 p-5 sm:p-6">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-amber-100">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-golddeep">Ringkasan
                                        Operasional</p>
                                    <h2 class="text-base font-bold text-ink">Rubung Kuning Storefront</h2>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-sunsoft text-golddeep border border-amber-200">
                                    Aktif • Solo &amp; Online
                                </span>
                            </div>

                            {{-- Dummy Batik Showcase Card (Inline SVG Data URI) --}}
                            <div class="rounded-xl overflow-hidden border border-amber-200/80 bg-sunsoft mb-5">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 380'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0%25' stop-color='%23FFFBEB'/%3E%3Cstop offset='100%25' stop-color='%23FEF08A'/%3E%3C/linearGradient%3E%3Cpattern id='p' width='48' height='48' patternUnits='userSpaceOnUse'%3E%3Ccircle cx='24' cy='24' r='14' fill='none' stroke='%23CA8A04' stroke-opacity='0.22' stroke-width='2'/%3E%3Cpath d='M24 6 L30 24 L24 42 L18 24 Z' fill='%23EAB308' fill-opacity='0.18'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='800' height='380' fill='url(%23g)'/%3E%3Crect width='800' height='380' fill='url(%23p)'/%3E%3Crect x='28' y='28' width='744' height='324' rx='16' fill='%23FFFFFF' fill-opacity='0.88' stroke='%23FACC15' stroke-width='2'/%3E%3Ctext x='64' y='96' font-family='sans-serif' font-size='14' font-weight='700' fill='%23CA8A04'%3EDUMMY PREVIEW • KOLEKSI UNGGULAN%3C/text%3E%3Ctext x='64' y='142' font-family='Georgia, serif' font-size='34' font-weight='700' fill='%23111827'%3EBatik Rubung Kuning%3C/text%3E%3Ctext x='64' y='180' font-family='sans-serif' font-size='17' fill='%234B5563'%3EMotif Klasik &amp; Kontemporer • Katun Primissima &amp; Sutra%3C/text%3E%3Crect x='64' y='215' width='190' height='42' rx='10' fill='%23FACC15'/%3E%3Ctext x='92' y='241' font-family='sans-serif' font-size='14' font-weight='700' fill='%23111827'%3E12.480+ Pengguna%3C/text%3E%3Crect x='272' y='215' width='190' height='42' rx='10' fill='%23FFFBEB' stroke='%23EAB308'/%3E%3Ctext x='300' y='241' font-family='sans-serif' font-size='14' font-weight='700' fill='%23111827'%3E640+ Produk Aktif%3C/text%3E%3C/svg%3E"
                                    alt="Dummy Preview Batik Rubung Kuning" class="w-full h-48 sm:h-56 object-cover">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-xl bg-surface border border-amber-200/70">
                                    <p class="text-xs font-medium text-muted">Total Pengguna Terdaftar</p>
                                    <p class="text-2xl font-extrabold text-ink mt-1">12.480</p>
                                    <p class="text-xs font-semibold text-golddeep mt-1">+18,4% bulan ini</p>
                                </div>
                                <div class="p-4 rounded-xl bg-surface border border-amber-200/70">
                                    <p class="text-xs font-medium text-muted">Total Produk Tersedia</p>
                                    <p class="text-2xl font-extrabold text-ink mt-1">640</p>
                                    <p class="text-xs font-semibold text-golddeep mt-1">4 kategori utama</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- TOTAL PENGGUNA & TOTAL PRODUK (KPI METRICS)                   --}}
        {{-- ============================================================ --}}
        <section id="metrics" class="py-16 lg:py-20 bg-surface border-b border-amber-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 reveal">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-golddeep">Metrik Utama</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-ink mt-1">
                            Total Pengguna &amp; Total Produk
                        </h2>
                    </div>
                    <p class="text-sm text-muted max-w-md">
                        Data ringkas performa ekosistem Batik Rubung Kuning yang memadukan galeri fisik dan aplikasi
                        digital.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Metric 1: Total Pengguna --}}
                    <div class="reveal bg-pure rounded-2xl p-6 border border-amber-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted">Total Pengguna</span>
                            <div class="w-10 h-10 rounded-xl bg-sunsoft flex items-center justify-center text-golddeep">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-ink">12.480</p>
                        <p class="text-xs text-muted mt-2">
                            <span class="font-bold text-golddeep">8.920</span> pengguna aplikasi &amp;
                            <span class="font-bold text-ink">3.560</span> akun web/mitra
                        </p>
                    </div>

                    {{-- Metric 2: Total Produk --}}
                    <div class="reveal bg-pure rounded-2xl p-6 border border-amber-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted">Total Produk</span>
                            <div class="w-10 h-10 rounded-xl bg-sunsoft flex items-center justify-center text-golddeep">
                                <i data-lucide="package" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-ink">640</p>
                        <p class="text-xs text-muted mt-2">
                            <span class="font-bold text-golddeep">512</span> siap kirim &amp;
                            <span class="font-bold text-ink">128</span> edisi terbatas/custom
                        </p>
                    </div>

                    {{-- Metric 3: Total Unduhan App --}}
                    <div class="reveal bg-pure rounded-2xl p-6 border border-amber-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted">Unduhan Aplikasi</span>
                            <div class="w-10 h-10 rounded-xl bg-sunsoft flex items-center justify-center text-golddeep">
                                <i data-lucide="download" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-ink">9.350+</p>
                        <p class="text-xs text-muted mt-2">
                            Tersedia di Android &amp; iOS dengan rating rata-rata <span class="font-bold text-ink">4,9/5</span>
                        </p>
                    </div>

                    {{-- Metric 4: Klien Korporat --}}
                    <div class="reveal bg-pure rounded-2xl p-6 border border-amber-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted">Klien &amp; Komunitas</span>
                            <div class="w-10 h-10 rounded-xl bg-sunsoft flex items-center justify-center text-golddeep">
                                <i data-lucide="building-2" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-ink">145+</p>
                        <p class="text-xs text-muted mt-2">
                            Instansi &amp; perusahaan serta <span class="font-bold text-ink">50+</span> pengrajin binaan
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- INFORMASI TOKO                                                --}}
        {{-- ============================================================ --}}
        <section id="store-info" class="py-20 lg:py-24 bg-pure border-b border-amber-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-12 gap-12 items-start">
                    {{-- Left: Store Visual + Overview --}}
                    <div class="lg:col-span-5 space-y-6 reveal">
                        <div class="rounded-2xl overflow-hidden border border-amber-200 bg-sunsoft">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 420'%3E%3Crect width='640' height='420' fill='%23FFFBEB'/%3E%3Crect x='24' y='24' width='592' height='372' rx='18' fill='%23FFFFFF' stroke='%23FACC15' stroke-width='2'/%3E%3Ccircle cx='320' cy='155' r='54' fill='%23FEF08A' stroke='%23EAB308' stroke-width='2'/%3E%3Ctext x='320' y='166' text-anchor='middle' font-family='Georgia, serif' font-size='32' font-weight='700' fill='%23111827'%3ERK%3C/text%3E%3Ctext x='320' y='248' text-anchor='middle' font-family='sans-serif' font-size='22' font-weight='700' fill='%23111827'%3EGaleri &amp; Workshop Solo%3C/text%3E%3Ctext x='320' y='278' text-anchor='middle' font-family='sans-serif' font-size='14' fill='%234B5563'%3EJl. Batik Indah No. 88, Surakarta, Jawa Tengah%3C/text%3E%3Crect x='170' y='308' width='300' height='36' rx='18' fill='%23FACC15'/%3E%3Ctext x='320' y='331' text-anchor='middle' font-family='sans-serif' font-size='13' font-weight='700' fill='%23111827'%3EDUMMY STOREFRONT ASSET%3C/text%3E%3C/svg%3E"
                                alt="Dummy Foto Galeri Batik Rubung Kuning" class="w-full h-64 object-cover">
                        </div>

                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest text-golddeep">Informasi Toko</span>
                            <h2 class="text-3xl font-extrabold text-ink mt-1">
                                Galeri &amp; Pusat Layanan Batik Rubung Kuning
                            </h2>
                            <p class="text-sm sm:text-base text-muted leading-relaxed mt-3">
                                Kami melayani pembelian satuan, konsultasi bahan dan motif, hingga pengadaan seragam
                                instansi maupun suvenir resmi. Setiap produk melewati proses kurasi kualitas kain dan
                                pewarnaan sebelum masuk katalog toko.
                            </p>
                        </div>
                    </div>

                    {{-- Right: Store Details Cards --}}
                    <div class="lg:col-span-7 grid sm:grid-cols-2 gap-5 reveal">
                        <div class="p-6 rounded-2xl bg-surface border border-amber-200/80">
                            <div class="w-10 h-10 rounded-xl bg-sunlight/30 flex items-center justify-center text-golddeep mb-4">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-ink text-base">Alamat Galeri Utama</h3>
                            <p class="text-sm text-muted mt-1.5 leading-relaxed">
                                Jl. Batik Indah No. 88, Kota Surakarta (Solo), Jawa Tengah 57100
                            </p>
                        </div>

                        <div class="p-6 rounded-2xl bg-surface border border-amber-200/80">
                            <div class="w-10 h-10 rounded-xl bg-sunlight/30 flex items-center justify-center text-golddeep mb-4">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-ink text-base">Jam Operasional Toko</h3>
                            <p class="text-sm text-muted mt-1.5 leading-relaxed">
                                Senin – Sabtu: 08.30 – 17.30 WIB<br>
                                Minggu &amp; Hari Libur: 10.00 – 15.00 WIB
                            </p>
                        </div>

                        <div class="p-6 rounded-2xl bg-surface border border-amber-200/80">
                            <div class="w-10 h-10 rounded-xl bg-sunlight/30 flex items-center justify-center text-golddeep mb-4">
                                <i data-lucide="phone-call" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-ink text-base">Layanan Pelanggan &amp; WhatsApp</h3>
                            <p class="text-sm text-muted mt-1.5 leading-relaxed">
                                Telepon / WA: +62 812-3456-7890<br>
                                Email: hello@rubungkuning.com
                            </p>
                        </div>

                        <div class="p-6 rounded-2xl bg-surface border border-amber-200/80">
                            <div class="w-10 h-10 rounded-xl bg-sunlight/30 flex items-center justify-center text-golddeep mb-4">
                                <i data-lucide="truck" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-ink text-base">Pengiriman &amp; Pickup</h3>
                            <p class="text-sm text-muted mt-1.5 leading-relaxed">
                                Pengiriman ke seluruh Indonesia dengan kemasan pelindung aman atau ambil langsung di toko.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- KATALOG PRODUK (RINGKAS & PROFESIONAL)                        --}}
        {{-- ============================================================ --}}
        <section id="catalog" class="py-20 lg:py-24 bg-surface border-b border-amber-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12 reveal">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-golddeep">Katalog Produk</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-ink mt-1">
                            Lini Produk Utama (640+ Item)
                        </h2>
                    </div>
                    <p class="text-sm text-muted max-w-md">
                        Seluruh koleksi menggunakan aset ilustrasi dummy sebagai pratinjau katalog sebelum sinkronisasi
                        stok penuh.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Produk 1 --}}
                    <div class="reveal bg-pure rounded-2xl border border-amber-200/80 overflow-hidden shadow-sm flex flex-col">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 260'%3E%3Crect width='400' height='260' fill='%23FFFBEB'/%3E%3Crect x='20' y='20' width='360' height='220' rx='12' fill='%23FEF08A' fill-opacity='0.55' stroke='%23EAB308'/%3E%3Ctext x='200' y='118' text-anchor='middle' font-family='Georgia, serif' font-size='22' font-weight='700' fill='%23111827'%3EBatik Tulis Klasik%3C/text%3E%3Ctext x='200' y='148' text-anchor='middle' font-family='sans-serif' font-size='13' fill='%234B5563'%3EDUMMY ASSET • 140 PRODUK%3C/text%3E%3C/svg%3E"
                            alt="Dummy Produk Batik Tulis" class="w-full h-44 object-cover border-b border-amber-100">
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-golddeep">140 Produk</span>
                                <h3 class="font-bold text-ink text-lg mt-1">Batik Tulis Premium</h3>
                                <p class="text-sm text-muted mt-1.5">
                                    Karya tangan pengrajin pada katun primissima dan sutra dengan motif berkarakter.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Produk 2 --}}
                    <div class="reveal bg-pure rounded-2xl border border-amber-200/80 overflow-hidden shadow-sm flex flex-col">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 260'%3E%3Crect width='400' height='260' fill='%23FFFBEB'/%3E%3Crect x='20' y='20' width='360' height='220' rx='12' fill='%23FDE047' fill-opacity='0.35' stroke='%23EAB308'/%3E%3Ctext x='200' y='118' text-anchor='middle' font-family='Georgia, serif' font-size='22' font-weight='700' fill='%23111827'%3ESeragam Korporat%3C/text%3E%3Ctext x='200' y='148' text-anchor='middle' font-family='sans-serif' font-size='13' fill='%234B5563'%3EDUMMY ASSET • 220 PRODUK%3C/text%3E%3C/svg%3E"
                            alt="Dummy Produk Seragam Korporat" class="w-full h-44 object-cover border-b border-amber-100">
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-golddeep">220 Produk</span>
                                <h3 class="font-bold text-ink text-lg mt-1">Batik Cap &amp; Korporat</h3>
                                <p class="text-sm text-muted mt-1.5">
                                    Konsistensi pola dan warna presisi untuk kebutuhan seragam instansi dan tim kerja.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Produk 3 --}}
                    <div class="reveal bg-pure rounded-2xl border border-amber-200/80 overflow-hidden shadow-sm flex flex-col">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 260'%3E%3Crect width='400' height='260' fill='%23FFFBEB'/%3E%3Crect x='20' y='20' width='360' height='220' rx='12' fill='%23FEF9C3' stroke='%23EAB308'/%3E%3Ctext x='200' y='118' text-anchor='middle' font-family='Georgia, serif' font-size='22' font-weight='700' fill='%23111827'%3EReady-to-Wear%3C/text%3E%3Ctext x='200' y='148' text-anchor='middle' font-family='sans-serif' font-size='13' fill='%234B5563'%3EDUMMY ASSET • 185 PRODUK%3C/text%3E%3C/svg%3E"
                            alt="Dummy Produk Ready to Wear" class="w-full h-44 object-cover border-b border-amber-100">
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-golddeep">185 Produk</span>
                                <h3 class="font-bold text-ink text-lg mt-1">Busana Siap Pakai</h3>
                                <p class="text-sm text-muted mt-1.5">
                                    Kemeja, outer, dan setelan modern yang nyaman untuk aktivitas harian maupun formal.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Produk 4 --}}
                    <div class="reveal bg-pure rounded-2xl border border-amber-200/80 overflow-hidden shadow-sm flex flex-col">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 260'%3E%3Crect width='400' height='260' fill='%23FFFBEB'/%3E%3Crect x='20' y='20' width='360' height='220' rx='12' fill='%23FACC15' fill-opacity='0.22' stroke='%23EAB308'/%3E%3Ctext x='200' y='118' text-anchor='middle' font-family='Georgia, serif' font-size='22' font-weight='700' fill='%23111827'%3EAksesoris &amp; Living%3C/text%3E%3Ctext x='200' y='148' text-anchor='middle' font-family='sans-serif' font-size='13' fill='%234B5563'%3EDUMMY ASSET • 95 PRODUK%3C/text%3E%3C/svg%3E"
                            alt="Dummy Produk Aksesoris dan Living" class="w-full h-44 object-cover border-b border-amber-100">
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-golddeep">95 Produk</span>
                                <h3 class="font-bold text-ink text-lg mt-1">Aksesoris &amp; Home Living</h3>
                                <p class="text-sm text-muted mt-1.5">
                                    Selendang, pouch, cushion, dan paket hadiah eksklusif dengan sentuhan kuning emas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- DOWNLOAD APPS SECTION (SAAS HIGHLIGHT)                        --}}
        {{-- ============================================================ --}}
        <section id="download-app" class="py-20 lg:py-24 bg-pure border-b border-amber-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div
                    class="rounded-3xl bg-sunsoft border border-amber-300/80 p-8 sm:p-12 lg:p-14 grid lg:grid-cols-12 gap-10 items-center reveal">
                    <div class="lg:col-span-7 space-y-5">
                        <span
                            class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-pure border border-amber-300 text-xs font-bold text-golddeep">
                            <i data-lucide="download-cloud" class="w-3.5 h-3.5"></i>
                            Aplikasi Mobile Resmi
                        </span>

                        <h2 class="text-3xl sm:text-4xl font-extrabold text-ink leading-tight">
                            Download Apps Batik Rubung Kuning
                        </h2>

                        <p class="text-sm sm:text-base text-muted leading-relaxed max-w-2xl">
                            Kelola pesanan, cek ketersediaan motif dan ukuran secara langsung, lacak pengiriman, serta
                            akses promo anggota langsung dari smartphone Anda.
                        </p>

                        <ul class="grid sm:grid-cols-2 gap-3 text-sm font-medium text-ink pt-1">
                            <li class="flex items-center gap-2.5">
                                <i data-lucide="check" class="w-4 h-4 text-golddeep flex-shrink-0"></i>
                                Katalog interaktif &amp; detail bahan
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i data-lucide="check" class="w-4 h-4 text-golddeep flex-shrink-0"></i>
                                Notifikasi status pesanan real-time
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i data-lucide="check" class="w-4 h-4 text-golddeep flex-shrink-0"></i>
                                Riwayat pembelian &amp; invoice digital
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i data-lucide="check" class="w-4 h-4 text-golddeep flex-shrink-0"></i>
                                Akses cepat layanan pelanggan toko
                            </li>
                        </ul>

                        <div class="flex flex-wrap gap-3.5 pt-3">
                            <a href="#download-app"
                                class="inline-flex items-center gap-3 px-5 py-3.5 rounded-xl bg-ink hover:bg-neutral-800 text-pure text-left transition-colors shadow-sm">
                                <i data-lucide="play" class="w-5 h-5 text-sunlight"></i>
                                <div>
                                    <span class="block text-[10px] uppercase tracking-wider text-pure/70">Unduh di</span>
                                    <span class="block text-sm font-bold leading-tight">Google Play</span>
                                </div>
                            </a>

                            <a href="#download-app"
                                class="inline-flex items-center gap-3 px-5 py-3.5 rounded-xl bg-pure hover:bg-amber-50 text-ink border border-amber-400 text-left transition-colors">
                                <i data-lucide="apple" class="w-5 h-5 text-ink"></i>
                                <div>
                                    <span class="block text-[10px] uppercase tracking-wider text-muted">Unduh di</span>
                                    <span class="block text-sm font-bold leading-tight">App Store</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Dummy Mobile App Mockup --}}
                    <div class="lg:col-span-5">
                        <div class="bg-pure rounded-2xl border border-amber-200 p-4 shadow-lg">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 420 460'%3E%3Crect width='420' height='460' rx='20' fill='%23FFFDF7'/%3E%3Crect x='65' y='20' width='290' height='420' rx='32' fill='%23FFFFFF' stroke='%23111827' stroke-width='8'/%3E%3Crect x='165' y='32' width='90' height='10' rx='5' fill='%23111827'/%3E%3Crect x='85' y='62' width='250' height='84' rx='14' fill='%23FACC15'/%3E%3Ctext x='103' y='94' font-family='sans-serif' font-size='12' font-weight='700' fill='%23111827'%3ERUBUNG KUNING APP%3C/text%3E%3Ctext x='103' y='122' font-family='sans-serif' font-size='18' font-weight='800' fill='%23111827'%3E12.480 Pengguna Aktif%3C/text%3E%3Crect x='85' y='162' width='118' height='92' rx='12' fill='%23FFFBEB' stroke='%23FACC15'/%3E%3Ctext x='100' y='194' font-family='sans-serif' font-size='20' font-weight='800' fill='%23111827'%3E640%3C/text%3E%3Ctext x='100' y='218' font-family='sans-serif' font-size='12' fill='%234B5563'%3ETotal Produk%3C/text%3E%3Crect x='217' y='162' width='118' height='92' rx='12' fill='%23FFFBEB' stroke='%23FACC15'/%3E%3Ctext x='232' y='194' font-family='sans-serif' font-size='20' font-weight='800' fill='%23111827'%3E4,9 ★%3C/text%3E%3Ctext x='232' y='218' font-family='sans-serif' font-size='12' fill='%234B5563'%3ERating Aplikasi%3C/text%3E%3Crect x='85' y='270' width='250' height='142' rx='14' fill='%23FFFDF7' stroke='%23FDE68A'/%3E%3Ctext x='103' y='302' font-family='sans-serif' font-size='13' font-weight='700' fill='%23111827'%3EDUMMY APP MOCKUP%3C/text%3E%3Ctext x='103' y='328' font-family='sans-serif' font-size='12' fill='%234B5563'%3E• Cek stok kain &amp; ukuran%3C/text%3E%3Ctext x='103' y='352' font-family='sans-serif' font-size='12' fill='%234B5563'%3E• Pesan seragam &amp; custom%3C/text%3E%3Ctext x='103' y='376' font-family='sans-serif' font-size='12' fill='%234B5563'%3E• Lacak status pengiriman%3C/text%3E%3C/svg%3E"
                                alt="Dummy Mockup Aplikasi Mobile Batik Rubung Kuning" class="w-full h-auto rounded-xl">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- KONTAK & FORMULIR PESAN                                       --}}
        {{-- ============================================================ --}}
        <section id="contact" class="py-20 lg:py-24 bg-surface">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-12 gap-12 items-start">
                    <div class="lg:col-span-5 space-y-5 reveal">
                        <span class="text-xs font-bold uppercase tracking-widest text-golddeep">Kontak Toko</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-ink">
                            Butuh Bantuan atau Pesanan Khusus?
                        </h2>
                        <p class="text-sm sm:text-base text-muted leading-relaxed">
                            Kirimkan pertanyaan seputar ketersediaan produk, kunjungan galeri, atau pengadaan dalam
                            jumlah besar. Tim kami siap merespons pada jam operasional.
                        </p>

                        <div class="space-y-4 pt-2 text-sm">
                            <div class="flex items-center gap-3 text-ink">
                                <i data-lucide="map-pin" class="w-4 h-4 text-golddeep"></i>
                                <span>Jl. Batik Indah No. 88, Solo, Jawa Tengah 57100</span>
                            </div>
                            <div class="flex items-center gap-3 text-ink">
                                <i data-lucide="phone" class="w-4 h-4 text-golddeep"></i>
                                <span>+62 812-3456-7890</span>
                            </div>
                            <div class="flex items-center gap-3 text-ink">
                                <i data-lucide="mail" class="w-4 h-4 text-golddeep"></i>
                                <span>hello@rubungkuning.com</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7 reveal">
                        <div class="bg-pure rounded-2xl p-6 sm:p-8 border border-amber-200/80 shadow-sm">
                            <h3 class="text-xl font-bold text-ink mb-5">Kirim Pesan ke Toko</h3>

                            @if (session('contact_success'))
                                <div class="mb-4 p-3.5 rounded-xl bg-sunsoft border border-amber-300 text-sm font-medium text-ink">
                                    {{ session('contact_success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mb-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-muted mb-1.5">
                                            Nama Lengkap
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                                            placeholder="Nama Anda"
                                            class="w-full px-4 py-3 rounded-xl bg-pure border border-amber-200 text-sm text-ink focus:outline-none focus:border-amberbrand focus:ring-2 focus:ring-sunlight/50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-muted mb-1.5">
                                            Email
                                        </label>
                                        <input type="email" name="email" value="{{ old('email') }}" required maxlength="100"
                                            placeholder="nama@email.com"
                                            class="w-full px-4 py-3 rounded-xl bg-pure border border-amber-200 text-sm text-ink focus:outline-none focus:border-amberbrand focus:ring-2 focus:ring-sunlight/50">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-muted mb-1.5">
                                        Pesan
                                    </label>
                                    <textarea rows="4" name="message" required minlength="10" maxlength="5000"
                                        placeholder="Tuliskan kebutuhan produk, ukuran, atau pertanyaan Anda..."
                                        class="w-full px-4 py-3 rounded-xl bg-pure border border-amber-200 text-sm text-ink resize-none focus:outline-none focus:border-amberbrand focus:ring-2 focus:ring-sunlight/50">{{ old('message') }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-sunlight hover:bg-amberbrand text-ink font-bold text-sm transition-colors shadow-sm">
                                    Kirim Pesan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- ============================================================ --}}
    {{-- FOOTER                                                        --}}
    {{-- ============================================================ --}}
    <footer class="bg-pure border-t border-amber-200/80 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-8 border-b border-amber-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-sunlight flex items-center justify-center font-display font-bold text-ink">
                        RK
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm">Batik Rubung Kuning</p>
                        <p class="text-xs text-muted">Jl. Batik Indah No. 88, Solo • hello@rubungkuning.com</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 text-sm font-medium text-muted">
                    <a href="#metrics" class="hover:text-ink transition-colors">Statistik</a>
                    <a href="#store-info" class="hover:text-ink transition-colors">Informasi Toko</a>
                    <a href="#catalog" class="hover:text-ink transition-colors">Produk</a>
                    <a href="#download-app" class="hover:text-ink transition-colors">Download Apps</a>
                    <a href="#contact" class="hover:text-ink transition-colors">Kontak</a>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-muted">
                <p>&copy; {{ date('Y') }} Batik Rubung Kuning. Seluruh hak cipta dilindungi.</p>
                <p>Landing Page • Tema Putih &amp; Kuning</p>
            </div>
        </div>
    </footer>

    {{-- ============================================================ --}}
    {{-- SCRIPTS                                                       --}}
    {{-- ============================================================ --}}
    <script>
        lucide.createIcons();

        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
        });

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    </script>

</body>

</html>
