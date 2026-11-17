@php $brand = config('app.name', 'Heri Brennholz'); @endphp
@component('emails.layout', ['subject' => 'Zahlungsnachweis ' . $order->reference, 'preheader' => 'Zahlungsnachweis für Bestellung ' . $order->reference . ' eingegangen'])

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#1c140f;border-radius:10px;margin-bottom:22px;">
  <tr>
    <td style="padding:16px 20px;color:#ffffff;">
      <div style="font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#c9bfb4;">Zahlungsnachweis eingegangen</div>
      <div style="font-size:24px;font-weight:700;margin-top:2px;">{{ $order->reference }} — {{ $order->money($order->total) }}</div>
      <div style="font-size:13px;margin-top:6px;color:#c9bfb4;">Der Nachweis ist als Anhang dieser E-Mail beigefügt.</div>
    </td>
  </tr>
</table>

@include('emails.orders._details')

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
