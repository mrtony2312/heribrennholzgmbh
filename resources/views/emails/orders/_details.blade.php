{{-- Vollständige Kundendaten + Zahlung für Admin- und Kundenmails --}}
@php
    $bank = bank_account();
    $paymentLabel = config('shop.payment_methods.'.$order->payment_method.'.label', $order->payment_method);
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;font-size:14px;line-height:1.7;">
  <tr>
    <td width="50%" valign="top" style="padding-right:12px;">
      <h2 style="margin:0 0 6px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Kundendaten</h2>
      <strong>Vorname:</strong> {{ $order->first_name ?: '—' }}<br>
      <strong>Nachname:</strong> {{ $order->last_name ?: '—' }}<br>
      <strong>E-Mail:</strong> <a href="mailto:{{ $order->email }}" style="color:#1c140f;">{{ $order->email ?: '—' }}</a><br>
      <strong>Telefon:</strong> {{ $order->phone ?: '—' }}
    </td>
    <td width="50%" valign="top" style="padding-left:12px;">
      <h2 style="margin:0 0 6px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Lieferadresse</h2>
      <strong>Adresse:</strong> {{ $order->address ?: '—' }}<br>
      @if ($order->address_2)<strong>Adresszusatz:</strong> {{ $order->address_2 }}<br>@endif
      <strong>PLZ / Ort:</strong> {{ trim(($order->postcode ?? '').' '.($order->city ?? '')) ?: '—' }}<br>
      <strong>Land:</strong> {{ $order->country ?: 'Schweiz' }}
    </td>
  </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;background:#f0f7f1;border:1px solid #cfe3d3;border-radius:8px;">
  <tr>
    <td style="padding:14px 16px;font-size:14px;line-height:1.7;">
      <h2 style="margin:0 0 8px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Zahlung</h2>
      <strong>Zahlungsart:</strong> {{ $paymentLabel }}<br>
      <strong>Gesamtbetrag:</strong> {{ $order->money($order->total) }}<br>
      <strong>Kontoinhaber:</strong> {{ $bank['account_holder'] ?: '—' }}<br>
      @if ($bank['bank_name'])<strong>Bank:</strong> {{ $bank['bank_name'] }}<br>@endif
      <strong>IBAN:</strong> {{ format_iban($bank['iban']) ?: '—' }}<br>
      @if ($bank['bic'])<strong>BIC:</strong> {{ $bank['bic'] }}<br>@endif
      <strong>Verwendungszweck:</strong> {{ $order->reference }}
      @if ($bank['note'])
        <br><span style="color:#5b544c;">{{ $bank['note'] }}</span>
      @endif
    </td>
  </tr>
</table>

@if ($order->notes)
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;background:#fff8e6;border:1px solid #f0e2b8;border-radius:8px;">
    <tr>
      <td style="padding:12px 16px;font-size:14px;">
        <strong>Anmerkung:</strong><br>{{ $order->notes }}
      </td>
    </tr>
  </table>
@endif
