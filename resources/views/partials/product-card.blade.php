@php
    $img = $product->primary_image;
    $hover = $product->images->get(1);
    $inWishlist = $wishlist->has($product->id);
    $hasPromo = $product->on_sale && $product->regular_price && $product->sale_price && $product->regular_price > $product->price;
@endphp
<div class="card-product">
    <div class="card-product_wrapper">
        <a href="{{ route('product.show', $product) }}" class="product-img">
            <img class="img-product" loading="lazy" src="{{ $img?->url ?? asset('assets/images/item/item-bg.jpg') }}" alt="{{ $product->name }}">
            @if ($hover)
                <img class="img-hover" loading="lazy" src="{{ $hover->url }}" alt="{{ $product->name }}">
            @endif
        </a>

        @if ($hasPromo)
            <ul class="product-badge_list">
                <li class="product-badge_item text-caption sale">
                    -{{ (int) round(100 - ($product->sale_price / max((float) $product->regular_price, 0.01) * 100)) }}%
                </li>
            </ul>
        @elseif (! $product->in_stock)
            <ul class="product-badge_list"><li class="product-badge_item text-caption">Rupture</li></ul>
        @endif

        <ul class="product-action_list">
            <li class="wishlist {{ $inWishlist ? 'active' : '' }}">
                <a href="#" data-id="{{ $product->id }}"
                   class="js-wishlist hover-tooltip tooltip-left box-icon">
                    <span class="icon {{ $inWishlist ? 'icon-Trash' : 'icon-Hearth2' }}"></span>
                    <span class="tooltip">{{ $inWishlist ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen' }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('product.show', $product) }}"
                   data-url="{{ route('product.quickview', $product) }}"
                   class="js-quickview hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-EyeStroke"></span>
                    <span class="tooltip">Schnellansicht</span>
                </a>
            </li>
        </ul>

        <div class="product-action_bot">
            <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn-action_choose text-caption" {{ $product->in_stock ? '' : 'disabled' }}>
                    {{ $product->in_stock ? 'In den Warenkorb' : 'Nicht verfügbar' }}
                </button>
            </form>
        </div>
    </div>

    <div class="card-product_info">
        <a href="{{ route('product.show', $product) }}" class="name-product fw-medium link-underline">{{ $product->name }}</a>
        <div class="price-wrap h6">
            @if ($hasPromo)
                <span class="price-old text-decoration-line-through opacity-50 me-1">{{ swiss_money((float) $product->regular_price) }}</span>
            @endif
            <span class="price-new fw-medium">{{ swiss_money((float) $product->price) }}</span>
        </div>
    </div>
</div>
