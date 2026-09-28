<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batik Rubung Kuning — Company Profile</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ivory: '#FDFBF7',
                        cream: '#F9F5EC',
                        gold: '#D4A843',
                        mustard: '#E5B84B',
                        charcoal: '#2D2D2D',
                        slate: '#5A5A5A',
                        sage: '#8A9A86',
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['"Montserrat"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #FDFBF7;
        }

        ::-webkit-scrollbar-thumb {
            background: #D4A843;
            border-radius: 3px;
        }

        /* Fade-in animation */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Gold line accent */
        .gold-line::after {
            content: '';
            display: block;
            width: 60px;
            height: 2px;
            background: #D4A843;
            margin-top: 16px;
        }

        .gold-line-center::after {
            content: '';
            display: block;
            width: 60px;
            height: 2px;
            background: #D4A843;
            margin: 16px auto 0;
        }

        /* Hero parallax-like overlay */
        .hero-overlay {
            background: linear-gradient(135deg,
                    rgba(253, 251, 247, 0.92) 0%,
                    rgba(253, 251, 247, 0.75) 50%,
                    rgba(253, 251, 247, 0.55) 100%);
        }
    </style>
</head>

<body class="bg-ivory text-charcoal font-sans antialiased">

    {{-- ============================================================ --}}
    {{-- NAVBAR                                                        --}}
    {{-- ============================================================ --}}
    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 transition-all duration-500 bg-transparent">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="#hero" class="flex items-center gap-3 group">
                <div
                    class="w-10 h-10 rounded-full bg-gold flex items-center justify-center
                            group-hover:scale-110 transition-transform duration-300">
                    <span class="text-ivory font-serif font-bold text-lg">R</span>
                </div>
                <div class="leading-tight">
                    <span class="font-serif font-semibold text-charcoal text-lg tracking-wide">Rubung Kuning</span>
                    <span class="block text-[10px] tracking-[0.25em] uppercase text-slate font-medium">Batik
                        Premium</span>
                </div>
            </a>

            {{-- Desktop Menu --}}
            <ul class="hidden md:flex items-center gap-8 text-sm font-medium tracking-wide text-slate">
                <li><a href="#about" class="hover:text-gold transition-colors">Tentang</a></li>
                <li><a href="#vision" class="hover:text-gold transition-colors">Visi & Misi</a></li>
                <li><a href="#values" class="hover:text-gold transition-colors">Filosofi</a></li>
                <li><a href="#collection" class="hover:text-gold transition-colors">Koleksi</a></li>
                <li><a href="#why" class="hover:text-gold transition-colors">Keunggulan</a></li>
                <li><a href="#contact"
                        class="px-5 py-2 border border-gold text-gold rounded-full
                                                 hover:bg-gold hover:text-ivory transition-all duration-300">
                        Hubungi Kami
                    </a></li>
            </ul>

            {{-- Mobile Hamburger --}}
            <button id="menuBtn" class="md:hidden text-charcoal focus:outline-none">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="md:hidden hidden bg-ivory/95 backdrop-blur-md border-t border-gold/20">
            <ul class="flex flex-col items-center gap-4 py-8 text-sm font-medium text-slate">
                <li><a href="#about" class="hover:text-gold transition-colors">Tentang</a></li>
                <li><a href="#vision" class="hover:text-gold transition-colors">Visi & Misi</a></li>
                <li><a href="#values" class="hover:text-gold transition-colors">Filosofi</a></li>
                <li><a href="#collection" class="hover:text-gold transition-colors">Koleksi</a></li>
                <li><a href="#why" class="hover:text-gold transition-colors">Keunggulan</a></li>
                <li><a href="#contact"
                        class="px-5 py-2 border border-gold text-gold rounded-full
                                                 hover:bg-gold hover:text-ivory transition-all">Hubungi
                        Kami</a></li>
            </ul>
        </div>
    </nav>


    {{-- ============================================================ --}}
    {{-- HERO / COVER                                                  --}}
    {{-- ============================================================ --}}
    {{-- ============================================================ --}}
    {{-- HERO SECTION (UPGRADED PREMIUM VERSION)                       --}}
    {{-- ============================================================ --}}
    <section id="hero" class="relative min-h-screen flex items-center overflow-hidden bg-ivory">

        {{-- 1. Subtle Background Pattern & Gradient --}}
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: radial-gradient(#D4A843 1px, transparent 1px); background-size: 24px 24px;">
        </div>
        <div
            class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-gold/5 to-transparent pointer-events-none">
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-12 py-32 w-full">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- LEFT COLUMN: Text & CTA --}}
                <div class="space-y-8 order-2 lg:order-1">

                    {{-- Heading --}}
                    <h1 class="fade-up font-serif text-5xl md:text-6xl lg:text-7xl font-bold leading-[1.1] text-charcoal"
                        style="transition-delay: 200ms;">
                        Sentuhan Emas <br>
                        <span class="text-gold italic">Warisan</span> Nusantara
                    </h1>

                    {{-- Subheading --}}
                    <p class="fade-up text-slate text-lg md:text-xl font-light leading-relaxed max-w-md"
                        style="transition-delay: 300ms;">
                        Batik Rubung Kuning menghadirkan keanggunan kontemporer.
                        Setiap helai kain adalah mahakarya yang menceritakan identitas,
                        kualitas, dan peradaban.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="fade-up flex flex-wrap gap-4" style="transition-delay: 400ms;">
                        <a href="#collection"
                            class="group relative px-8 py-4 bg-charcoal text-ivory font-medium rounded-full overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-charcoal/20">
                            <span class="relative z-10 flex items-center gap-2">
                                Lihat Koleksi
                                <i data-lucide="arrow-right"
                                    class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </span>
                            <div
                                class="absolute inset-0 bg-gold transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300">
                            </div>
                        </a>
                        <a href="#about"
                            class="px-8 py-4 border border-charcoal/20 text-charcoal font-medium rounded-full
                                  hover:border-gold hover:text-gold hover:bg-gold/5 transition-all duration-300 flex items-center gap-2">
                            <i data-lucide="play-circle" class="w-5 h-5"></i>
                            Tentang Kami
                        </a>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Visual Composition --}}
                <div class="relative order-1 lg:order-2 fade-up" style="transition-delay: 300ms;">
                    {{-- Decorative Frame --}}
                    <div
                        class="absolute -top-6 -right-6 w-full h-full border-2 border-gold/30 rounded-3xl hidden lg:block">
                    </div>

                    {{-- Main Image --}}
                    <div
                        class="relative rounded-3xl overflow-hidden shadow-2xl shadow-gold/10 aspect-[4/5] lg:aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1680345575812-2f6878d7d775?q=80&w=387"
                            alt="Batik Rubung Kuning Premium"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">

                        {{-- Gradient Overlay at bottom for text readability if needed --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-charcoal/40 via-transparent to-transparent">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 text-slate/50 z-20">
            <span class="text-[10px] tracking-[0.3em] uppercase font-medium">Scroll</span>
            <div class="w-px h-12 bg-gradient-to-b from-gold via-gold/50 to-transparent animate-pulse"></div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- ABOUT US                                                      --}}
    {{-- ============================================================ --}}
    <section id="about" class="py-24 md:py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                {{-- Image --}}
                <div class="fade-up relative">
                    <div class="absolute -top-4 -left-4 w-full h-full border border-gold/30 rounded-2xl"></div>
                    <img src="https://images.unsplash.com/photo-1761516659497-8478e39d2b26?q=80&w=580"
                        alt="Proses Membatik" class="relative rounded-2xl shadow-xl w-full h-[500px] object-cover">
                    {{-- Floating badge --}}
                    <div class="absolute -bottom-6 -right-6 bg-gold text-ivory px-6 py-4 rounded-xl shadow-lg">
                        <span class="font-serif text-3xl font-bold">10+</span>
                        <span class="block text-xs tracking-wider uppercase">Tahun Pengalaman</span>
                    </div>
                </div>

                {{-- Text --}}
                <div class="fade-up">
                    <p class="text-gold font-medium tracking-[0.3em] uppercase text-xs mb-3">Tentang Kami</p>
                    <h2 class="font-serif text-4xl md:text-5xl font-bold text-charcoal mb-6 gold-line">
                        Warisan yang<br>Hidup Kembali
                    </h2>
                    <p class="text-slate leading-relaxed mb-6">
                        Batik Rubung Kuning adalah rumah mode dan tekstil premium yang berdedikasi untuk
                        melestarikan warisan budaya Nusantara melalui lensa estetika kontemporer. Nama
                        <strong class="text-charcoal">"Rubung Kuning"</strong> terinspirasi dari keindahan alam
                        dan filosofi warna kuning dalam budaya Jawa yang melambangkan kemakmuran, kehangatan,
                        dan keagungan.
                    </p>
                    <p class="text-slate leading-relaxed mb-8">
                        Kami tidak sekadar memproduksi kain batik — kami menciptakan karya seni yang dapat
                        dikenakan. Dengan memadukan teknik tradisional yang otentik dan desain yang relevan
                        dengan gaya hidup modern, Batik Rubung Kuning hadir sebagai pilihan utama bagi
                        individu dan korporasi yang menghargai kualitas, kehalusan, dan nilai budaya.
                    </p>
                    <div class="flex gap-12">
                        <div>
                            <span class="font-serif text-3xl font-bold text-gold">500+</span>
                            <p class="text-sm text-slate mt-1">Motif Eksklusif</p>
                        </div>
                        <div>
                            <span class="font-serif text-3xl font-bold text-gold">50+</span>
                            <p class="text-sm text-slate mt-1">Pengrajin Lokal</p>
                        </div>
                        <div>
                            <span class="font-serif text-3xl font-bold text-gold">1K+</span>
                            <p class="text-sm text-slate mt-1">Klien Puas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- VISI & MISI                                                   --}}
    {{-- ============================================================ --}}
    <section id="vision" class="py-24 md:py-32 bg-cream">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 fade-up">
                <p class="text-gold font-medium tracking-[0.3em] uppercase text-xs mb-3">Arah & Tujuan</p>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-charcoal gold-line-center">
                    Visi & Misi
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-12">
                {{-- Visi --}}
                <div
                    class="fade-up bg-ivory rounded-2xl p-10 shadow-sm border border-gold/10
                            hover:shadow-lg hover:border-gold/30 transition-all duration-500">
                    <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center mb-6">
                        <i data-lucide="eye" class="w-6 h-6 text-gold"></i>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-charcoal mb-4">Visi Kami</h3>
                    <p class="text-slate leading-relaxed">
                        Menjadi brand batik premium terdepan yang diakui secara nasional dan internasional,
                        serta menjadi jembatan yang menghubungkan warisan leluhur dengan gaya hidup modern
                        yang berkelanjutan.
                    </p>
                </div>

                {{-- Misi --}}
                <div
                    class="fade-up bg-ivory rounded-2xl p-10 shadow-sm border border-gold/10
                            hover:shadow-lg hover:border-gold/30 transition-all duration-500">
                    <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center mb-6">
                        <i data-lucide="target" class="w-6 h-6 text-gold"></i>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-charcoal mb-4">Misi Kami</h3>
                    <ul class="space-y-4 text-slate">
                        <li class="flex gap-3">
                            <span class="mt-1.5 w-2 h-2 rounded-full bg-gold flex-shrink-0"></span>
                            <span><strong class="text-charcoal">Kualitas Tanpa Kompromi</strong> — Material terbaik dan
                                pengerjaan presisi.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 w-2 h-2 rounded-full bg-gold flex-shrink-0"></span>
                            <span><strong class="text-charcoal">Desain Kontemporer</strong> — Menghormati pakem
                                tradisional, relevan untuk masa kini.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 w-2 h-2 rounded-full bg-gold flex-shrink-0"></span>
                            <span><strong class="text-charcoal">Pemberdayaan & Keberlanjutan</strong> — Mendukung
                                pengrajin lokal & produksi ramah lingkungan.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 w-2 h-2 rounded-full bg-gold flex-shrink-0"></span>
                            <span><strong class="text-charcoal">Pelayanan Eksklusif</strong> — Pengalaman personal dan
                                profesional bagi setiap klien.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- FILOSOFI & NILAI                                              --}}
    {{-- ============================================================ --}}
    <section id="values" class="py-24 md:py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 fade-up">
                <p class="text-gold font-medium tracking-[0.3em] uppercase text-xs mb-3">The Rubung Kuning Values</p>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-charcoal gold-line-center">
                    Filosofi & Nilai
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Putih --}}
                <div class="fade-up text-center group">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-ivory border-2 border-gold/30
                                flex items-center justify-center mb-6
                                group-hover:bg-gold group-hover:border-gold transition-all duration-500">
                        <i data-lucide="shield-check"
                            class="w-8 h-8 text-gold group-hover:text-ivory transition-colors duration-500"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-charcoal mb-3">Autentisitas</h3>
                    <p class="text-xs tracking-[0.2em] uppercase text-gold mb-3">Putih</p>
                    <p class="text-slate text-sm leading-relaxed">
                        Seperti warna dasar kain kami yang suci, kami menjunjung tinggi kejujuran dalam proses
                        kreasi dan keaslian motif warisan.
                    </p>
                </div>

                {{-- Kuning --}}
                <div class="fade-up text-center group">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-ivory border-2 border-gold/30
                                flex items-center justify-center mb-6
                                group-hover:bg-gold group-hover:border-gold transition-all duration-500">
                        <i data-lucide="sun"
                            class="w-8 h-8 text-gold group-hover:text-ivory transition-colors duration-500"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-charcoal mb-3">Kemakmuran & Kehangatan</h3>
                    <p class="text-xs tracking-[0.2em] uppercase text-gold mb-3">Kuning</p>
                    <p class="text-slate text-sm leading-relaxed">
                        Melambangkan energi positif, kesuksesan, dan kehangatan yang kami harapkan dapat
                        dirasakan oleh siapa pun yang mengenakan karya kami.
                    </p>
                </div>

                {{-- Emas --}}
                <div class="fade-up text-center group">
                    <div
                        class="w-24 h-24 mx-auto rounded-full bg-ivory border-2 border-gold/30
                                flex items-center justify-center mb-6
                                group-hover:bg-gold group-hover:border-gold transition-all duration-500">
                        <i data-lucide="gem"
                            class="w-8 h-8 text-gold group-hover:text-ivory transition-colors duration-500"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-charcoal mb-3">Keanggunan</h3>
                    <p class="text-xs tracking-[0.2em] uppercase text-gold mb-3">Emas</p>
                    <p class="text-slate text-sm leading-relaxed">
                        Refleksi dari profesionalisme dan dedikasi kami terhadap detail, memastikan setiap
                        helai benang memiliki nilai estetika yang tinggi.
                    </p>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- KOLEKSI & PRODUK                                              --}}
    {{-- ============================================================ --}}
    <section id="collection" class="py-24 md:py-32 bg-cream">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 fade-up">
                <p class="text-gold font-medium tracking-[0.3em] uppercase text-xs mb-3">Produk Kami</p>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-charcoal gold-line-center">
                    Koleksi
                </h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1 --}}
                <div class="fade-up group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-5 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1594040226829-7f251ab46d80?w=600&q=80"
                            alt="Batik Tulis"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-charcoal/60 to-transparent
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-500
                                    flex items-end p-6">
                            <span class="text-ivory text-sm font-medium">Lihat Detail →</span>
                        </div>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal">The Heritage Collection</h3>
                    <p class="text-slate text-sm mt-1">Batik Tulis Premium</p>
                </div>

                {{-- Card 2 --}}
                <div class="fade-up group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-5 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80"
                            alt="Batik Corporate"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-charcoal/60 to-transparent
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-500
                                    flex items-end p-6">
                            <span class="text-ivory text-sm font-medium">Lihat Detail →</span>
                        </div>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal">The Corporate Line</h3>
                    <p class="text-slate text-sm mt-1">Batik Cap & Printing Premium</p>
                </div>

                {{-- Card 3 --}}
                <div class="fade-up group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-5 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1558171813-4c088753af8f?w=600&q=80"
                            alt="Ready to Wear"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-charcoal/60 to-transparent
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-500
                                    flex items-end p-6">
                            <span class="text-ivory text-sm font-medium">Lihat Detail →</span>
                        </div>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal">Everyday Elegance</h3>
                    <p class="text-slate text-sm mt-1">Ready-to-Wear</p>
                </div>

                {{-- Card 4 --}}
                <div class="fade-up group cursor-pointer">
                    <div class="relative overflow-hidden rounded-2xl mb-5 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=600&q=80"
                            alt="Home & Accessories"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-charcoal/60 to-transparent
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-500
                                    flex items-end p-6">
                            <span class="text-ivory text-sm font-medium">Lihat Detail →</span>
                        </div>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal">Rubung Living</h3>
                    <p class="text-slate text-sm mt-1">Home & Accessories</p>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- KEUNGGULAN                                                    --}}
    {{-- ============================================================ --}}
    <section id="why" class="py-24 md:py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 fade-up">
                <p class="text-gold font-medium tracking-[0.3em] uppercase text-xs mb-3">Mengapa Kami</p>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-charcoal gold-line-center">
                    Keunggulan Kami
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- 1 --}}
                <div
                    class="fade-up text-center p-8 rounded-2xl bg-ivory border border-gold/10
                            hover:border-gold/40 hover:shadow-lg transition-all duration-500">
                    <div class="w-16 h-16 mx-auto rounded-xl bg-gold/10 flex items-center justify-center mb-5">
                        <i data-lucide="award" class="w-7 h-7 text-gold"></i>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal mb-3">Material Premium</h3>
                    <p class="text-slate text-sm leading-relaxed">
                        Sutra, Katun Primissima, dan Linen berkualitas tinggi yang nyaman di iklim tropis.
                    </p>
                </div>

                {{-- 2 --}}
                <div
                    class="fade-up text-center p-8 rounded-2xl bg-ivory border border-gold/10
                            hover:border-gold/40 hover:shadow-lg transition-all duration-500">
                    <div class="w-16 h-16 mx-auto rounded-xl bg-gold/10 flex items-center justify-center mb-5">
                        <i data-lucide="palette" class="w-7 h-7 text-gold"></i>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal mb-3">Pewarnaan Berkualitas</h3>
                    <p class="text-slate text-sm leading-relaxed">
                        Pewarna color-fast yang ramah lingkungan, termasuk opsi pewarna alam untuk koleksi tertentu.
                    </p>
                </div>

                {{-- 3 --}}
                <div
                    class="fade-up text-center p-8 rounded-2xl bg-ivory border border-gold/10
                            hover:border-gold/40 hover:shadow-lg transition-all duration-500">
                    <div class="w-16 h-16 mx-auto rounded-xl bg-gold/10 flex items-center justify-center mb-5">
                        <i data-lucide="pen-tool" class="w-7 h-7 text-gold"></i>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal mb-3">Custom & Personalisasi</h3>
                    <p class="text-slate text-sm leading-relaxed">
                        Desain motif eksklusif dan bordir personal untuk kebutuhan korporat dan souvenir premium.
                    </p>
                </div>

                {{-- 4 --}}
                <div
                    class="fade-up text-center p-8 rounded-2xl bg-ivory border border-gold/10
                            hover:border-gold/40 hover:shadow-lg transition-all duration-500">
                    <div class="w-16 h-16 mx-auto rounded-xl bg-gold/10 flex items-center justify-center mb-5">
                        <i data-lucide="gift" class="w-7 h-7 text-gold"></i>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-charcoal mb-3">Kemasan Eksklusif</h3>
                    <p class="text-slate text-sm leading-relaxed">
                        Dikemas dalam packaging elegan yang siap menjadi hadiah berkesan (gifting-ready).
                    </p>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- TESTIMONI                                                     --}}
    {{-- ============================================================ --}}
    <section class="py-24 md:py-32 bg-charcoal text-ivory relative overflow-hidden">
        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-gold/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-gold/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-4xl mx-auto px-6 lg:px-12 text-center relative z-10 fade-up">
            <i data-lucide="quote" class="w-12 h-12 text-gold/40 mx-auto mb-8"></i>
            <blockquote class="font-serif text-2xl md:text-3xl italic leading-relaxed mb-8 text-ivory/90">
                "Batik Rubung Kuning berhasil menerjemahkan visi perusahaan kami ke dalam seragam yang tidak
                hanya formal, tetapi juga memiliki nilai seni dan kenyamanan yang luar biasa."
            </blockquote>
            <div class="flex items-center justify-center gap-4">
                <img src="https://i.pravatar.cc/80?img=12" alt="Client"
                    class="w-12 h-12 rounded-full border-2 border-gold/50">
                <div class="text-left">
                    <p class="font-semibold text-ivory">Ahmad Fauzan</p>
                    <p class="text-sm text-ivory/50">CEO, PT Nusantara Jaya</p>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- KONTAK / CTA                                                  --}}
    {{-- ============================================================ --}}
    <section id="contact" class="py-24 md:py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                {{-- Text --}}
                <div class="fade-up">
                    <p class="text-gold font-medium tracking-[0.3em] uppercase text-xs mb-3">Hubungi Kami</p>
                    <h2 class="font-serif text-4xl md:text-5xl font-bold text-charcoal mb-6 gold-line">
                        Mari<br>Berkolaborasi
                    </h2>
                    <p class="text-slate leading-relaxed mb-10">
                        Kami siap menjadi mitra Anda dalam menghadirkan keanggunan warisan Nusantara.
                        Untuk konsultasi desain, pemesanan korporat, atau kunjungan ke galeri kami,
                        silakan hubungi kami.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-gold/10 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="map-pin" class="w-4 h-4 text-gold"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-charcoal text-sm">Alamat</p>
                                <p class="text-slate text-sm">Jl. Batik Indah No. 88, Solo, Jawa Tengah 57100</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-gold/10 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="phone" class="w-4 h-4 text-gold"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-charcoal text-sm">Telepon / WhatsApp</p>
                                <p class="text-slate text-sm">+62 812-3456-7890</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-gold/10 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-4 h-4 text-gold"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-charcoal text-sm">Email</p>
                                <p class="text-slate text-sm">hello@rubungkuning.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="fade-up bg-cream rounded-2xl p-8 md:p-10 border border-gold/10">
                    <h3 class="font-serif text-2xl font-bold text-charcoal mb-6">Kirim Pesan</h3>
                    <form action="#" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate tracking-wide uppercase mb-2">Nama
                                Lengkap</label>
                            <input type="text" placeholder="Masukkan nama Anda"
                                class="w-full px-4 py-3 bg-ivory border border-gold/20 rounded-xl text-sm
                                          focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold/30 transition-all">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate tracking-wide uppercase mb-2">Email</label>
                            <input type="email" placeholder="email@contoh.com"
                                class="w-full px-4 py-3 bg-ivory border border-gold/20 rounded-xl text-sm
                                          focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold/30 transition-all">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate tracking-wide uppercase mb-2">Pesan</label>
                            <textarea rows="4" placeholder="Ceritakan kebutuhan Anda..."
                                class="w-full px-4 py-3 bg-ivory border border-gold/20 rounded-xl text-sm resize-none
                                             focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold/30 transition-all"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-3 bg-gold text-ivory font-medium rounded-xl
                                       hover:bg-mustard transition-colors duration-300 shadow-lg shadow-gold/20">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- FOOTER                                                        --}}
    {{-- ============================================================ --}}
    <footer class="bg-charcoal text-ivory/60 py-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gold flex items-center justify-center">
                            <span class="text-ivory font-serif font-bold text-lg">R</span>
                        </div>
                        <div class="leading-tight">
                            <span class="font-serif font-semibold text-ivory text-lg">Rubung Kuning</span>
                            <span class="block text-[10px] tracking-[0.25em] uppercase text-ivory/40">Batik
                                Premium</span>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed max-w-sm mb-6">
                        Menghidupkan warisan Nusantara melalui keanggunan kontemporer.
                        Setiap helai kain adalah karya seni yang bercerita.
                    </p>
                    <div class="flex gap-3">
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-ivory/20 flex items-center justify-center
                                           hover:border-gold hover:text-gold transition-colors">
                            <i data-lucide="instagram" class="w-4 h-4"></i>
                        </a>
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-ivory/20 flex items-center justify-center
                                           hover:border-gold hover:text-gold transition-colors">
                            <i data-lucide="facebook" class="w-4 h-4"></i>
                        </a>
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-ivory/20 flex items-center justify-center
                                           hover:border-gold hover:text-gold transition-colors">
                            <i data-lucide="linkedin" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-semibold text-ivory text-sm tracking-wide uppercase mb-4">Navigasi</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#about" class="hover:text-gold transition-colors">Tentang Kami</a></li>
                        <li><a href="#vision" class="hover:text-gold transition-colors">Visi & Misi</a></li>
                        <li><a href="#collection" class="hover:text-gold transition-colors">Koleksi</a></li>
                        <li><a href="#contact" class="hover:text-gold transition-colors">Kontak</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h4 class="font-semibold text-ivory text-sm tracking-wide uppercase mb-4">Legal</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-gold transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-gold transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-gold transition-colors">FAQ</a></li>
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div
                class="border-t border-ivory/10 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
                <p>&copy; {{ date('Y') }} Batik Rubung Kuning. All rights reserved.</p>
                <p class="italic font-serif text-ivory/40">Crafted with Heritage, Worn with Pride.</p>
            </div>
        </div>
    </footer>


    {{-- ============================================================ --}}
    {{-- SCRIPTS                                                       --}}
    {{-- ============================================================ --}}
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 80) {
                navbar.classList.add('bg-ivory/95', 'backdrop-blur-md', 'shadow-sm');
                navbar.classList.remove('bg-transparent');
            } else {
                navbar.classList.remove('bg-ivory/95', 'backdrop-blur-md', 'shadow-sm');
                navbar.classList.add('bg-transparent');
            }
        });

        // Mobile menu toggle
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        // Close mobile menu on link click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
        });

        // Fade-up on scroll (Intersection Observer)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.15
        });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>

</body>

</html>
