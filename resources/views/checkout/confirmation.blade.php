@extends('layouts.omniva')

@section('title', 'Bestellung bestätigt')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Vielen Dank für Ihre Bestellung'])

    <section class="flat-spacing-9">
        <div class="container confirmation-page">
            <div class="text-center mb-4 px-1">
                <div class="confirmation-heading">Bestellung <strong>{{ $order->reference }}</strong> eingegangen</div>
                <p class="opacity-75 mb-0 confirmation-lead">Eine Bestätigung wurde an <span class="confirmation-break">{{ $order->email }}</span> gesendet.</p>
            </div>

            <div class="confirmation-card">
                <ul class="confirmation-lines list-unstyled mb-0">
                    @foreach ($order->items as $item)
                        <li class="confirmation-line">
                            <span class="confirmation-line-label">{{ $item->name }} × {{ $item->quantity }}</span>
                            <span class="confirmation-line-value">{{ swiss_money((float) $item->line_total) }}</span>
                        </li>
                    @endforeach
                    <li class="confirmation-line">
                        <span class="confirmation-line-label">Zwischensumme</span>
                        <span class="confirmation-line-value">{{ swiss_money((float) $order->subtotal) }}</span>
                    </li>
                    <li class="confirmation-line">
                        <span class="confirmation-line-label">Versand</span>
                        <span class="confirmation-line-value">{{ $order->shipping > 0 ? swiss_money((float) $order->shipping) : 'Kostenlos' }}</span>
                    </li>
                    <li class="confirmation-line confirmation-line-total">
                        <span class="confirmation-line-label">Gesamtbetrag</span>
                        <span class="confirmation-line-value">{{ swiss_money((float) $order->total) }}</span>
                    </li>
                    <li class="confirmation-line confirmation-line-muted">
                        <span class="confirmation-line-label">Darin enthaltene MWST ({{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} %)</span>
                        <span class="confirmation-line-value">{{ swiss_money(vat_included($order->total)) }}</span>
                    </li>
                    @if ($order->payment_method)
                        <li class="confirmation-line confirmation-line-muted">
                            <span class="confirmation-line-label">Zahlungsart</span>
                            <span class="confirmation-line-value">{{ config('shop.payment_methods.'.$order->payment_method.'.label', $order->payment_method) }}</span>
                        </li>
                    @endif
                </ul>

                <div class="confirmation-address">
                    <strong>Lieferadresse</strong>
                    <p class="mb-0 mt-1">
                        {{ $order->first_name }} {{ $order->last_name }}<br>
                        {{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif<br>
                        {{ $order->postcode }} {{ $order->city }}<br>
                        {{ $order->country }}
                    </p>
                </div>

                <div class="confirmation-bank">
                    <h6 class="mb-2">Jetzt per Banküberweisung bezahlen</h6>
                    <p class="text-caption opacity-75 mb-3">Bitte überweisen Sie den Gesamtbetrag auf folgendes Konto. Der Versand erfolgt nach Zahlungseingang.</p>
                    <p class="mb-1 text-caption">Kontoinhaber: <strong>{{ $bank['account_holder'] ?: '—' }}</strong></p>
                    @if ($bank['bank_name'])
                        <p class="mb-1 text-caption">Bank: {{ $bank['bank_name'] }}</p>
                    @endif
                    <p class="mb-1 text-caption confirmation-break">IBAN: <strong>{{ format_iban($bank['iban']) ?: '—' }}</strong></p>
                    @if ($bank['bic'])
                        <p class="mb-1 text-caption">BIC: {{ $bank['bic'] }}</p>
                    @endif
                    <p class="mb-1 text-caption">Betrag: <strong>{{ swiss_money((float) $order->total) }}</strong></p>
                    <p class="mb-0 text-caption">Verwendungszweck: <strong>{{ $order->reference }}</strong></p>
                    @if ($bank['note'])
                        <p class="mb-0 mt-2 text-caption opacity-75">{{ $bank['note'] }}</p>
                    @endif
                </div>

                <div class="confirmation-proof" lang="de">
                    <h6 class="mb-2">Zahlungsnachweis senden</h6>
                    <p class="text-caption opacity-75 mb-3">Laden Sie nach der Überweisung Ihren Beleg hoch (Screenshot oder PDF). Wir erhalten ihn per E-Mail.</p>

                    @if (session('status'))
                        <div class="confirmation-alert">{{ session('status') }}</div>
                    @endif

                    @if ($order->payment_proof_path)
                        <p class="mb-3 text-caption"><strong>Status:</strong> Zahlungsnachweis wurde bereits gesendet. Sie können bei Bedarf eine neue Datei nachreichen.</p>
                    @endif

                    <form action="{{ route('checkout.payment-proof', $order->reference) }}" method="POST" enctype="multipart/form-data" class="confirmation-proof-form">
                        @csrf
                        <span class="form-label fw-semibold d-block" id="payment_proof_label">Zahlungsnachweis hochladen *</span>
                        <label class="payment-proof-picker @error('payment_proof') is-invalid @enderror" for="payment_proof" aria-labelledby="payment_proof_label">
                            <input type="file" name="payment_proof" id="payment_proof" class="payment-proof-input" accept=".jpg,.jpeg,.png,.pdf,.webp,image/*,application/pdf" required>
                            <span class="payment-proof-btn" aria-hidden="true">Datei auswählen</span>
                            <span class="payment-proof-name" id="payment_proof_name">Keine Datei ausgewählt</span>
                        </label>
                        <p class="text-caption opacity-75 mt-1 mb-0">JPG, PNG, WEBP oder PDF, max. 8 MB.</p>
                        @error('payment_proof')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="tf-btn btn-fill animate-btn w-100 mt-3"><span>Senden</span></button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('shop.index') }}" class="tf-btn btn-fill animate-btn confirmation-cta"><span>Zurück zum Shop</span></a>
            </div>
        </div>
    </section>

    <style>
        .confirmation-page {
            max-width: 720px;
            padding-left: 16px;
            padding-right: 16px;
        }
        .confirmation-heading {
            font-size: clamp(1.05rem, 4.2vw, 1.5rem);
            line-height: 1.35;
            word-break: break-word;
        }
        .confirmation-lead {
            font-size: 0.95rem;
            line-height: 1.45;
        }
        .confirmation-break {
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .confirmation-card {
            border: 1px solid #ece7e1;
            border-radius: 12px;
            padding: 16px;
            background: #fff;
        }
        .confirmation-lines {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .confirmation-line {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .confirmation-line-label {
            flex: 1 1 auto;
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .confirmation-line-value {
            flex: 0 0 auto;
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .confirmation-line-total {
            font-weight: 600;
            padding-top: 8px;
            border-top: 1px solid #ece7e1;
        }
        .confirmation-line-muted {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        .confirmation-address,
        .confirmation-bank,
        .confirmation-proof {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #ece7e1;
            font-size: 0.95rem;
            line-height: 1.45;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .confirmation-alert {
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 0.9rem;
            background: #e8f5e9;
            color: #1b5e20;
        }
        .payment-proof-picker {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #d5c9b8;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            margin: 0;
        }
        .payment-proof-picker.is-invalid { border-color: #dc3545; }
        .payment-proof-input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .payment-proof-btn {
            border: 0;
            border-radius: 6px;
            padding: 8px 14px;
            background: #1c140f;
            color: #fff;
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            pointer-events: none;
        }
        .payment-proof-name {
            font-size: 14px;
            color: #6b6156;
            word-break: break-all;
            pointer-events: none;
        }
        .confirmation-cta {
            width: 100%;
            max-width: 320px;
        }
        @media (min-width: 576px) {
            .confirmation-card {
                padding: 24px;
            }
            .confirmation-page {
                padding-left: 12px;
                padding-right: 12px;
            }
        }
        @media (max-width: 575.98px) {
            .tf-page-title-3 .heading {
                font-size: clamp(1.25rem, 6vw, 1.75rem) !important;
                padding-left: 12px;
                padding-right: 12px;
                line-height: 1.25;
            }
            .tf-page-title-3 .breadcrumbs {
                padding-left: 12px;
                padding-right: 12px;
                row-gap: 4px;
            }
        }
    </style>
@endsection

@push('scripts')
<script>
(function () {
    var input = document.getElementById('payment_proof');
    var nameEl = document.getElementById('payment_proof_name');
    if (!input || !nameEl) return;

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        nameEl.textContent = file ? file.name : 'Keine Datei ausgewählt';
    });
})();
</script>
@endpush
