@php $drawerItems = $cart->items(); @endphp
<span id="drawerCount" hidden>{{ $cart->count() }}</span>
@if ($drawerItems->isEmpty())
    <div class="boire-cart-empty">
        <img src="{{ asset('assets/images/item/cart-empty.png') }}" alt="Warenkorb leer" width="120">
        <p>Ihr Warenkorb ist leer.</p>
        <a href="{{ route('shop.index') }}" class="tf-btn btn-fill animate-btn"><span>Zum Shop</span></a>
    </div>
@else
    <div class="boire-cart-scroll">
        @foreach ($drawerItems as $item)
            <div class="boire-cart-item">
                <a href="{{ route('product.show', $item['product']) }}" class="boire-cart-thumb">
                    <img src="{{ $item['product']->primary_image?->url ?? asset('assets/images/item/item-bg.jpg') }}" alt="{{ $item['product']->name }}">
                </a>
                <div class="boire-cart-meta">
                    <a href="{{ route('product.show', $item['product']) }}" class="boire-cart-name">{{ $item['product']->name }}</a>
                    <div class="boire-cart-line">{{ $item['quantity'] }} &times; {{ swiss_money((float) $item['product']->price) }}</div>
                    <form action="{{ route('cart.remove') }}" method="POST" class="js-cart-remove">
                        @csrf @method('DELETE')
                        <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                        <button type="submit" class="boire-cart-remove">Entfernen</button>
                    </form>
                </div>
                <div class="boire-cart-price">{{ swiss_money($item['line_total']) }}</div>
            </div>
        @endforeach
    </div>

    <div class="boire-cart-foot">
        <div class="boire-cart-subtotal">
            <span>Zwischensumme</span>
            <span>{{ swiss_money($cart->subtotal()) }}</span>
        </div>
        <p class="boire-cart-note">Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.</p>
        <div class="boire-cart-actions">
            <a href="{{ route('cart.index') }}" class="tf-btn btn-outline animate-btn"><span>Warenkorb ansehen</span></a>
            <a href="{{ route('checkout.index') }}" class="tf-btn btn-fill animate-btn"><span>Zur Kasse</span></a>
        </div>
    </div>
@endif
