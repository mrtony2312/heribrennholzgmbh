@php
    $hasActiveFilters = ! empty($activeCategorySlugs) || request()->filled('min') || request()->filled('max')
        || request()->boolean('in_stock') || request()->boolean('on_sale') || ($search ?? '') !== '';
@endphp
<div id="shopResults">
    <span id="shopTitleValue" hidden>{{ $pageTitle }}</span>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="count-text text-caption" id="resultCount">
            {{ $products->total() }} Produkt{{ $products->total() > 1 ? 'e' : '' }}
        </div>
    </div>

    @if ($hasActiveFilters)
        <div class="shop-chips">
            @foreach ($activeCategorySlugs as $slug)
                @php $c = $categories->firstWhere('slug', $slug); @endphp
                @if ($c)<span class="shop-chip">{{ $c->name }} <a href="#" data-uncheck="categories[]" data-val="{{ $slug }}">&times;</a></span>@endif
            @endforeach
            @if (request()->boolean('in_stock'))<span class="shop-chip">Auf Lager <a href="#" data-uncheck="in_stock">&times;</a></span>@endif
            @if (request()->boolean('on_sale'))<span class="shop-chip">Im Angebot <a href="#" data-uncheck="on_sale">&times;</a></span>@endif
            @if (request()->filled('min') || request()->filled('max'))
                <span class="shop-chip">Preis {{ request('min', 0) }}–{{ request('max', $priceBounds['max']) }} CHF <a href="#" data-clearprice="1">&times;</a></span>
            @endif
            <a href="{{ route('shop.index') }}" class="text-caption text-decoration-underline ms-1 js-reset-filters">Alle entfernen</a>
        </div>
    @endif

    @if ($products->isEmpty())
        <p class="py-5 text-center">Keine Produkte entsprechen Ihrer Suche.</p>
    @else
        <div class="wrapper-shop tf-grid-layout {{ $gridCols ?? 'tf-col-4' }} gap-20" id="gridLayout">
            @foreach ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="wd-full d-flex justify-content-center shop-pagination-wrap">
            {{ $products->onEachSide(1)->links('pagination.shop') }}
        </div>
    @endif
</div>
