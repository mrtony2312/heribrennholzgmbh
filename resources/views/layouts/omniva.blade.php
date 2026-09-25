<!DOCTYPE html>

<!--[if IE 8]><html class="ie" xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Heri Brennholz') — Brennholz, Pellets &amp; Scheite aus der Schweiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Heri Brennholz GmbH – Brennholz, Holzpellets und Holzbriketts. Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Heri Brennholz') — Brennholz, Pellets &amp; Scheite aus der Schweiz">
    <meta property="og:description"
        content="Heri Brennholz GmbH – Brennholz, Holzpellets und Holzbriketts. Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.">
    <meta property="og:image" content="{{ asset('assets/images/thumb.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Heri Brennholz GmbH">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Heri Brennholz') — Brennholz, Pellets &amp; Scheite aus der Schweiz">
    <meta name="twitter:description"
        content="Heri Brennholz GmbH – Brennholz, Holzpellets und Holzbriketts. Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.">
    <meta name="twitter:image" content="{{ asset('assets/images/thumb.jpg') }}">

    <!-- font -->
    <link rel="stylesheet" href="/assets/fonts/fonts.css">
    <link rel="stylesheet" href="/assets/icon/icomoon/style.css">
    <!-- css -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/css/animate.css">
    <link rel="stylesheet" href="/assets/css/image-compare-viewer.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/styles.css">

    <!-- Favicon and Touch Icons  -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logo/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/logo/favicon-16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/logo/apple-touch-icon.png">
    @stack('styles')
    @php
        $co = config('feed.company');
    @endphp
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    'name' => $co['legal_name'],
                    'legalName' => $co['legal_name'],
                    'url' => url('/'),
                    'email' => $co['email'],
                    'telephone' => $co['phone'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $co['street'],
                        'postalCode' => $co['postal_code'],
                        'addressLocality' => $co['city'],
                        'addressCountry' => $co['country'],
                    ],
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'contactType' => 'customer service',
                        'telephone' => $co['phone'],
                        'email' => $co['email'],
                        'areaServed' => 'CH',
                        'availableLanguage' => ['de'],
                    ],
                ],
                [
                    '@type' => 'OnlineStore',
                    'name' => $co['legal_name'],
                    'url' => url('/'),
                    'telephone' => $co['phone'],
                    'email' => $co['email'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $co['street'],
                        'postalCode' => $co['postal_code'],
                        'addressLocality' => $co['city'],
                        'addressCountry' => $co['country'],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('jsonld')
    <style>
        /* Barre orange : chaque annonce tient sur une seule ligne */
        .tf-topbar .text-adver {
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
        .tf-topbar .swiper-topbar { overflow: hidden; }
        .tf-topbar .text-adver .br-line { flex: 0 0 auto; }
        /* Fiche produit : galerie miniatures nette */
        .pdp-thumbs img.is-active { border-color: #111 !important; }

        /* ===== Tiroir panier ===== */
        .boire-cart-drawer { width: 420px; max-width: 92vw; display: flex; flex-direction: column; background: #fff; }
        .boire-cart-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #ece5da; }
        #cartDrawerBody { flex: 1; display: flex; flex-direction: column; min-height: 0; }
        .boire-cart-scroll { flex: 1; overflow-y: auto; padding: 8px 24px; }
        .boire-cart-item { display: grid; grid-template-columns: 64px 1fr auto; gap: 14px; padding: 18px 0; border-bottom: 1px solid #f0ebe3; }
        .boire-cart-thumb { display: block; }
        .boire-cart-thumb img { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; background: #f6f2ec; }
        .boire-cart-name { display: block; font-weight: 500; color: #1c140f; line-height: 1.3; margin-bottom: 4px; }
        .boire-cart-name:hover { text-decoration: underline; }
        .boire-cart-line { font-size: 13px; color: #7a7167; }
        .boire-cart-remove { border: 0; background: none; padding: 0; margin-top: 4px; font-size: 12px; color: #a1968a; text-decoration: underline; cursor: pointer; }
        .boire-cart-remove:hover { color: #1c140f; }
        .boire-cart-price { font-weight: 600; white-space: nowrap; }
        .boire-cart-foot { border-top: 1px solid #ece5da; padding: 20px 24px 24px; }
        .boire-cart-subtotal { display: flex; align-items: center; justify-content: space-between; font-weight: 600; font-size: 16px; }
        .boire-cart-note { font-size: 12px; color: #8a8075; margin: 6px 0 14px; }
        .boire-cart-actions { display: grid; gap: 10px; }
        .boire-cart-actions .tf-btn { width: 100%; justify-content: center; }
        .boire-cart-empty { text-align: center; padding: 48px 24px; display: flex; flex-direction: column; align-items: center; gap: 14px; }

        /* ===== Kopfzeile / Logo ===== */
        .tf-header .header-inner { min-height: 92px; }
        .tf-header .header-center .logo-site { display: inline-flex; align-items: center; }
        .tf-header .logo-site img,
        .tf-footer .logo-site img { width: auto; height: 68px; max-width: 180px; object-fit: contain; }
        @media (max-width: 767px) {
            .tf-header .header-inner { min-height: 76px; }
            .tf-header .logo-site img { height: 54px; }
        }

        /* ===== Zahlungsarten (Fußzeile) ===== */
        .payment-methods { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; margin: 0; padding: 0; align-items: center; }
        .payment-method { display: inline-flex; }
        .payment-method img { display: block; width: 48px; height: 30px; }

        /* ===== WhatsApp-Button (über dem Nach-oben-Button) ===== */
        .wa-float { position: fixed; right: 19px; bottom: 92px; z-index: 101; width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center; border-radius: 50%;
            background: #25D366; color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.12), 0 12px 30px rgba(0,0,0,.18);
            transition: transform .18s ease; }
        .wa-float:hover { transform: scale(1.08); color: #fff; }
        .wa-float svg { width: 26px; height: 26px; }
        @media (max-width: 1199px) { .wa-float { bottom: 132px; } }
    </style>
</head>

<body>
    <!-- Scroll Top -->
    <button id="goTop" type="button">
        <span class="d-none">Text</span>
        <span class="border-progress"></span>
        <span class="ic-wrap">
            <span class="icon icon-ArrowCaretUp"></span>
        </span>
    </button>
    <!-- /Scroll Top -->


    <main id="wrapper">
        <!-- Topbar -->
        <div class="tf-topbar  bg-main-12 sm-d-none">
            <div class="container-full">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="tf-btn-swiper-main">
                            <div class="text-white fs-18 nav-prev-swiper">
                                <i class="icon icon-ArrowCaretLeft"></i>
                            </div>
                            <div dir="ltr" class="swiper tf-swiper swiper-topbar" data-touch="false" data-auto="true"
                                data-loop="true" data-speed="1500">
                                <div class="swiper-wrapper">
                                    <!-- slide 1 -->
                                    <div class="swiper-slide">
                                        <div class="text-adver text-white text-caption">
                                            Kostenlose Lieferung in der Schweiz in 1–2 Werktagen
                                            <span class="br-line bg-white"></span>
                                            <a href="/shop" class="fw-semibold link-underline">
                                                Zum Shop
                                            </a>
                                        </div>
                                    </div>
                                    <!-- slide 2 -->
                                    <div class="swiper-slide">
                                        <div class="text-adver text-white text-caption">
                                            Trockenes Holz &amp; zertifizierte Pellets, palettenweise geliefert.
                                            <span class="br-line bg-white"></span>
                                            <a href="/shop" class="fw-semibold link-underline">
                                                Zum Shop
                                            </a>
                                        </div>
                                    </div>
                                    <!-- slide 3 -->
                                </div>
                                <!-- <div class="sw-dot-default tf-sw-pagination"></div> -->
                            </div>
                            <div class="text-white fs-18 nav-next-swiper">
                                <i class="icon icon-ArrowCaretRight"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="tf-list justify-content-end">
                            <a href="/ueber-uns" class="text-caption text-white link-underline">Über uns</a>
                            <a href="/shop" class="text-caption text-white link-underline">Unsere Produkte</a>
                            <a href="/kontakt" class="text-caption text-white link-underline">Kontakt</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Topbar -->
        <!-- Header -->
        <header class="tf-header">
            <div class="header-inner">
                <div class="container-full">
                    <div class="header-inner_wrap">
                        <div class="header-left">
                            <div class="box-btn-open-menu d-flex d-xl-none">
                                <a href="#mobileMenu" data-bs-toggle="offcanvas" class="d-xl-none">
                                    <i class="icon icon-OpenMenu fs-24"></i>
                                </a>
                            </div>
                            <nav class="box-navigation d-none d-xl-block">
                                <ul class="box-nav-menu">
                                    <li class="menu-item">
                                        <a href="{{ route('home') }}" class="item-link {{ request()->routeIs('home') ? 'activeMenu' : '' }}">
                                            <span class="text">Startseite</span>
                                        </a>
                                    </li>
                                    <li class="menu-item position-relative">
                                        <a href="{{ route('shop.index') }}" class="item-link {{ request()->routeIs('shop.*') || request()->routeIs('product.*') ? 'activeMenu' : '' }}">
                                            <span class="text">Shop</span>
                                            <i class="icon icon-ArrowCaretDown"></i>
                                        </a>
                                        <div class="sub-menu">
                                            <ul class="sub-menu_list">
                                                <li class="sub-menu_item">
                                                    <a href="{{ route('shop.index') }}" class="sub-menu_link text-caption">
                                                        <span class="text">Alle Produkte</span>
                                                    </a>
                                                </li>
                                                @foreach ($navCategories as $navCat)
                                                    <li class="sub-menu_item">
                                                        <a href="{{ route('shop.category', $navCat) }}" class="sub-menu_link text-caption">
                                                            <span class="text">{{ $navCat->name }} ({{ $navCat->products_count ?? $navCat->products()->count() }})</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('pages.contact') }}" class="item-link {{ request()->routeIs('pages.contact') ? 'activeMenu' : '' }}">
                                            <span class="text">Kontakt</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('pages.privacy') }}" class="item-link {{ request()->routeIs('pages.privacy') ? 'activeMenu' : '' }}">
                                            <span class="text">Datenschutz</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div class="header-center">
                            <h1>
                                <a href="/" class="logo-site">
                                    <img width="76" height="68" src="/assets/images/logo/logo.png" alt="Heri Brennholz GmbH">
                                </a>
                            </h1>
                        </div>
                        <div class="header-right">
                            <ul class="nav-icon-list tf-list justify-content-end">
                                <li class="d-none d-sm-block">
                                    <a href="#search" data-bs-toggle="offcanvas" class="nav-icon-item link"
                                        aria-label="Open search">
                                        <span class="icon icon-Search" aria-hidden="true"></span>
                                    </a>
                                </li>

                                <li class="d-none d-sm-block">
                                    <a href="{{ route('wishlist.index') }}" class="nav-icon-item link has-number" aria-label="Favoris">
                                        <i class="icon icon-Hearth2"></i>
                                        <span class="number-order js-wishlist-count">{{ $wishlist->count() }}</span>
                                    </a>
                                </li>
                                <li class="d-none d-sm-block">
                                    <a href="{{ route('cart.index') }}" class="nav-icon-item link has-number" aria-label="Warenkorb">
                                        <i class="icon icon-ShoppingBag"></i>
                                        <span class="number-order js-cart-count">{{ $cart->count() }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                        class="nav-icon-item link has-number" aria-label="Warenkorb öffnen">
                                        <i class="icon icon-Bag"></i>
                                        <span class="number-order js-cart-count">{{ $cart->count() }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- flash message --}}
        @if (session('status'))
            <div class="container"><div style="background:#eef7ee;border:1px solid #cbe3cb;padding:12px 16px;border-radius:8px;margin:16px 0;text-align:center;">{{ session('status') }}</div></div>
        @endif

        @yield('content')

        <footer class="tf-footer">
            <span class="br-line fake-class top-0"></span>
            <div class="footer-inner">
                <div class="container">
                    <div class="footer-inner_wrap">
                        <div class="ft-infor">
                            <a href="/" class="logo-site">
                                <img loading="lazy" width="76" height="68" src="/assets/images/logo/logo.png"
                                    alt="Heri Brennholz GmbH">
                            </a>
                            <ul class="list-infor-contact tf-list vertical gap-15">
                                <li class="infor-contact_item">
                                    <span class="ic-w">
                                        <i class="icon icon-DotLocation"></i>
                                    </span>
                                    <a href="https://www.google.com/maps?q=Fiderholzstrasse+7,+4562+Biberist,+Schweiz"
                                        class="text-caption fw-medium link-underline link-black">
                                        Fiderholzstrasse 7, 4562 Biberist, Schweiz
                                    </a>
                                </li>
                                <li class="infor-contact_item">
                                    <span class="ic-w">
                                        <i class="icon icon-Phone"></i>
                                    </span>
                                    <a href="tel:+41786099516"
                                        class="text-caption fw-medium link-underline link-black">
                                        +41 78 609 95 16
                                    </a>
                                </li>
                                <li class="infor-contact_item">
                                    <span class="ic-w">
                                        <i class="icon icon-LetterEnvelope"></i>
                                    </span>
                                    <a href="mailto:kontakt@heribrennholzgmbh.com"
                                        class="text-caption fw-medium link-underline link-black">
                                        kontakt@heribrennholzgmbh.com
                                    </a>
                                </li>
                            </ul>
                            <a href="/kontakt" class="tf-btn-line gap-6">
                                <span class="text-caption fw-medium">
                                    Kontakt aufnehmen
                                </span>
                                <i class="icon icon-ArrowUpRight"></i>
                            </a>
                        </div>
                        <div class="footer-col-block foot-col-link-1 ms-auto">
                            <p class="footer-heading footer-heading-mobile">Über uns</p>
                            <div class="tf-collapse-content">
                                <ul class="footer-menu-list">
                                    <li>
                                        <a href="/ueber-uns" class="text-caption fw-medium link-black link-underline">
                                            Über uns
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/kontakt" class="text-caption fw-medium link-black link-underline">
                                            Kontakt
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/ueber-uns" class="text-caption fw-medium link-black link-underline">
                                            Unsere Geschichte
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/shop"
                                            class="text-caption fw-medium link-black link-underline">
                                            Zum Shop
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="footer-col-block foot-col-link-2">
                            <p class="footer-heading footer-heading-mobile">Informationen</p>
                            <div class="tf-collapse-content">
                                <ul class="footer-menu-list">
                                    <li>
                                        <a href="{{ route('pages.impressum') }}" class="text-caption fw-medium link-black link-underline">
                                            Impressum
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/datenschutz" class="text-caption fw-medium link-black link-underline">
                                            Datenschutz
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/agb"
                                            class="text-caption fw-medium link-black link-underline">
                                            AGB
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pages.rueckgabe') }}" class="text-caption fw-medium link-black link-underline">
                                            Rückgabe & Erstattung
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/faq" class="text-caption fw-medium link-black link-underline">
                                            FAQ
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('pages.versand') }}" class="text-caption fw-medium link-black link-underline">
                                            Versand
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="footer-col-block foot-col-contact">
                            <p class="footer-heading footer-heading-mobile">Newsletter abonnieren</p>
                            <div class="tf-collapse-content">
                                <div class="ft-contact">
                                    <p class="desc text-caption fw-medium">
                                        Abonnieren Sie unseren Newsletter und erhalten Sie unsere Angebote zu Brennholz und Pellets. Kein Spam.
                                    </p>
                                    <form action="{{ route('pages.contact.submit') }}" method="POST" class="form-subscribe-v2">
                                        @csrf
                                        <input type="hidden" name="name" value="Newsletter-Abonnent">
                                        <input type="hidden" name="message" value="Newsletter-Anmeldung (Footer)">
                                        <fieldset class="form-field">
                                            <label for="iptSubEmail" class="d-none">
                                                E-Mail
                                            </label>
                                            <input id="iptSubEmail" type="email" class="text-caption"
                                                name="email" placeholder="Ihre E-Mail-Adresse" aria-label="E-Mail-Adresse" required>
                                            <button type="submit" class="btn-action_submit tf-btn animate-btn"
                                                aria-label="Abonnieren">
                                                <i class="icon icon-ArrowRight" aria-hidden="true"></i>
                                            </button>
                                        </fieldset>
                                        <label class="text-caption d-flex gap-2 text-start mt-2"><input type="checkbox" name="newsletter_consent" value="1" required> <span>Ich möchte Angebote per E-Mail erhalten und habe die <a href="/datenschutz" class="text-decoration-underline">Datenschutzerklärung</a> gelesen. Abmeldung jederzeit per E-Mail an kontakt@heribrennholzgmbh.com.</span></label>
                                    </form>
                                    <p>
                                        Weitere Informationen finden Sie in unserer
                                        <a href="/datenschutz" class="cl-text-main text-decoration-underline d-inline-block">Datenschutzerklärung</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="footer-bottom_wrap">
                        <span class="br-line fake-class top-0"></span>
                        <div class="text-nocopy text-caption cl-text-main">
                            © {{ date('Y') }} Heri Brennholz GmbH. Alle Rechte vorbehalten.
                        </div>
                        <div class="text-caption cl-text-main">
                            Preise in CHF inkl. {{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} % MWST · Zahlungsarten: {{ payment_methods_text() }}
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    @if (config('contact.whatsapp'))
        <a class="wa-float" href="https://wa.me/{{ config('contact.whatsapp') }}?text={{ rawurlencode(config('contact.whatsapp_text')) }}"
           target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
            <svg viewBox="0 0 32 32" width="30" height="30" aria-hidden="true" fill="currentColor">
                <path d="M16.003 3.2c-7.06 0-12.8 5.74-12.8 12.8 0 2.257.59 4.46 1.71 6.402L3.2 28.8l6.56-1.72a12.74 12.74 0 0 0 6.243 1.59h.005c7.06 0 12.8-5.74 12.8-12.8 0-3.42-1.332-6.636-3.752-9.055A12.71 12.71 0 0 0 16.003 3.2zm0 23.36h-.004a10.55 10.55 0 0 1-5.38-1.473l-.386-.23-3.892 1.02 1.04-3.796-.25-.39a10.53 10.53 0 0 1-1.615-5.62c0-5.867 4.774-10.64 10.65-10.64a10.57 10.57 0 0 1 7.524 3.12 10.55 10.55 0 0 1 3.116 7.526c0 5.867-4.774 10.64-10.65 10.64zm5.84-7.97c-.32-.16-1.894-.934-2.188-1.04-.293-.107-.507-.16-.72.16-.214.32-.826 1.04-1.013 1.253-.187.214-.373.24-.693.08-.32-.16-1.352-.498-2.576-1.59-.952-.848-1.594-1.896-1.78-2.216-.187-.32-.02-.493.14-.653.144-.143.32-.373.48-.56.16-.187.213-.32.32-.533.107-.214.053-.4-.027-.56-.08-.16-.72-1.734-.986-2.374-.26-.624-.524-.54-.72-.55l-.613-.01c-.213 0-.56.08-.853.4-.293.32-1.12 1.093-1.12 2.667 0 1.574 1.147 3.094 1.307 3.307.16.214 2.253 3.44 5.46 4.826.763.33 1.36.526 1.824.674.766.244 1.464.21 2.016.127.615-.092 1.894-.774 2.16-1.52.267-.747.267-1.387.187-1.52-.08-.133-.293-.213-.613-.373z"/>
            </svg>
        </a>
    @endif

    <!-- Mobile Menu -->
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <div class="canvas-header">
            <button type="button" class="link-rotate" data-bs-dismiss="offcanvas">
                <i class="icon icon-Close2"></i>
            </button>
        </div>
        <div class="canvas-body">
            <div class="mb-content-top">
                <div id="wrapper-menu-navigation"></div>
                <div class="group-btn gap-7">
                    <a href="{{ route('cart.index') }}" class="tf-btn rounded-4 style-stroke-3 small gap-10 text-black">
                        <i class="icon icon-ShoppingBag fs-14"></i>
                        <span class="text-caption letter-space--3">Warenkorb ({{ $cart->count() }})</span>
                    </a>
                    <a href="#search" data-bs-toggle="offcanvas"
                        class="tf-btn rounded-4 style-stroke-3 small gap-10 text-black">
                        <i class="icon icon-Search2 fs-14"></i>
                        <span class="text-caption letter-space--3">Suchen</span>
                    </a>
                </div>
                <div class="need-help-wrap">
                    <p class="text-caption cl-text-main fw-medium text-decoration-underline mb-15">Brauchen Sie Hilfe?</p>
                    <div class="tf-list vertical gap-6">
                        <a href="{{ route('pages.contact') }}" class="d-block text-caption">
                            Adresse:
                            <span class="fw-medium">Fiderholzstrasse 7, 4562 Biberist, Schweiz</span>
                        </a>
                        <a href="mailto:kontakt@heribrennholzgmbh.com" class="d-block text-caption">
                            E-Mail: <span class="fw-medium">kontakt@heribrennholzgmbh.com</span>
                        </a>
                        <a href="tel:+41786099516" class="d-block text-caption">
                            Telefon: <span class="fw-medium">+41 78 609 95 16</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Mobile Menu -->

    <!-- Search -->
    <div class="offcanvas offcanvas-top popup-search full" id="search">
        <div class="btn-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon icon-Close2"></i>
        </div>
        <div class="container">
            <div class="canvas-heading">
                <h4 class="title">
                    Wonach suchen Sie?
                </h4>
                <form class="form-search" action="{{ route('shop.search') }}" method="GET">
                    <input type="text" name="q" placeholder="Pellets, Scheite, Brennholz …" value="{{ request('q') }}">
                    <button type="submit" class="btn-action_submit">
                        <i class="icon icon-Search"></i>
                    </button>
                </form>
                <div class="popuplar-search">
                    <span class="text-caption fw-medium">
                        Beliebte Suchanfragen:
                    </span>
                    <div class="list-popular">
                        @foreach ($navCategories as $navCat)
                            <a href="{{ route('shop.category', $navCat) }}" class="popular-item text-caption fw-medium">{{ $navCat->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="offcanvas-content">
                <h4 class="title">Unsere Kategorien</h4>
                <div class="tf-grid-layout tf-col-2 md-col-3 xl-col-4 gap-15">
                    @foreach ($navCategories as $navCat)
                        <a href="{{ route('shop.category', $navCat) }}" class="card-product text-center p-3" style="border:1px solid #ece7e1;border-radius:10px;display:block;">
                            <span class="name-product fw-medium d-block">{{ $navCat->name }}</span>
                            <span class="text-caption opacity-75">{{ $navCat->products()->count() }} Produkte</span>
                        </a>
                    @endforeach
                </div>
            </div>
            </div>
        </div>
    </div>
    <!-- /Search -->
    <!-- Toolbar -->
    <div class="tf-toolbar-bottom">
        <div class="toolbar-item">
            <a href="#search" data-bs-toggle="offcanvas">
                <span class="toolbar-icon">
                    <i class="icon icon-Search2"></i>
                </span>
                <span class="toolbar-label">Suchen</span>
            </a>
        </div>
        <div class="toolbar-item">
            {{-- TODO: kein Konto-/Login-Feature im Backend vorhanden; verlinkt bis dahin auf den Shop --}}
            <a href="/shop">
                <span class="toolbar-icon">
                    <i class="icon icon-User"></i>
                </span>
                <span class="toolbar-label">Konto</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="/shop">
                <span class="toolbar-icon">
                    <i class="icon icon-StoreFront"></i>
                </span>
                <span class="toolbar-label">Shop</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="{{ route('wishlist.index') }}">
                <span class="toolbar-icon">
                    <i class="icon icon-HearchStroke"></i>
                </span>
                <span class="toolbar-label">Favoriten</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="/warenkorb">
                <span class="toolbar-icon">
                    <i class="icon icon-ShoppingBag"></i>
                    <span class="toolbar-count js-cart-count">{{ $cart->count() }}</span>
                </span>
                <span class="toolbar-label">Cart</span>
            </a>
        </div>
    </div>
    <!-- /Toolbar -->
    <!-- Shopping Cart -->
    <div class="offcanvas offcanvas-end boire-cart-drawer" id="shoppingCart" tabindex="-1">
        <div class="boire-cart-head">
            <h5 class="m-0">Ihr Warenkorb</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Schließen"></button>
        </div>
        <div id="cartDrawerBody">
            @include('partials.cart-drawer-body')
        </div>
    </div>
    <!-- /Shopping Cart -->
    <!-- Quick View -->
    <div class="modal fade" id="modalQuickView" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"></div>
        </div>
    </div>
    <!-- /Quick View -->

    <!-- Javascript -->
    <script src="/assets/js/plugin/bootstrap.min.js"></script>
    <script src="/assets/js/plugin/jquery.min.js"></script>
    <script src="/assets/js/plugin/swiper-bundle.min.js"></script>
    <script src="/assets/js/plugin/bootstrap-select.min.js"></script>
    <script src="/assets/js/plugin/count-down.js"></script>
    <script src="/assets/js/plugin/infinityslide.js"></script>
    <script src="/assets/js/plugin/wow.min.js"></script>
    <script src="/assets/js/plugin/parallaxie.js"></script>
    <script src="/assets/js/plugin/countto.js"></script>
    <script src="/assets/js/plugin/image-compare-viewer.min.js"></script>
    <script src="/assets/js/plugin/image-compare-viewer.js"></script>

    <script src="/assets/js/carousel.js"></script>
    <script src="/assets/js/main.js"></script>
    <script>
    // Safety net: never let a leftover preloader / modal backdrop freeze the page.
    (function () {
        function unstick() {
            document.querySelectorAll('#preload, .modal-backdrop, .offcanvas-backdrop').forEach(function (el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
        window.addEventListener('load', function () { setTimeout(unstick, 400); });
        // If an offcanvas/modal is open, Escape or a click outside always clears it.
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') unstick(); });
    })();
    </script>
    <script>
    document.addEventListener('submit', function (e) {
        var form = e.target.closest('form.js-add-to-cart');
        if (!form) return;
        e.preventDefault();
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(new FormData(form))})
        .then(function(r){return r.json();})
        .then(function(data){
            document.querySelectorAll('.js-cart-count').forEach(function(el){el.textContent=data.count;});
            // met à jour le contenu du tiroir SANS l'ouvrir
            fetch('{{ route('cart.drawer') }}').then(function(r){return r.text();}).then(function(h){
                var b=document.getElementById('cartDrawerBody'); if(b) b.innerHTML=h;
            });
            boireToast(data.message || 'Produkt in den Warenkorb gelegt.');
        }).catch(function(){form.submit();});
    });

    // ---- Retrait depuis le tiroir (sans quitter la page) ----
    document.addEventListener('submit', function (e) {
        var form = e.target.closest('form.js-cart-remove');
        if (!form) return;
        e.preventDefault();
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(new FormData(form))})
        .then(function(){ return fetch('{{ route('cart.drawer') }}'); })
        .then(function(r){ return r.text(); })
        .then(function(h){
            var b=document.getElementById('cartDrawerBody');
            if(b){
                b.innerHTML=h;
                var c=b.querySelector('#drawerCount');
                if(c) document.querySelectorAll('.js-cart-count').forEach(function(el){el.textContent=c.textContent;});
            }
        })
        .catch(function(){ form.submit(); });
    });

    // ---- Toast léger ----
    function boireToast(msg){
        var t=document.getElementById('boireToast');
        if(!t){t=document.createElement('div');t.id='boireToast';
            t.style.cssText='position:fixed;z-index:99999;left:50%;bottom:28px;transform:translateX(-50%);background:#1c140f;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;box-shadow:0 8px 24px rgba(0,0,0,.25);opacity:0;transition:opacity .25s;';
            document.body.appendChild(t);}
        t.textContent=msg;t.style.opacity='1';
        clearTimeout(t._h);t._h=setTimeout(function(){t.style.opacity='0';},2600);
    }

    // ---- Favoris ----
    document.addEventListener('click', function(e){
        var a=e.target.closest('.js-wishlist');
        if(!a) return;
        e.preventDefault(); e.stopPropagation();
        fetch('{{ route('wishlist.toggle') }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+a.dataset.id})
        .then(function(r){return r.json();})
        .then(function(data){
            document.querySelectorAll('.js-wishlist-count').forEach(function(el){el.textContent=data.count;});
            var li=a.closest('.wishlist'), ic=a.querySelector('.icon'), tip=a.querySelector('.tooltip');
            if(li) li.classList.toggle('active', data.added);
            if(ic){ic.classList.toggle('icon-Hearth2',!data.added); ic.classList.toggle('icon-Trash',data.added);}
            if(tip) tip.textContent = data.added ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen';
            boireToast(data.message);
        }).catch(function(){});
    });

    // ---- Aperçu rapide : lightbox autonome (aucune dépendance au CSS du template) ----
    (function(){
        var ov=null;
        function close(){ if(ov){ ov.remove(); ov=null; document.body.style.overflow=''; } }
        function open(url){
            close();
            ov=document.createElement('div');
            ov.setAttribute('id','boireQuickview');
            ov.style.cssText='position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(15,10,7,.6);';
            ov.innerHTML='<div class="bqv-panel" style="background:#fff;border-radius:14px;max-width:900px;width:100%;max-height:90vh;overflow:auto;position:relative;box-shadow:0 30px 80px rgba(0,0,0,.35);">'
                + '<button type="button" class="bqv-close" aria-label="Schließen" style="position:absolute;top:10px;right:12px;z-index:2;border:0;background:#f1ece6;width:34px;height:34px;border-radius:50%;font-size:20px;line-height:1;cursor:pointer;">&times;</button>'
                + '<div class="bqv-body" style="padding:8px;"><div style="padding:48px;text-align:center;">Wird geladen …</div></div></div>';
            document.body.appendChild(ov);
            document.body.style.overflow='hidden';
            ov.addEventListener('click', function(e){ if(e.target===ov || e.target.closest('.bqv-close')) close(); });
            fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){ return r.text(); })
                .then(function(h){ var b=ov.querySelector('.bqv-body'); if(b) b.innerHTML=h; })
                .catch(function(){ window.location = url.replace('/apercu',''); });
        }
        document.addEventListener('click', function(e){
            var a=e.target.closest('.js-quickview');
            if(!a) return;
            e.preventDefault();
            open(a.dataset.url);
        });
        document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });
    })();
    </script>
    @stack('scripts')
</body>

</html>