@extends('layouts.omniva')

@section('title', 'Bestellung')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Bestellung abschließen'])

    <section class="flat-spacing-9">
        <div class="container">
            <form action="{{ route('checkout.store') }}" method="POST" class="row">
                @csrf
                <div class="col-lg-7">
                    <h6 class="mb-3">Kontakt- &amp; Lieferdaten</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><input class="form-control" name="first_name" placeholder="Vorname *" value="{{ old('first_name') }}"></div>
                        <div class="col-md-6"><input class="form-control" name="last_name" placeholder="Nachname *" value="{{ old('last_name') }}"></div>
                        <div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="E-Mail *" value="{{ old('email') }}"></div>
                        <div class="col-md-6"><input class="form-control" name="phone" placeholder="Telefon" value="{{ old('phone') }}"></div>
                        <div class="col-12"><input class="form-control" name="address" placeholder="Adresse *" value="{{ old('address') }}"></div>
                        <div class="col-12"><input class="form-control" name="address_2" placeholder="Adresszusatz" value="{{ old('address_2') }}"></div>
                        <div class="col-md-4"><input class="form-control" name="postcode" placeholder="PLZ *" maxlength="4" value="{{ old('postcode') }}"></div>
                        <div class="col-md-4"><input class="form-control" name="city" placeholder="Stadt *" value="{{ old('city') }}"></div>
                        <div class="col-md-4">
                            <input type="hidden" name="country" value="Schweiz">
                            <input class="form-control" value="Schweiz" disabled>
                        </div>
                        <div class="col-12"><textarea class="form-control" name="notes" rows="3" placeholder="Anmerkungen (optional)">{{ old('notes') }}</textarea></div>
                    </div>
                    <h6 class="mt-4 mb-3">Zahlungsart</h6>
                    <div class="d-flex flex-column gap-2">
                        @foreach (payment_methods() as $key => $label)
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="payment_method" value="{{ $key }}" {{ old('payment_method', array_key_first(payment_methods())) === $key ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                        <p class="text-caption opacity-75 mb-0">Nach Bestelleingang erhalten Sie die Zahlungsdetails bzw. die Rechnung per E-Mail.</p>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-wrap gap-4 mb-4">
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
                    <div style="border:1px solid #ece7e1;border-radius:12px;padding:24px;">
                        <h6 class="mb-3">Ihre Bestellung</h6>
                        @foreach ($items as $item)
                            <div class="d-flex justify-content-between mb-2 text-caption">
                                <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                                <span>{{ swiss_money($item['line_total']) }}</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between mb-2"><span>Zwischensumme</span><span>{{ swiss_money($cart->subtotal()) }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><span>Versand</span><span>{{ $cart->shipping() > 0 ? swiss_money($cart->shipping()) : 'Kostenlos' }}</span></div>
                        <div class="d-flex justify-content-between fw-semibold h6"><span>Gesamt</span><span>{{ swiss_money($cart->total()) }}</span></div>
                        <div class="d-flex justify-content-between text-caption opacity-75"><span>Darin enthaltene MWST ({{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} %)</span><span>{{ swiss_money(vat_included($cart->total())) }}</span></div>
                        <button type="submit" class="tf-btn btn-fill animate-btn w-100 mt-3"><span>Bestellung bestätigen</span></button>
                        <p class="text-caption mt-2 opacity-75">Alle Preise in CHF, inklusive gesetzlicher Mehrwertsteuer.</p>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
