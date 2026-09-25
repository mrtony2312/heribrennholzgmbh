@php $brand = config('app.name', 'Heri Brennholz'); @endphp
@component('emails.layout', ['subject' => 'Bestellbestätigung ' . $order->reference, 'preheader' => 'Wir haben Ihre Bestellung ' . $order->reference . ' erhalten.'])

<h1 style="margin:0 0 6px;font-size:22px;">Vielen Dank für Ihre Bestellung!</h1>
<p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#5b544c;">
  Hallo {{ $order->first_name }}, wir haben Ihre Bestellung erhalten und bearbeiten sie schnellstmöglich.
  Sobald die Ware versandt wird, erhalten Sie eine weitere Nachricht.
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf7f2;border:1px solid #ece5da;border-radius:10px;margin-bottom:24px;">
  <tr>
    <td style="padding:16px 20px;font-size:14px;">
      <strong>Bestellnummer:</strong> {{ $order->reference }}<br>
      <strong>Datum:</strong> {{ $order->created_at->format('d.m.Y H:i') }} Uhr<br>
      <strong>Status:</strong> {{ $order->statusLabel() }}
    </td>
  </tr>
</table>

<h2 style="margin:0 0 4px;font-size:15px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Bestellübersicht</h2>
@include('emails.orders._summary')

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
  <tr>
    <td width="50%" valign="top" style="padding-right:12px;font-size:14px;line-height:1.6;">
      <h2 style="margin:0 0 6px;font-size:15px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Lieferadresse</h2>
      {{ $order->full_name }}<br>
      {{ $order->address }}<br>
      @if ($order->address_2){{ $order->address_2 }}<br>@endif
      {{ $order->postcode }} {{ $order->city }}<br>
      {{ $order->country }}
    </td>
    <td width="50%" valign="top" style="padding-left:12px;font-size:14px;line-height:1.6;">
      <h2 style="margin:0 0 6px;font-size:15px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Kontakt</h2>
      {{ $order->email }}<br>
      @if ($order->phone){{ $order->phone }}<br>@endif
    </td>
  </tr>
</table>

@if ($order->notes)
  <p style="margin:20px 0 0;font-size:14px;"><strong>Ihre Anmerkung:</strong><br>{{ $order->notes }}</p>
@endif

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
  <tr>
    <td align="center">
      <a href="{{ route('checkout.confirmation', $order->reference) }}"
         style="display:inline-block;background:#1c140f;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:13px 28px;border-radius:8px;">
        Bestellung online ansehen
      </a>
    </td>
  </tr>
</table>

<p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#8a8178;">
  Lieferung in 1 bis 2 Werktagen, kostenlos in der ganzen Schweiz.
  Bei Fragen antworten Sie einfach auf diese E-Mail oder nennen Sie uns Ihre Bestellnummer {{ $order->reference }}.
</p>

@endcomponent
