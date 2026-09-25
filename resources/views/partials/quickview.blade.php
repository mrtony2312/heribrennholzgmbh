@php $qvImgs = $product->images; @endphp
<div class="row g-0">
    <div class="col-md-6">
        <div class="p-3">
            <img id="qvMain-{{ $product->id }}"
                 src="{{ $qvImgs->first()?->url ?? asset('assets/images/item/item-bg.jpg') }}"
                 alt="{{ $product->name }}"
                 style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:10px;background:#f6f2ec;">
            @if ($qvImgs->count() > 1)
                <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                    @foreach ($qvImgs->take(5) as $im)
                        <img src="{{ $im->url }}" alt="{{ $im->alt }}"
                             style="width:60px;height:60px;object-fit:cover;border-radius:6px;cursor:pointer;border:1px solid #e5ded6;"
                             onclick="document.getElementById('qvMain-{{ $product->id }}').src=this.src">
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-4">
            <p class="text-caption cl-text-3 mb-1">
                @foreach ($product->categories as $c){{ $c->name }}@if (! $loop->last), @endif @endforeach
            </p>
            <h5 class="mb-2">{{ $product->name }}</h5>
            <div class="h6 mb-2">
                @if ($product->on_sale && $product->regular_price > $product->price)
                    <span class="text-decoration-line-through opacity-50 me-1">{{ swiss_money((float) $product->regular_price) }}</span>
                @endif
                <span class="fw-semibold">{{ swiss_money((float) $product->price) }}</span>
            </div>
            <p class="mb-3">
                <span class="badge {{ $product->in_stock ? 'bg-success' : 'bg-secondary' }}">
                    {{ $product->in_stock ? 'Auf Lager' : 'Ausverkauft' }}
                </span>
            </p>
            @if ($product->short_description)
                <div class="text-caption mb-3" style="max-height:9em;overflow:auto;">
                    {!! \Illuminate\Support\Str::limit(strip_tags($product->short_description), 320) !!}
                </div>
            @endif
            <form action="{{ route('cart.add') }}" method="POST" class="js-add-to-cart" style="display:flex;gap:8px;align-items:center;margin-bottom:14px;">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="number" name="quantity" value="1" min="1" max="99" class="form-control" style="width:80px;">
                <button type="submit" class="tf-btn btn-fill animate-btn" {{ $product->in_stock ? '' : 'disabled' }}>
                    <span>In den Warenkorb</span>
                </button>
            </form>
            <a href="{{ route('product.show', $product) }}" class="link-underline text-caption">Zur vollständigen Produktseite →</a>
        </div>
    </div>
</div>
