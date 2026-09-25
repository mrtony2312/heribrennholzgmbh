@extends('layouts.omniva')

@section('title', $product->name)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->short_description ?: $product->description), 150))

@php
    $primaryCat = $product->categories->first();
    $gallery = $product->images->isNotEmpty()
        ? $product->images
        : collect([(object) ['url' => asset('assets/images/item/item-bg.jpg'), 'alt' => $product->name]]);
    $hasPromo = $product->on_sale && $product->regular_price && $product->regular_price > $product->price;
@endphp

@section('content')
    {{-- ===== Page Title ===== --}}
    <div class="tf-page-title single">
        <div class="container">
            <div class="content">
                <ul class="breadcrumb text-extra-small fw-medium">
                    <li><a href="{{ route('home') }}" class="link-black link-underline">Startseite</a></li>
                    <li class="br-dot"></li>
                    <li><a href="{{ route('shop.index') }}" class="link-black link-underline">Shop</a></li>
                    @if ($primaryCat)
                        <li class="br-dot"></li>
                        <li><a href="{{ route('shop.category', $primaryCat) }}" class="link-black link-underline">{{ $primaryCat->name }}</a></li>
                    @endif
                    <li class="br-dot"></li>
                    <li class="cl-text-main">{{ $product->name }}</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ===== Product Single ===== --}}
    <section class="section-product-single tf-main-product">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="tf-product-media-wrap sticky-top">
                        <div class="boire-gallery" id="boireGallery">
                            @if ($gallery->count() > 1)
                                <div class="boire-gallery-thumbs">
                                    @foreach ($gallery as $i => $img)
                                        <button type="button" class="boire-thumb {{ $i === 0 ? 'is-active' : '' }}" data-src="{{ $img->url }}" data-index="{{ $i }}">
                                            <img loading="lazy" src="{{ $img->url }}" alt="{{ $product->name }}">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            <figure class="boire-gallery-main" id="boireGalleryMain" role="button" tabindex="0" aria-label="Bild vergrößern">
                                <img id="boireGalleryImg" src="{{ $gallery->first()->url }}" alt="{{ $product->name }}">
                                <span class="boire-gallery-zoomhint"><i class="icon icon-Search"></i></span>
                            </figure>
                        </div>
                    </div>
                    <script type="application/json" id="boireGalleryData">@json($gallery->map(fn ($g) => ['src' => $g->url, 'alt' => $g->alt ?? $product->name])->values())</script>
                </div>

                <div class="col-md-6">
                    <div class="tf-product-info-wrap position-relative mt-md-0" id="tfProductInfoWrap">
                        <div class="tf-zoom-main sticky-top"></div>
                        <div class="tf-product-info-list other-image-zoom">
                            <div class="tf-product-info-heading">
                                @if ($primaryCat)
                                    <p class="product-infor-badge text-caption cl-text-main fw-medium">{{ $primaryCat->name }}</p>
                                @endif
                                <h1 class="product-infor-name h3">{{ $product->name }}</h1>
                                <div class="product-infor-price">
                                    <span class="price-on-sale h4 text-primary" data-gmc-price="{{ number_format((float) $product->price, 2, '.', '') }}">{{ swiss_money((float) $product->price) }}</span>
                                    @if ($hasPromo)
                                        <span class="price-on-old cl-text-main fw-medium text-decoration-line-through">{{ swiss_money((float) $product->regular_price) }}</span>
                                        <span class="badge-sale text-extra-small fw-medium style-fill">
                                            -{{ (int) round(100 - ($product->price / max((float) $product->regular_price, 0.01) * 100)) }} %
                                        </span>
                                    @endif
                                </div>
                                @if ($product->formatted_grundpreis)
                                    <div class="product-infor-grundpreis text-caption cl-text-main">{{ $product->formatted_grundpreis }}</div>
                                @endif
                                <div class="text-caption cl-text-main">Preis in CHF inkl. {{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} % MWST</div>
                                <div class="tf-product-shipping cl-text-main">
                                    Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.
                                    <a href="{{ route('pages.versand') }}" class="text-decoration-underline link">Versandbedingungen</a>
                                </div>
                                <p class="mb-0">
                                    <span class="badge {{ $product->in_stock ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->in_stock ? 'Auf Lager' : 'Ausverkauft' }}
                                    </span>
                                    @if ($product->stock_availability)
                                        <span class="text-caption ms-1">{{ $product->stock_availability }}</span>
                                    @endif
                                </p>
                                @if ($product->short_description)
                                    <div class="tf-product-description cl-text-7 letter-space--2">
                                        {!! strip_tags($product->short_description, '<p><br><ul><ol><li><strong><em>') !!}
                                    </div>
                                @endif
                            </div>

                            <div class="tf-product-variant">
                                <div class="tf-product-total-quantity">
                                    <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart group-action">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="wg-quantity">
                                            <button type="button" class="btn-quantity btn-decrease"><i class="icon icon-Minus fs-10"></i></button>
                                            <input class="quantity-product" type="text" name="quantity" value="1">
                                            <button type="button" class="btn-quantity btn-increase"><i class="icon icon-Plus fs-10"></i></button>
                                        </div>
                                        <button type="submit" class="btn-action-price tf-btn style-2 style-stroke-2 size3 w-100" {{ $product->in_stock ? '' : 'disabled' }}>
                                            {{ $product->in_stock ? 'In den Warenkorb' : 'Nicht verfügbar' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('checkout.index') }}" class="tf-btn style-2 size3 animate-btn">Zur Kasse</a>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3 my-4">
                                <div class="d-flex align-items-center gap-3" style="flex:1 1 220px;">
                                    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;border-radius:50%;border:2px solid #3cb54a;">
                                        <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
                                            <path d="M8.5 12.8L6 21l6-3 6 3-2.5-8.2" fill="#1c1c1c"/>
                                            <circle cx="12" cy="7.5" r="6" fill="#1c1c1c"/>
                                            <path d="M9.3 7.6l1.7 1.7 3.4-3.6" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span style="min-width:0;"><strong class="d-block" style="color:#3cb54a;font-style:italic;font-size:17px;">Qualität</strong><span style="color:#1c1c1c;font-size:14px;line-height:1.3;">Pellets von hoher Qualität DIN Plus / EN Plus</span></span>
                                </div>
                                <div class="d-flex align-items-center gap-3" style="flex:1 1 220px;">
                                    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;border-radius:50%;border:2px solid #3cb54a;">
                                        <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" fill="#1c1c1c"/>
                                            <text x="12" y="16.5" font-size="13" font-family="Arial, sans-serif" font-weight="700" fill="#fff" text-anchor="middle">&#8364;</text>
                                        </svg>
                                    </span>
                                    <span style="min-width:0;"><strong class="d-block" style="color:#3cb54a;font-style:italic;font-size:17px;">Ersparnis</strong><span style="color:#1c1c1c;font-size:14px;line-height:1.3;">Die besten Angebote für jede Bestellung</span></span>
                                </div>
                                <div class="d-flex align-items-center gap-3" style="flex:1 1 220px;">
                                    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;border-radius:50%;border:2px solid #3cb54a;">
                                        <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
                                            <path d="M1 6.5h12v9H1z" fill="#1c1c1c"/>
                                            <path d="M13 10.5h3.6l3.4 3v2h-7z" fill="#1c1c1c"/>
                                            <circle cx="6" cy="18" r="1.8" fill="#fff" stroke="#1c1c1c" stroke-width="1.6"/>
                                            <circle cx="17.5" cy="18" r="1.8" fill="#fff" stroke="#1c1c1c" stroke-width="1.6"/>
                                        </svg>
                                    </span>
                                    <span style="min-width:0;"><strong class="d-block" style="color:#3cb54a;font-style:italic;font-size:17px;">Lieferung Schweiz</strong><span style="color:#1c1c1c;font-size:14px;line-height:1.3;">Schnelle &amp; sichere Lieferung in der ganzen Schweiz</span></span>
                                </div>
                            </div>

                            <div class="tf-product-delivery">
                                <div class="product-delivery">
                                    <i class="icon icon-Truck cl-text-main"></i>
                                    <p class="text-caption">Voraussichtliche Lieferzeit:
                                        <span class="cl-text-main fw-medium">1 bis 2 Werktage</span>
                                    </p>
                                </div>
                                <span class="br-line type-vertical"></span>
                                <div class="product-delivery">
                                    <i class="icon icon-Box2 cl-text-main"></i>
                                    <p class="text-caption">
                                        <span class="cl-text-main">Kostenlose Lieferung</span> in der ganzen Schweiz
                                    </p>
                                </div>
                            </div>

                            <div class="tf-product-accordion" id="prdDes">
                                <div class="accordion-item">
                                    <div class="accordion-action h6 collapsed" data-bs-target="#faq-desc" data-bs-toggle="collapse" aria-expanded="true" role="button">
                                        <span>Beschreibung</span>
                                        <span class="icon ic-accordion-custom"></span>
                                    </div>
                                    <div id="faq-desc" class="collapse show" data-bs-parent="#prdDes">
                                        <div class="accordion-content d-grid gap-14">
                                            @if ($product->description)
                                                {!! $product->description !!}
                                            @else
                                                <p class="text-caption">{{ $product->short_description ? strip_tags($product->short_description) : 'Von Heri Brennholz ausgewählter Holzbrennstoff.' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-action h6 collapsed" data-bs-target="#faq-carac" data-bs-toggle="collapse" role="button">
                                        <span>Technische Daten</span>
                                        <span class="icon ic-accordion-custom"></span>
                                    </div>
                                    <div id="faq-carac" class="collapse" data-bs-parent="#prdDes">
                                        <div class="accordion-content d-grid gap-14">
                                            <ul class="list text-caption">
                                                @if ($product->sku)<li><span class="br-dot"></span> Art.-Nr.: {{ $product->sku }}</li>@endif
                                                @if ($product->formatted_weight)<li><span class="br-dot"></span> Gewicht: {{ $product->formatted_weight }}</li>@endif
                                                @if ($product->dimensions)<li><span class="br-dot"></span> Maße: {{ $product->dimensions }}</li>@endif
                                                <li><span class="br-dot"></span> Kategorien:
                                                    @foreach ($product->categories as $c)<a href="{{ route('shop.category', $c) }}">{{ $c->name }}</a>@if(! $loop->last), @endif @endforeach
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="accordion-action h6 collapsed" data-bs-target="#faq-ship" data-bs-toggle="collapse" role="button">
                                        <span>Versand &amp; Rückgabe</span>
                                        <span class="icon ic-accordion-custom"></span>
                                    </div>
                                    <div id="faq-ship" class="collapse" data-bs-parent="#prdDes">
                                        <div class="accordion-content d-grid gap-14">
                                            <ul class="list text-caption">
                                                <li><span class="br-dot"></span> Lieferung in 1 bis 2 Werktagen innerhalb der Schweiz.</li>
                                                <li><span class="br-dot"></span> Kostenlose Lieferung in der ganzen Schweiz – ohne Mindestbestellwert.</li>
                                                <li><span class="br-dot"></span> 14 Tage freiwilliges Rückgaberecht (siehe <a href="/rueckgabe">Rückgabe</a>).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Sticky ATC ===== --}}
    <div class="tf-sticky-btn-atc">
        <div class="container">
            <div class="tf-height-observer">
                <div class="tf-sticky-atc-product">
                    <div class="atc-product-side">
                        <div class="prd_img">
                            <img loading="lazy" width="60" height="80" src="{{ $gallery->first()->url }}" alt="{{ $product->name }}">
                        </div>
                        <div class="prd_info d-none d-sm-grid">
                            <p class="name__prd fw-medium cl-text-main lh-24">{{ $product->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="tf-sticky-atc-infos">
                    <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="tf-sticky-atc-variant-price">
                            <span class="h6">{{ swiss_money((float) $product->price) }}</span>
                        </div>
                        <div class="tf-product-info-quantity">
                            <div class="wg-quantity py-2">
                                <button type="button" class="btn-quantity minus-btn"><i class="icon icon-Minus"></i></button>
                                <input class="quantity-product" type="text" name="quantity" value="1">
                                <button type="button" class="btn-quantity plus-btn"><i class="icon icon-Plus"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="tf-btn size2 rounded-6 animate-btn btn-add-to-cart" {{ $product->in_stock ? '' : 'disabled' }}>
                            In den Warenkorb
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Ähnliche Produkte ===== --}}
    @if ($related->isNotEmpty())
        <div class="flat-spacing pb-0">
            <div class="container">
                <h3 class="text-center spacing-bottom-2">Das könnte Ihnen auch gefallen</h3>
                <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3" data-mobile-sm="2"
                    data-mobile="2" data-space="15" data-pagination="2" data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                    <div class="swiper-wrapper">
                        @foreach ($related as $product)
                            <div class="swiper-slide">
                                @include('partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-line-default tf-sw-pagination d-md-none"></div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('jsonld')
    @if (! empty($googleProduct))
        <script type="application/ld+json">{!! json_encode($googleProduct->toJsonLd(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
@endpush

@push('styles')
<style>
    /* ===== Galerie fiche produit ===== */
    .boire-gallery { display: grid; grid-template-columns: 88px 1fr; gap: 16px; }
    .boire-gallery.no-thumbs { grid-template-columns: 1fr; }
    @media (max-width: 575px) { .boire-gallery { grid-template-columns: 1fr; } .boire-gallery-thumbs { flex-direction: row !important; overflow-x: auto; } }

    .boire-gallery-thumbs { display: flex; flex-direction: column; gap: 10px; }
    .boire-thumb { padding: 0; border: 1px solid #e5ded4; border-radius: 10px; overflow: hidden; background: #f6f2ec; cursor: pointer; aspect-ratio: 1/1; transition: border-color .15s; }
    .boire-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .boire-thumb.is-active { border-color: #1c140f; }

    .boire-gallery-main { position: relative; margin: 0; border-radius: 14px; overflow: hidden; background: #f6f2ec; cursor: zoom-in; aspect-ratio: 1/1; }
    .boire-gallery-main img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .35s ease; }
    .boire-gallery-main.zoomed img { transition: none; }
    .boire-gallery-zoomhint { position: absolute; right: 14px; bottom: 14px; width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255,255,255,.92); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(0,0,0,.15); }

    /* Lightbox */
    #boireLightbox { position: fixed; inset: 0; z-index: 100000; background: rgba(15,10,7,.92); display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: opacity .25s ease; }
    #boireLightbox.open { opacity: 1; visibility: visible; }
    #boireLightbox .bx-img { max-width: 92vw; max-height: 88vh; border-radius: 10px; transform: scale(.94); transition: transform .28s ease; box-shadow: 0 30px 80px rgba(0,0,0,.5); }
    #boireLightbox.open .bx-img { transform: scale(1); }
    #boireLightbox .bx-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 48px; height: 48px; border: 0; border-radius: 50%;
        background: rgba(255,255,255,.15); color: #fff; font-size: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    #boireLightbox .bx-btn:hover { background: rgba(255,255,255,.3); }
    #boireLightbox .bx-prev { left: 3vw; } #boireLightbox .bx-next { right: 3vw; }
    #boireLightbox .bx-close { top: 24px; right: 24px; transform: none; }
    #boireLightbox .bx-count { position: absolute; bottom: 24px; left: 0; right: 0; text-align: center; color: #fff; font-size: 13px; letter-spacing: .05em; }
    @media (max-width: 575px) { #boireLightbox .bx-prev { left: 6px; } #boireLightbox .bx-next { right: 6px; } }
</style>
@endpush

@push('scripts')
<script>
(function () {
    var wrap = document.getElementById('boireGallery');
    if (!wrap) return;
    var mainFig = document.getElementById('boireGalleryMain');
    var mainImg = document.getElementById('boireGalleryImg');
    var thumbs = Array.prototype.slice.call(wrap.querySelectorAll('.boire-thumb'));
    if (!thumbs.length) wrap.classList.add('no-thumbs');

    var data = [];
    try { data = JSON.parse(document.getElementById('boireGalleryData').textContent) || []; } catch (e) {}
    var current = 0;

    function show(i) {
        if (!data[i]) return;
        current = i;
        mainImg.src = data[i].src;
        mainImg.alt = data[i].alt || '';
        thumbs.forEach(function (t, k) { t.classList.toggle('is-active', k === i); });
    }
    thumbs.forEach(function (t) {
        t.addEventListener('click', function () { show(parseInt(t.dataset.index, 10)); });
        t.addEventListener('mouseenter', function () { show(parseInt(t.dataset.index, 10)); });
    });

    /* ---- Hover zoom (loupe) ---- */
    mainFig.addEventListener('mousemove', function (e) {
        if (window.matchMedia('(max-width: 991px)').matches) return;
        var r = mainFig.getBoundingClientRect();
        var x = ((e.clientX - r.left) / r.width) * 100;
        var y = ((e.clientY - r.top) / r.height) * 100;
        mainFig.classList.add('zoomed');
        mainImg.style.transformOrigin = x + '% ' + y + '%';
        mainImg.style.transform = 'scale(2)';
    });
    mainFig.addEventListener('mouseleave', function () {
        mainFig.classList.remove('zoomed');
        mainImg.style.transform = '';
    });

    /* ---- Lightbox ---- */
    var lb = document.getElementById('boireLightbox');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'boireLightbox';
        lb.innerHTML =
            '<button class="bx-btn bx-close" aria-label="Schließen">&times;</button>' +
            '<button class="bx-btn bx-prev" aria-label="Zurück">&#8249;</button>' +
            '<img class="bx-img" alt="">' +
            '<button class="bx-btn bx-next" aria-label="Weiter">&#8250;</button>' +
            '<div class="bx-count"></div>';
        document.body.appendChild(lb);
    }
    var lbImg = lb.querySelector('.bx-img');
    var lbCount = lb.querySelector('.bx-count');
    function render() {
        lbImg.src = data[current].src;
        lbCount.textContent = data.length > 1 ? (current + 1) + ' / ' + data.length : '';
        lb.querySelector('.bx-prev').style.display = data.length > 1 ? '' : 'none';
        lb.querySelector('.bx-next').style.display = data.length > 1 ? '' : 'none';
    }
    function openLb() { render(); lb.classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeLb() { lb.classList.remove('open'); document.body.style.overflow = ''; }
    function step(d) { current = (current + d + data.length) % data.length; render(); thumbs.forEach(function (t, k) { t.classList.toggle('is-active', k === current); }); mainImg.src = data[current].src; }

    mainFig.addEventListener('click', openLb);
    mainFig.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openLb(); } });
    lb.addEventListener('click', function (e) {
        if (e.target === lb || e.target.closest('.bx-close')) return closeLb();
        if (e.target.closest('.bx-prev')) return step(-1);
        if (e.target.closest('.bx-next')) return step(1);
    });
    document.addEventListener('keydown', function (e) {
        if (!lb.classList.contains('open')) return;
        if (e.key === 'Escape') closeLb();
        if (e.key === 'ArrowLeft') step(-1);
        if (e.key === 'ArrowRight') step(1);
    });
})();
</script>
@endpush
