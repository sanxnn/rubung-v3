@extends('storefront.layout')

@section('content')
        <section id="home" class="hero-section" aria-labelledby="hero-title">
          <div class="hero shell">
            <div class="hero-copy">
                <p class="location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Jember, Indonesia</p>
                <h1 id="hero-title">{{ __('storefront.hero_head') }} <span>{{ __('storefront.hero_tail') }}</span></h1>
                <p>{{ __('storefront.hero_intro') }}</p>
                <div class="actions">
                    <a class="button button-primary" href="#catalog">{{ __('storefront.browse') }}</a>
                    <a class="button button-secondary" href="#contact">{{ __('storefront.ask_order') }}</a>
                </div>
            </div>
            <figure class="hero-visual">
                <img src="{{ asset('images/batik-company-profile.webp') }}"
                    alt="{{ __('storefront.image_alt') }}" width="1200" height="900" fetchpriority="high">
                <figcaption>{{ __('storefront.image_caption') }}</figcaption>
            </figure>
          </div>
        </section>

        <section id="catalog" class="section section-soft" aria-labelledby="catalog-title">
            <div class="shell">
                <div class="section-heading section-heading-row">
                    <div>
                        <h2 id="catalog-title">{{ $featured->first()?->units_sold > 0 ? __('storefront.best_sellers') : __('storefront.featured') }}</h2>
                        <p>{{ __('storefront.featured_intro') }}</p>
                    </div>
                    <a class="button button-secondary" href="{{ route('company.catalog') }}">{{ __('storefront.view_products') }}</a>
                </div>
                @if ($featured->isEmpty())
                    <p class="empty-state">{{ __('storefront.empty_batik') }}</p>
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
                <h2 id="store-title">{{ __('storefront.about_title') }}</h2>
                <p>{{ __('storefront.about_intro') }}</p>
                <p>{{ __('storefront.about_visit') }}</p>
            </div>
            <ul class="service-card">
                <li>
                    <span class="feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                    <div><strong>{{ __('storefront.location') }}</strong><p>{{ __('storefront.city') }}</p></div>
                </li>
                <li>
                    <span class="feature-icon"><i class="fa-solid fa-box" aria-hidden="true"></i></span>
                    <div><strong>{{ __('storefront.custom_orders') }}</strong><p>{{ __('storefront.custom_intro') }}</p></div>
                </li>
                <li>
                    <span class="feature-icon"><i class="fa-solid fa-truck" aria-hidden="true"></i></span>
                    <div><strong>{{ __('storefront.shipping') }}</strong><p>{{ __('storefront.shipping_intro') }}</p></div>
                </li>
            </ul>
        </section>

        <section id="download-app" class="shell" aria-labelledby="app-title">
            <div class="app-panel">
                <div class="section-heading">
                    <h2 id="app-title">{{ __('storefront.app_title') }}</h2>
                    <p>{{ __('storefront.app_intro') }}</p>
                </div>
                <div class="app-download">
                    @include('storefront.app-badges')
                    @unless (config('services.storefront.google_play_url') && config('services.storefront.app_store_url'))
                        <a class="text-link" href="#contact">{{ __('storefront.ask_app') }}</a>
                    @endunless
                </div>
            </div>
        </section>

        <section id="contact" class="section shell split contact-section" aria-labelledby="contact-title">
            <div class="section-heading">
                <h2 id="contact-title">{{ __('storefront.contact') }}</h2>
                <p>{{ __('storefront.contact_intro') }}</p>
                <ul class="contact-info">
                    <li><span class="feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                        <div><strong>{{ __('storefront.city') }}</strong><p>{{ __('storefront.visit') }}</p></div></li>
                    <li><span class="feature-icon"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
                        <div><strong>{{ __('storefront.support') }}</strong><p>{{ __('storefront.reply') }}</p>
                            @if ($publicEmail = config('services.storefront.public_email'))
                                <a class="text-link" href="mailto:{{ $publicEmail }}">{{ $publicEmail }}</a>
                            @endif
                        </div></li>
                </ul>
            </div>
            <form class="contact-form" action="{{ route('contact.send') }}" method="POST">
                @csrf
                <h3>{{ __('storefront.send_heading') }}</h3>
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
                        <label for="contact-name">{{ __('storefront.name') }}</label>
                        <input id="contact-name" type="text" name="name" value="{{ old('name') }}"
                            required maxlength="100" autocomplete="name" placeholder="{{ __('storefront.placeholder_name') }}" @error('name') aria-invalid="true" @enderror>
                    </div>
                    <div class="field">
                        <label for="contact-email">{{ __('storefront.email') }}</label>
                        <input id="contact-email" type="email" name="email" value="{{ old('email') }}"
                            required maxlength="100" autocomplete="email" placeholder="{{ __('storefront.placeholder_email') }}" @error('email') aria-invalid="true" @enderror>
                    </div>
                </div>
                <div class="field">
                    <label for="contact-message">{{ __('storefront.message') }}</label>
                    <textarea id="contact-message" name="message" rows="5" required minlength="10" maxlength="5000" placeholder="{{ __('storefront.placeholder_message') }}"
                        @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                </div>
                <button class="button button-primary" type="submit">{{ __('storefront.send') }}</button>
            </form>
        </section>
@endsection
