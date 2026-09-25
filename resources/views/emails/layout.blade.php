@php $brand = config('app.name', 'Heri Brennholz'); @endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light">
<title>{{ $subject ?? $brand }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f1ec;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#2d2a26;">
<span style="display:none!important;visibility:hidden;opacity:0;height:0;width:0;overflow:hidden;">{{ $preheader ?? '' }}</span>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1ec;">
  <tr>
    <td align="center" style="padding:32px 16px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06);">

        <tr>
          <td style="background:#1c140f;padding:22px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="font-size:22px;font-weight:700;letter-spacing:.02em;color:#ffffff;">{{ $brand }}</td>
                <td align="right" style="font-size:12px;color:#c9bfb4;">Brennholz · Lieferung in der ganzen Schweiz</td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:32px;">
            {{ $slot }}
          </td>
        </tr>

        <tr>
          <td style="padding:24px 32px;background:#faf7f2;border-top:1px solid #ece5da;font-size:12px;line-height:1.6;color:#8a8178;">
            Heri Brennholz GmbH · Fiderholzstrasse 7 · 4562 Biberist, Schweiz<br>
            <a href="mailto:{{ config('mail.from.address') }}" style="color:#8a8178;">{{ config('mail.from.address') }}</a> ·
            <a href="{{ config('app.url') }}" style="color:#8a8178;">{{ str_replace(['https://','http://'], '', config('app.url')) }}</a><br>
            Diese E-Mail wurde automatisch versendet. Bitte antworten Sie bei Rückfragen direkt auf diese Nachricht.
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
