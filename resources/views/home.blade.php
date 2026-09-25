@extends('layouts.omniva')

@section('title', 'Brennholz, Holzpellets & Kaminholz')

@push('styles')
<style>
    /* ---- Hero : on garde le positionnement du template, on ajoute juste un voile + texte clair ---- */
    .boire-hero .tf-hero-banner .hero-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(20, 14, 10, .78) 0%, rgba(20, 14, 10, .40) 55%, rgba(20, 14, 10, .05) 100%);
        pointer-events: none;
    }
    .boire-hero .hero_title,
    .boire-hero .hero_desc { color: #fff; text-shadow: 0 2px 14px rgba(0, 0, 0, .5); }
    .boire-hero .hero_desc { max-width: 32rem; }

    /* ---- Category grid ---- */
    .boire-cat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
    @media (max-width: 1200px) { .boire-cat-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 767px)  { .boire-cat-grid { grid-template-columns: repeat(2, 1fr); } }
    .boire-cat-card {
        display: block; border: 1px solid #e9e3db; border-radius: 14px; overflow: hidden;
        background: #fff; transition: box-shadow .2s ease, transform .2s ease;
    }
    .boire-cat-card:hover { box-shadow: 0 12px 30px rgba(0, 0, 0, .10); transform: translateY(-3px); }
    .boire-cat-card .thumb { position: relative; aspect-ratio: 4 / 3; overflow: hidden; }
    .boire-cat-card .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .boire-cat-card .thumb::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 30%, rgba(0, 0, 0, .75) 100%); }
    .boire-cat-card .label { position: absolute; left: 14px; right: 14px; bottom: 12px; color: #fff; z-index: 1; text-shadow: 0 1px 3px rgba(0, 0, 0, .8), 0 1px 12px rgba(0, 0, 0, .5); }
    .boire-cat-card .label .name { font-weight: 700; font-size: 1rem; line-height: 1.25; display: block; }
    .boire-cat-card .label .count { font-size: .8rem; opacity: .95; }

    /* ---- Per-category product sections ---- */
    .boire-sec-head { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
    .boire-sec-head h3 { margin: 0; }
    .boire-prod-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
    @media (max-width: 991px) { .boire-prod-grid { grid-template-columns: repeat(2, 1fr); } }
    .boire-prod-grid .card-product .card-product_wrapper { aspect-ratio: 1 / 1; border-radius: 12px; overflow: hidden; }
    .boire-prod-grid .card-product .card-product_wrapper .img-product,
    .boire-prod-grid .card-product .card-product_wrapper .img-hover { width: 100%; height: 100%; object-fit: cover; }
</style>
@endpush

@section('content')
    {{-- ============ HERO ============ --}}
    <div class="tf-slideshow boire-hero">
        <div dir="ltr" class="swiper tf-swiper slider_effect_fade" data-auto="true" data-loop="true" data-delay="4500"
            data-preview="1" data-tablet="1" data-mobile="1" data-space="0">
            <div class="swiper-wrapper">
                @php
                    $slides = [
                        ['img' => $heroImages['hero-1'] ?? null, 'title' => 'Heizen mit Holz<br>leistungsstark &amp; wirtschaftlich', 'desc' => "Pellets, Scheite und Brennholz – trocken, mit hohem Heizwert, palettenweise geliefert."],
                        ['img' => $heroImages['hero-2'] ?? null, 'title' => 'Holzpellets<br>für Ofen und Kessel', 'desc' => "Holzpellets verschiedener Hersteller, in 15-kg-Säcken auf Palette."],
                        ['img' => $heroImages['hero-3'] ?? null, 'title' => 'Scheite &amp; Kaminholz<br>sofort brennfertig', 'desc' => "Gespaltenes, getrocknetes Hartholz – ideal für Kamineinsätze, Kamine und Holzöfen."],
                    ];
                @endphp
                @foreach ($slides as $slide)
                    <div class="swiper-slide">
                        <div class="tf-hero-banner">
                            <div class="hero-image overflow-hidden">
                                <img class="tf-animate-zoom-in-out" fetchpriority="high"
                                    src="{{ $slide['img'] ?? '/assets/images/slider/skincare/slider-1.jpg' }}" alt="Heri Brennholz">
                            </div>
                            <div class="hero-banner_wrap">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-sm-11 col-md-7 col-lg-6">
                                            <div class="hero-banner">
                                                <p class="hero_title text-hero font-instrument_serif mb-10">{!! $slide['title'] !!}</p>
                                                <p class="hero_desc mb-20">{{ $slide['desc'] }}</p>
                                                <div>
                                                    <a href="{{ route('shop.index') }}" class="btn-action_link tf-btn btn-fill animate-btn">
                                                        <span>Zum Shop</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="box-pag">
                <div class="container">
                    <div class="sw-line-default tf-sw-pagination buttlet-white justify-content-start"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ KATEGORIEN ============ --}}
    <div class="flat-spacing overflow-hidden">
        <div class="container">
            <div class="sect-heading center spacing-bottom-2">
                <h3 class="s-title font-instrument_serif mb-12">Unsere Kategorien</h3>
                <p class="s-desc">Pellets, Scheite, Holzbriketts, Kaminholz und Brennholz.</p>
            </div>
            <div class="boire-cat-grid">
                @foreach ($categories as $cat)
                    <a href="{{ route('shop.category', $cat) }}" class="boire-cat-card">
                        <span class="thumb">
                            <img loading="lazy" src="{{ $catImages[$cat->slug] }}" alt="{{ $cat->name }}">
                            <span class="label">
                                <span class="name">{{ $cat->name }}</span>
                                <span class="count">{{ $cat->products_count }} Produkte</span>
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ SECTION PAR CATÉGORIE (4 produits) ============ --}}
    @foreach ($categories as $cat)
        @continue($cat->products->isEmpty())
        <div class="flat-spacing overflow-hidden pt-0">
            <div class="container">
                <div class="boire-sec-head">
                    <h3 class="s-title font-instrument_serif">{{ $cat->name }}</h3>
                    <a href="{{ route('shop.category', $cat) }}" class="tf-btn-line gap-6">
                        <span class="text-caption fw-medium">Alle {{ $cat->products_count }} Produkte ansehen</span>
                        <i class="icon icon-ArrowUpRight"></i>
                    </a>
                </div>
                <div class="boire-prod-grid">
                    @foreach ($cat->products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    {{-- ============ VORTEILE ============ --}}
    <div class="flat-spacing bg-surface-2 overflow-hidden">
        <div class="container">
            <div class="tf-grid-layout tf-col-2 lg-col-3 gap-30">
                <div class="box-icon_V01 text-center">
                    <span class="icon"><i class="icon-Package"></i></span>
                    <div class="content">
                        <p class="title h5">Palettenlieferung</p>
                        <p class="text-caption cl-text-2">Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.</p>
                    </div>
                </div>
                <div class="box-icon_V01 text-center">
                    <span class="icon"><i class="icon-CheckCircle"></i></span>
                    <div class="content">
                        <p class="title h5">Trocken &amp; zertifiziert</p>
                        <p class="text-caption cl-text-2">Pellets und Hartholz verschiedener Hersteller; Herstellerangaben je Produkt.</p>
                    </div>
                </div>
                <div class="box-icon_V01 text-center">
                    <span class="icon"><i class="icon-Fire2"></i></span>
                    <div class="content">
                        <p class="title h5">Erneuerbare Energie</p>
                        <p class="text-caption cl-text-2">Wirtschaftliches Heizen aus nachhaltig bewirtschafteten Wäldern.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ BANNER ============ --}}
    <div class="flat-spacing overflow-hidden">
        <div class="container">
            <div class="position-relative rounded-16 overflow-hidden" style="min-height:340px;display:flex;align-items:center;">
                <img loading="lazy" src="{{ $heroImages['cta-1'] ?? $heroImages['hero-1'] ?? '/assets/images/section/banner-hero.jpg' }}"
                    alt="Heri Brennholz" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                <span style="position:absolute;inset:0;background:linear-gradient(90deg,rgba(20,14,10,.75),rgba(20,14,10,.25));"></span>
                <div class="position-relative p-4 p-lg-5" style="max-width:560px;color:#fff;text-shadow:0 2px 12px rgba(0,0,0,.45);">
                    <h3 class="font-instrument_serif mb-10" style="color:#fff;">Brennholz und Pellets für die Heizsaison</h3>
                    <p class="mb-20" style="opacity:.95;">Entdecken Sie unser Sortiment an Brennholz, Pellets und Öfen.</p>
                    <a href="{{ route('shop.index') }}" class="tf-btn btn-fill animate-btn"><span>Jetzt bestellen</span></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ NEWSLETTER ============ --}}
    <div class="flat-spacing overflow-hidden pt-0">
        <div class="container">
            <div class="text-center" style="max-width:560px;margin:0 auto;">
                <h3 class="s-title font-instrument_serif mb-12">Bleiben Sie informiert</h3>
                <p class="s-desc mb-20">Unsere Angebote zu Brennholz und Pellets – direkt in Ihr Postfach.</p>
                <form action="{{ route('pages.contact.submit') }}" method="POST" class="form-subscribe-v2">
                    @csrf
                    <input type="hidden" name="name" value="Newsletter-Abonnent">
                    <input type="hidden" name="message" value="Newsletter-Anmeldung über die Startseite">
                    <fieldset class="form-field">
                        <input type="email" name="email" class="text-caption" placeholder="Ihre E-Mail-Adresse" required>
                        <button type="submit" class="btn-action_submit tf-btn animate-btn"><i class="icon icon-ArrowRight"></i></button>
                    </fieldset>
                    <label class="text-caption d-flex gap-2 text-start mt-2"><input type="checkbox" name="newsletter_consent" value="1" required> <span>Ich möchte Angebote per E-Mail erhalten und habe die <a href="/datenschutz" class="text-decoration-underline">Datenschutzerklärung</a> gelesen. Abmeldung jederzeit per E-Mail an kontakt@heribrennholzgmbh.com.</span></label>
                </form>
            </div>
        </div>
    </div>
@endsection
