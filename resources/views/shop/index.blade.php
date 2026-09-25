@extends('layouts.omniva')

@section('title', $pageTitle)

@push('styles')
<style>
    .shop-wrap { display: grid; grid-template-columns: 300px 1fr; gap: 48px; align-items: start; }
    @media (max-width: 1199px) { .shop-wrap { display: block; } }

    /* Mobile : toujours 2 produits par ligne, quel que soit le choix de disposition */
    @media (max-width: 767px) {
        .wrapper-shop.tf-grid-layout { grid-template-columns: 1fr 1fr !important; column-gap: 12px; row-gap: 20px; }
    }

    /* Sidebar */
    .shop-sidebar { position: sticky; top: 90px; }
    .shop-sidebar .widget-facet { border-bottom: 1px solid #ece5da; padding: 20px 0; }
    .shop-sidebar .widget-facet:first-child { padding-top: 0; }
    .shop-sidebar .widget-facet:last-child { border-bottom: 0; }
    .shop-sidebar .facet-title { display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 6px; }
    .shop-sidebar .facet-title .icon { transition: transform .2s; }
    .shop-sidebar .facet-title[aria-expanded="false"] .icon { transform: rotate(-90deg); }
    .shop-sidebar .filter-list { list-style: none; margin: 0; padding: 6px 0 0; }
    .shop-sidebar .filter-list li { padding: 6px 0; }
    .shop-sidebar .filter-list label { display: flex; align-items: center; gap: 10px; margin: 0; cursor: pointer; font-size: 14px; }
    .shop-sidebar .filter-list label .count { margin-left: auto; opacity: .45; }
    .shop-sidebar input[type="checkbox"] { width: 16px; height: 16px; accent-color: #1c140f; flex: 0 0 auto; }

    /* nouislider (styles Omniva déjà dans styles.css) */
    .filter-price { padding-top: 14px; }
    .filter-price .price-val-range { margin: 6px 4px 18px; }
    .filter-price .price-box { display: flex; align-items: center; gap: 12px; }
    .filter-price .price-val_wrap { display: inline-flex; gap: 3px; border: 1px solid #e2dbd1; border-radius: 6px; padding: 6px 10px; }
    .filter-price .br-line { flex: 1; height: 1px; background: #d9d1c5; }

    /* Toolbar */
    .shop-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
        padding-bottom: 16px; border-bottom: 1px solid #ece5da; margin-bottom: 24px; }
    .shop-toolbar .tf-control-layout { display: flex; gap: 6px; list-style: none; margin: 0; padding: 0; }
    .shop-toolbar .tf-control-layout li { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
        border: 1px solid #e2dbd1; border-radius: 6px; cursor: pointer; }
    .shop-toolbar .tf-control-layout li.active { background: #1c140f; color: #fff; border-color: #1c140f; }
    .shop-sort-select { border: 1px solid #e2dbd1; border-radius: 6px; padding: 8px 14px; background: #fff; font-size: 14px; }
    #btnOpenFilters { display: none; }
    @media (max-width: 1199px) { #btnOpenFilters { display: inline-flex; } }

    .shop-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
    .shop-chip { display: inline-flex; align-items: center; gap: 6px; background: #f4efe8; border-radius: 999px; padding: 6px 12px; font-size: 13px; }
    .shop-chip a { line-height: 1; font-weight: 700; text-decoration: none; }

    .shop-pagination-wrap { margin-top: 56px; padding-top: 32px; border-top: 1px solid #ece5da; }
    .flat-spacing-9 .pagination { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; }
    .flat-spacing-9 .pagination .page-item { margin: 0; }
    .flat-spacing-9 .pagination .page-link { display: flex; align-items: center; justify-content: center; min-width: 40px; height: 40px;
        padding: 0 12px; border: 1px solid #e2dbd1; border-radius: 8px; color: #1c140f; background: #fff; font-size: 14px; line-height: 1; text-decoration: none; }
    .flat-spacing-9 .pagination .page-link:hover { border-color: #1c140f; }
    .flat-spacing-9 .pagination .page-item.active .page-link { background: #1c140f; border-color: #1c140f; color: #fff; }
    .flat-spacing-9 .pagination .page-item.disabled .page-link { opacity: .4; pointer-events: none; }
    .flat-spacing-9 .pagination svg { width: 16px; height: 16px; }

    #shopResults.is-loading { opacity: .45; pointer-events: none; transition: opacity .15s; }

    /* Sidebar en tiroir sur mobile */
    @media (max-width: 1199px) {
        .shop-sidebar { position: fixed; top: 0; left: 0; z-index: 1060; width: 320px; max-width: 86vw; height: 100%;
            background: #fff; padding: 24px; overflow-y: auto; transform: translateX(-100%); transition: transform .25s ease;
            box-shadow: 0 0 40px rgba(0,0,0,.15); }
        .shop-sidebar.show { transform: none; }
        .shop-sidebar-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        #shopBackdrop { position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1055; opacity: 0; visibility: hidden; transition: .2s; }
        #shopBackdrop.show { opacity: 1; visibility: visible; }
    }
    @media (min-width: 1200px) { .shop-sidebar-head, #shopBackdrop { display: none; } }
</style>
@endpush

@section('content')
    @php
        $ptSlug = (isset($activeCategorySlugs) && count($activeCategorySlugs) === 1)
            ? 'cat-' . $activeCategorySlugs[0]
            : 'hero-2';
    @endphp
    @include('partials.page-title', ['pageTitle' => $pageTitle, 'titleImageSlug' => $ptSlug])

    <div class="flat-spacing-9">
        <div class="container">
            {{-- Kein <form> hier: die Produktkarten enthalten eigene "In den Warenkorb"-Formulare,
                 verschachtelte Formulare sind ungültig. Die Filter werden per JS zu einer URL zusammengesetzt. --}}
            <div id="shopFilterForm" data-action="{{ route('shop.index') }}">
                @if ($search)<input type="hidden" name="q" value="{{ $search }}">@endif

                <div class="shop-wrap">
                    {{-- ============ SIDEBAR ============ --}}
                    <aside class="shop-sidebar" id="shopSidebar">
                        <div class="shop-sidebar-head">
                            <span class="h6 mb-0"><i class="icon icon-Filter"></i> Filter</span>
                            <button type="button" class="btn-close" id="closeFilters" aria-label="Schließen"></button>
                        </div>

                        <div class="widget-facet">
                            <div class="facet-title" data-bs-toggle="collapse" data-bs-target="#facetCat" role="button" aria-expanded="true">
                                <span class="text-uppercase fw-medium">Kategorien</span>
                                <span class="icon icon-ArrowCaretDown"></span>
                            </div>
                            <div id="facetCat" class="collapse show">
                                <ul class="filter-list">
                                    @foreach ($categories as $cat)
                                        <li>
                                            <label>
                                                <input type="checkbox" name="categories[]" value="{{ $cat->slug }}" @checked(in_array($cat->slug, $activeCategorySlugs))>
                                                <span>{{ $cat->name }}</span>
                                                <span class="count">({{ $cat->products_count }})</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="widget-facet">
                            <div class="facet-title" data-bs-toggle="collapse" data-bs-target="#facetAvail" role="button" aria-expanded="true">
                                <span class="text-uppercase fw-medium">Verfügbarkeit</span>
                                <span class="icon icon-ArrowCaretDown"></span>
                            </div>
                            <div id="facetAvail" class="collapse show">
                                <ul class="filter-list">
                                    <li><label><input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock'))><span>Nur auf Lager</span></label></li>
                                    <li><label><input type="checkbox" name="on_sale" value="1" @checked(request()->boolean('on_sale'))><span>Im Angebot</span></label></li>
                                </ul>
                            </div>
                        </div>

                        <div class="widget-facet">
                            <div class="facet-title" data-bs-toggle="collapse" data-bs-target="#facetPrice" role="button" aria-expanded="true">
                                <span class="text-uppercase fw-medium">Preis (CHF)</span>
                                <span class="icon icon-ArrowCaretDown"></span>
                            </div>
                            <div id="facetPrice" class="collapse show">
                                <div class="filter-price">
                                    <div class="price-val-range" id="price-value-range" data-min="0" data-max="{{ $priceBounds['max'] }}"></div>
                                    <div class="price-box">
                                        <div class="price-val_wrap text-caption"><span>CHF</span><span class="price-val" id="price-min-value">0</span></div>
                                        <span class="br-line"></span>
                                        <div class="price-val_wrap text-caption"><span>CHF</span><span class="price-val" id="price-max-value">{{ $priceBounds['max'] }}</span></div>
                                    </div>
                                    <input type="hidden" name="min" id="priceMinInput" value="{{ request('min') }}">
                                    <input type="hidden" name="max" id="priceMaxInput" value="{{ request('max') }}">
                                </div>
                            </div>
                        </div>
                    </aside>
                    <div id="shopBackdrop"></div>

                    {{-- ============ PRODUITS ============ --}}
                    <div>
                        <div class="shop-toolbar">
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" id="btnOpenFilters" class="btn btn-sm btn-outline-dark">
                                    <span class="icon icon-Filter"></span> Filter
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <ul class="tf-control-layout" id="layoutSwitch">
                                    <li data-cols="tf-col-2"><i class="icon-Grid2Col"></i></li>
                                    <li data-cols="tf-col-3"><i class="icon-Grid3Col"></i></li>
                                    <li data-cols="tf-col-4" class="active"><i class="icon-Grid4Col"></i></li>
                                </ul>
                                <select name="sort" class="shop-sort-select">
                                    <option value="featured" @selected($sort === 'featured')>Empfohlen</option>
                                    <option value="newest" @selected($sort === 'newest')>Neuheiten</option>
                                    <option value="a-z" @selected($sort === 'a-z')>Name, A–Z</option>
                                    <option value="z-a" @selected($sort === 'z-a')>Name, Z–A</option>
                                    <option value="price-asc" @selected($sort === 'price-asc')>Preis aufsteigend</option>
                                    <option value="price-desc" @selected($sort === 'price-desc')>Preis absteigend</option>
                                </select>
                            </div>
                        </div>

                        @include('shop._results')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="/assets/js/plugin/nouislider.min.js"></script>
<script>
(function () {
    var form = document.getElementById('shopFilterForm');
    var results = document.getElementById('shopResults');
    var maxBound = {{ $priceBounds['max'] }};

    /* ---------- disposition grille ---------- */
    function applyCols(cols) {
        var grid = document.getElementById('gridLayout');
        if (grid) grid.className = 'wrapper-shop tf-grid-layout gap-20 ' + cols;
        document.querySelectorAll('#layoutSwitch li').forEach(function (li) {
            li.classList.toggle('active', li.dataset.cols === cols);
        });
        try { localStorage.setItem('shopCols', cols); } catch (e) {}
    }
    var savedCols = 'tf-col-4';
    try { savedCols = localStorage.getItem('shopCols') || 'tf-col-4'; } catch (e) {}
    applyCols(savedCols);
    document.getElementById('layoutSwitch').addEventListener('click', function (e) {
        var li = e.target.closest('li[data-cols]');
        if (li) applyCols(li.dataset.cols);
    });

    /* ---------- URL aus den Filter-Feldern zusammensetzen (ohne <form>) ---------- */
    function buildUrl(extra) {
        var params = new URLSearchParams();
        form.querySelectorAll('input[name], select[name], textarea[name]').forEach(function (el) {
            var name = el.getAttribute('name');
            if (el.disabled || !name) return;
            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
            var val = el.value;
            if (val === '' || val === null) return;
            params.append(name, val);
        });
        if (extra && extra.page) params.set('page', extra.page);
        var qs = params.toString();
        return form.dataset.action + (qs ? '?' + qs : '');
    }

    function resetFilters() {
        form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function (c) { c.checked = false; });
        var pmin = document.getElementById('priceMinInput'); if (pmin) pmin.value = '';
        var pmax = document.getElementById('priceMaxInput'); if (pmax) pmax.value = '';
        var srt = form.querySelector('select[name="sort"]'); if (srt) srt.value = 'featured';
        if (slider && slider.noUiSlider) slider.noUiSlider.set([0, maxBound]);
    }

    /* ---------- rafraîchissement AJAX ---------- */
    var busy = false;
    function refresh(url) {
        if (busy) return;
        busy = true;
        results.classList.add('is-loading');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('shopResults');
                if (fresh) {
                    results.innerHTML = fresh.innerHTML;
                } else {
                    results.innerHTML = html; // réponse partielle directe
                }
                applyCols(savedCols);
                var t = results.querySelector('#shopTitleValue');
                if (t) {
                    document.querySelectorAll('.js-page-title').forEach(function (el) { el.textContent = t.textContent; });
                    document.title = t.textContent + ' — Heri Brennholz';
                }
                history.pushState(null, '', url);
            })
            .catch(function () { window.location = url; })
            .finally(function () { busy = false; results.classList.remove('is-loading'); });
    }

    /* ---------- déclencheurs ---------- */
    form.addEventListener('change', function (e) {
        if (e.target.closest('#price-value-range')) return; // géré par le slider
        refresh(buildUrl());
    });

    // pagination + puces (contenu remplacé => délégation)
    results.addEventListener('click', function (e) {
        var page = e.target.closest('.pagination a');
        if (page) {
            e.preventDefault();
            refresh(page.href);
            return;
        }
        var chip = e.target.closest('[data-uncheck]');
        if (chip) {
            e.preventDefault();
            var name = chip.dataset.uncheck;
            form.querySelectorAll('[name="' + name + '"]').forEach(function (inp) {
                if (chip.dataset.val === undefined || inp.value === chip.dataset.val) {
                    inp.checked = false;
                }
            });
            refresh(buildUrl());
            return;
        }
        if (e.target.closest('[data-clearprice]')) {
            e.preventDefault();
            document.getElementById('priceMinInput').value = '';
            document.getElementById('priceMaxInput').value = '';
            if (slider && slider.noUiSlider) slider.noUiSlider.set([0, maxBound]);
            refresh(buildUrl());
            return;
        }
        if (e.target.closest('.js-reset-filters')) {
            e.preventDefault();
            resetFilters();
            refresh(form.dataset.action);
        }
    });

    /* ---------- slider de prix ---------- */
    var slider = document.getElementById('price-value-range');
    if (slider && window.noUiSlider) {
        var minInput = document.getElementById('priceMinInput');
        var maxInput = document.getElementById('priceMaxInput');
        var startMin = parseInt(minInput.value, 10); if (isNaN(startMin)) startMin = 0;
        var startMax = parseInt(maxInput.value, 10); if (isNaN(startMax)) startMax = maxBound;
        noUiSlider.create(slider, {
            start: [startMin, startMax], connect: true, step: 1,
            range: { min: 0, max: maxBound }
        });
        var lblMin = document.getElementById('price-min-value');
        var lblMax = document.getElementById('price-max-value');
        slider.noUiSlider.on('update', function (values) {
            var lo = Math.round(values[0]), hi = Math.round(values[1]);
            lblMin.textContent = lo; lblMax.textContent = hi;
        });
        slider.noUiSlider.on('change', function (values) {
            var lo = Math.round(values[0]), hi = Math.round(values[1]);
            minInput.value = lo <= 0 ? '' : lo;
            maxInput.value = hi >= maxBound ? '' : hi;
            refresh(buildUrl());
        });
    }

    /* ---------- tiroir mobile ---------- */
    var sidebar = document.getElementById('shopSidebar');
    var backdrop = document.getElementById('shopBackdrop');
    function openF() { sidebar.classList.add('show'); backdrop.classList.add('show'); }
    function closeF() { sidebar.classList.remove('show'); backdrop.classList.remove('show'); }
    document.getElementById('btnOpenFilters').addEventListener('click', openF);
    document.getElementById('closeFilters').addEventListener('click', closeF);
    backdrop.addEventListener('click', closeF);

    /* ---------- back/forward ---------- */
    window.addEventListener('popstate', function () { refresh(location.href); });
})();
</script>
@endpush
