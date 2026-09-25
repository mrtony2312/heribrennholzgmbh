@extends('layouts.omniva')

@section('title', 'Warenkorb')

@push('styles')
<style>
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #8a8178; font-weight: 600; padding: 0 0 14px; border-bottom: 1px solid #ece5da; }
    .cart-table td { padding: 20px 0; border-bottom: 1px solid #ece5da; vertical-align: middle; }
    .cart-prod { display: flex; align-items: center; gap: 16px; }
    .cart-prod img { width: 76px; height: 76px; object-fit: cover; border-radius: 10px; background: #f6f2ec; flex: 0 0 auto; }
    .cart-prod a { font-weight: 500; color: #1c140f; }

    .qty-stepper { display: inline-flex; align-items: center; border: 1px solid #ded6ca; border-radius: 999px; overflow: hidden; background: #fff; }
    .qty-stepper button { width: 38px; height: 40px; border: 0; background: transparent; font-size: 18px; line-height: 1; cursor: pointer; color: #1c140f; display: flex; align-items: center; justify-content: center; transition: background .15s; }
    .qty-stepper button:hover { background: #f4efe8; }
    .qty-stepper button:disabled { opacity: .35; cursor: not-allowed; }
    .qty-stepper input { width: 40px; height: 40px; border: 0; border-left: 1px solid #ede7dd; border-right: 1px solid #ede7dd; text-align: center; font-size: 14px; -moz-appearance: textfield; background: transparent; }
    .qty-stepper input::-webkit-outer-spin-button, .qty-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .cart-row.is-busy { opacity: .5; pointer-events: none; }
    .cart-line-total { font-weight: 600; white-space: nowrap; }
    .cart-remove { border: 0; background: transparent; color: #b0a89c; font-size: 22px; line-height: 1; cursor: pointer; padding: 4px 8px; }
    .cart-remove:hover { color: #c0392b; }

    .cart-summary { border: 1px solid #ece5da; border-radius: 14px; padding: 26px; position: sticky; top: 90px; }
    .cart-summary .row-line { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
    .cart-summary .row-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; padding-top: 14px; border-top: 2px solid #1c140f; }
    .cart-summary .hint { font-size: 12px; color: #8a8178; margin: 6px 0 0; }
</style>
@endpush

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Ihr Warenkorb'])

    <section class="flat-spacing-9">
        <div class="container" id="cartRoot"
             data-update-url="{{ route('cart.update') }}"
             data-remove-url="{{ route('cart.remove') }}"
             data-free-shipping="0">
            @if ($items->isEmpty())
                <div class="text-center py-5" id="cartEmpty">
                    <img src="{{ asset('assets/images/item/cart-empty.png') }}" alt="Warenkorb leer" width="160">
                    <p class="mt-3">Ihr Warenkorb ist leer.</p>
                    <a href="{{ route('shop.index') }}" class="tf-btn btn-fill animate-btn"><span>Zum Shop</span></a>
                </div>
            @else
                <div class="row g-4">
                    <div class="col-lg-8">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Produkt</th>
                                    <th>Einzelpreis</th>
                                    <th>Menge</th>
                                    <th class="text-end">Summe</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr class="cart-row" data-product-id="{{ $item['product']->id }}" data-price="{{ $item['product']->price }}">
                                        <td>
                                            <div class="cart-prod">
                                                <img src="{{ $item['product']->primary_image?->url ?? asset('assets/images/item/item-bg.jpg') }}" alt="{{ $item['product']->name }}">
                                                <a href="{{ route('product.show', $item['product']) }}">{{ $item['product']->name }}</a>
                                            </div>
                                        </td>
                                        <td>{{ swiss_money((float) $item['product']->price) }}</td>
                                        <td>
                                            <div class="qty-stepper" data-min="1" data-max="99">
                                                <button type="button" data-step="-1" aria-label="Weniger">&minus;</button>
                                                <input type="text" inputmode="numeric" class="cart-qty" value="{{ $item['quantity'] }}" aria-label="Menge">
                                                <button type="button" data-step="1" aria-label="Mehr">&plus;</button>
                                            </div>
                                        </td>
                                        <td class="text-end cart-line-total">{{ swiss_money($item['line_total']) }}</td>
                                        <td class="text-end">
                                            <button type="button" class="cart-remove" aria-label="Entfernen">&times;</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('shop.index') }}" class="tf-btn btn-outline animate-btn mt-4"><span>Weiter einkaufen</span></a>
                    </div>

                    <div class="col-lg-4">
                        <div class="cart-summary">
                            <h6 class="mb-3">Zusammenfassung</h6>
                            <div class="row-line"><span>Zwischensumme</span><span id="sumSubtotal">{{ swiss_money($cart->subtotal()) }}</span></div>
                            <div class="row-line"><span>Versand</span><span id="sumShipping">{{ $cart->shipping() > 0 ? swiss_money($cart->shipping()) : 'Kostenlos' }}</span></div>
                            <div class="row-total"><span>Gesamt</span><span id="sumTotal">{{ swiss_money($cart->total()) }}</span></div>
                            <p class="hint">Alle Preise inkl. {{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} % MWST.</p>
                            <p class="hint">Zahlungsarten: {{ payment_methods_text() }}.</p>
                            <p class="hint" id="shipHint"></p>
                            <a href="{{ route('checkout.index') }}" class="tf-btn btn-fill animate-btn w-100 mt-3"><span>Zur Kasse</span></a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    var root = document.getElementById('cartRoot');
    if (!root || document.getElementById('cartEmpty')) return;

    var csrf = document.querySelector('meta[name=csrf-token]').content;
    var updateUrl = root.dataset.updateUrl;
    var removeUrl = root.dataset.removeUrl;

    function eur(n) {
        return 'CHF ' + n.toLocaleString('de-CH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace(/’|’/g, "'");
    }

    function applySummary(data) {
        var st = document.getElementById('sumSubtotal'); if (st) st.textContent = eur(data.subtotal);
        var sh = document.getElementById('sumShipping'); if (sh) sh.textContent = data.shipping > 0 ? eur(data.shipping) : 'Kostenlos';
        var to = document.getElementById('sumTotal'); if (to) to.textContent = eur(data.total);
        document.querySelectorAll('.js-cart-count').forEach(function (el) { el.textContent = data.count; });

        var hint = document.getElementById('shipHint');
        if (hint) {
            hint.textContent = data.subtotal > 0 ? 'Versand kostenlos in der ganzen Schweiz.' : '';
        }
        if (data.empty) window.location.reload();
    }

    function send(row, qty) {
        row.classList.add('is-busy');
        return fetch(updateUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ _method: 'PATCH', product_id: row.dataset.productId, quantity: qty })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.removed) { row.remove(); }
            else {
                var lt = row.querySelector('.cart-line-total');
                if (lt) lt.textContent = eur(data.line_total);
            }
            applySummary(data);
        })
        .catch(function () { window.location.reload(); })
        .finally(function () { row.classList.remove('is-busy'); });
    }

    var timers = {};
    function queueUpdate(row) {
        var input = row.querySelector('.cart-qty');
        var val = Math.max(1, Math.min(99, parseInt(input.value, 10) || 1));
        input.value = val;
        var id = row.dataset.productId;
        clearTimeout(timers[id]);
        timers[id] = setTimeout(function () { send(row, val); }, 250);
    }

    root.addEventListener('click', function (e) {
        var stepBtn = e.target.closest('.qty-stepper button[data-step]');
        if (stepBtn) {
            var row = stepBtn.closest('.cart-row');
            var input = row.querySelector('.cart-qty');
            input.value = (parseInt(input.value, 10) || 1) + parseInt(stepBtn.dataset.step, 10);
            queueUpdate(row);
            return;
        }
        var rm = e.target.closest('.cart-remove');
        if (rm) {
            var row2 = rm.closest('.cart-row');
            row2.classList.add('is-busy');
            fetch(removeUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ _method: 'DELETE', product_id: row2.dataset.productId })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) { row2.remove(); applySummary(data); })
            .catch(function () { window.location.reload(); });
        }
    });

    root.addEventListener('input', function (e) {
        if (e.target.classList.contains('cart-qty')) queueUpdate(e.target.closest('.cart-row'));
    });

    applySummary({
        subtotal: parseFloat('{{ $cart->subtotal() }}'),
        shipping: parseFloat('{{ $cart->shipping() }}'),
        total: parseFloat('{{ $cart->total() }}'),
        count: parseInt('{{ $cart->count() }}', 10),
        empty: false
    });
})();
</script>
@endpush
