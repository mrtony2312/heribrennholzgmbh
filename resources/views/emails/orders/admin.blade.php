@php $brand = config('app.name', 'Heri Brennholz'); @endphp
@component('emails.layout', ['subject' => 'Neue Bestellung ' . $order->reference, 'preheader' => 'Neue Bestellung über ' . $order->money($order->total) . ' von ' . $order->full_name])

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#1c140f;border-radius:10px;margin-bottom:22px;">
  <tr>
    <td style="padding:16px 20px;color:#ffffff;">
      <div style="font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#c9bfb4;">Neue Bestellung eingegangen</div>
      <div style="font-size:24px;font-weight:700;margin-top:2px;">{{ $order->reference }} — {{ $order->money($order->total) }}</div>
    </td>
  </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.7;margin-bottom:20px;">
  <tr>
    <td width="50%" valign="top" style="padding-right:12px;">
      <h2 style="margin:0 0 4px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Kunde</h2>
      <strong>{{ $order->full_name }}</strong><br>
      <a href="mailto:{{ $order->email }}" style="color:#1c140f;">{{ $order->email }}</a><br>
      @if ($order->phone)<a href="tel:{{ $order->phone }}" style="color:#1c140f;">{{ $order->phone }}</a><br>@endif
    </td>
    <td width="50%" valign="top" style="padding-left:12px;">
      <h2 style="margin:0 0 4px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Lieferadresse</h2>
      {{ $order->address }}<br>
      @if ($order->address_2){{ $order->address_2 }}<br>@endif
      {{ $order->postcode }} {{ $order->city }}<br>
      {{ $order->country }}
    </td>
  </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf7f2;border:1px solid #ece5da;border-radius:8px;margin-bottom:16px;">
  <tr>
    <td style="padding:12px 16px;font-size:13px;">
      <strong>Eingang:</strong> {{ $order->created_at->format('d.m.Y H:i') }} Uhr &nbsp;·&nbsp;
      <strong>Status:</strong> {{ $order->statusLabel() }} &nbsp;·&nbsp;
      <strong>Positionen:</strong> {{ $order->items->count() }} &nbsp;·&nbsp;
      <strong>Artikel gesamt:</strong> {{ $order->items->sum('quantity') }}
    </td>
  </tr>
</table>

@include('emails.orders._summary')

@if ($order->notes)
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;background:#fff8e6;border:1px solid #f0e2b8;border-radius:8px;">
    <tr><td style="padding:12px 16px;font-size:14px;"><strong>Anmerkung des Kunden:</strong><br>{{ $order->notes }}</td></tr>
  </table>
@endif

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:26px;">
  <tr>
    <td align="center">
      <a href="{{ route('checkout.confirmation', $order->reference) }}"
         style="display:inline-block;background:#1c140f;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:13px 28px;border-radius:8px;">
        Bestelldetails öffnen
      </a>
    </td>
  </tr>
</table>

@endcomponent
