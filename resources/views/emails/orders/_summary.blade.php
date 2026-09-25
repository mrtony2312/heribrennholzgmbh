{{-- $order erwartet (mit geladenen items) --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:8px 0 4px;">
  <tr>
    <th align="left" style="padding:10px 0;border-bottom:2px solid #1c140f;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Artikel</th>
    <th align="center" style="padding:10px 8px;border-bottom:2px solid #1c140f;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Menge</th>
    <th align="right" style="padding:10px 0;border-bottom:2px solid #1c140f;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Betrag</th>
  </tr>
  @foreach ($order->items as $item)
    <tr>
      <td style="padding:12px 0;border-bottom:1px solid #ece5da;font-size:14px;">
        {{ $item->name }}
        <div style="font-size:12px;color:#8a8178;">Einzelpreis: {{ $order->money($item->price) }}</div>
      </td>
      <td align="center" style="padding:12px 8px;border-bottom:1px solid #ece5da;font-size:14px;">{{ $item->quantity }}</td>
      <td align="right" style="padding:12px 0;border-bottom:1px solid #ece5da;font-size:14px;white-space:nowrap;">{{ $order->money($item->line_total) }}</td>
    </tr>
  @endforeach
  <tr>
    <td colspan="2" align="right" style="padding:12px 8px 4px;font-size:13px;color:#5b544c;">Zwischensumme</td>
    <td align="right" style="padding:12px 0 4px;font-size:13px;white-space:nowrap;">{{ $order->money($order->subtotal) }}</td>
  </tr>
  <tr>
    <td colspan="2" align="right" style="padding:4px 8px;font-size:13px;color:#5b544c;">Versand</td>
    <td align="right" style="padding:4px 0;font-size:13px;white-space:nowrap;">{{ (float) $order->shipping > 0 ? $order->money($order->shipping) : 'Kostenlos' }}</td>
  </tr>
  <tr>
    <td colspan="2" align="right" style="padding:12px 8px;border-top:2px solid #1c140f;font-size:16px;font-weight:700;">Gesamtbetrag</td>
    <td align="right" style="padding:12px 0;border-top:2px solid #1c140f;font-size:16px;font-weight:700;white-space:nowrap;">{{ $order->money($order->total) }}</td>
  </tr>
</table>
