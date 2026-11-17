@extends('layouts.omniva')

@section('title', 'Bestellung bestätigt')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Vielen Dank für Ihre Bestellung'])

    <section class="flat-spacing-9">
        <div class="container" style="max-width:720px;">
            <div class="text-center mb-4">
                <div class="h4">Bestellung <strong>{{ $order->reference }}</strong> eingegangen</div>
                <p class="opacity-75">Eine Bestätigung wurde an {{ $order->email }} gesendet.</p>
            </div>

            <div style="border:1px solid #ece7e1;border-radius:12px;padding:24px;">
                <table class="table">
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->name }} × {{ $item->quantity }}</td>
                                <td class="text-end">{{ swiss_money((float) $item->line_total) }}</td>
                            </tr>
                        @endforeach
                        <tr><td>Zwischensumme</td><td class="text-end">{{ swiss_money((float) $order->subtotal) }}</td></tr>
                        <tr><td>Versand</td><td class="text-end">{{ $order->shipping > 0 ? swiss_money((float) $order->shipping) : 'Kostenlos' }}</td></tr>
                        <tr class="fw-semibold"><td>Gesamtbetrag</td><td class="text-end">{{ swiss_money((float) $order->total) }}</td></tr>
                        <tr class="text-caption"><td>Darin enthaltene MWST ({{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} %)</td><td class="text-end">{{ swiss_money(vat_included($order->total)) }}</td></tr>
                        @if ($order->payment_method)
                            <tr class="text-caption"><td>Zahlungsart</td><td class="text-end">{{ config('shop.payment_methods.'.$order->payment_method.'.label', $order->payment_method) }}</td></tr>
                        @endif
                        @if ($order->payment_proof_path)
                            <tr class="text-caption"><td>Zahlungsnachweis</td><td class="text-end">Hochgeladen</td></tr>
                        @endif
                    </tbody>
                </table>
                <p class="mb-1"><strong>Lieferadresse:</strong> {{ $order->first_name }} {{ $order->last_name }}, {{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif, {{ $order->postcode }} {{ $order->city }}, {{ $order->country }}</p>

                <div class="mt-3 pt-3" style="border-top:1px solid #ece7e1;">
                    <h6 class="mb-2">Bankdaten für Ihre Überweisung</h6>
                    <p class="mb-1 text-caption">Kontoinhaber: <strong>{{ $bank['account_holder'] }}</strong></p>
                    @if ($bank['bank_name'])<p class="mb-1 text-caption">Bank: {{ $bank['bank_name'] }}</p>@endif
                    <p class="mb-1 text-caption">IBAN: <strong>{{ format_iban($bank['iban']) }}</strong></p>
                    @if ($bank['bic'])<p class="mb-1 text-caption">BIC: {{ $bank['bic'] }}</p>@endif
                    <p class="mb-0 text-caption opacity-75">Verwendungszweck: {{ $order->reference }}</p>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('shop.index') }}" class="tf-btn btn-fill animate-btn"><span>Zurück zum Shop</span></a>
            </div>
        </div>
    </section>
@endsection
