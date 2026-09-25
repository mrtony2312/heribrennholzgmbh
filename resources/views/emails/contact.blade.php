@component('emails.layout', ['subject' => 'Kontaktformular: ' . ($contactSubject ?: 'Anfrage'), 'preheader' => 'Neue Nachricht von ' . $name . ' über das Kontaktformular'])

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#1c140f;border-radius:10px;margin-bottom:22px;">
  <tr>
    <td style="padding:16px 20px;color:#ffffff;">
      <div style="font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#c9bfb4;">Nachricht über das Kontaktformular</div>
      <div style="font-size:20px;font-weight:700;margin-top:2px;">{{ $contactSubject ?: 'Anfrage' }}</div>
    </td>
  </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.7;margin-bottom:20px;">
  <tr>
    <td valign="top">
      <h2 style="margin:0 0 4px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#8a8178;">Absender</h2>
      <strong>{{ $name }}</strong><br>
      <a href="mailto:{{ $email }}" style="color:#1c140f;">{{ $email }}</a>
    </td>
  </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf7f2;border:1px solid #ece5da;border-radius:8px;">
  <tr>
    <td style="padding:16px 20px;font-size:14px;line-height:1.7;white-space:pre-line;">{{ $body }}</td>
  </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:26px;">
  <tr>
    <td align="center">
      <a href="mailto:{{ $email }}"
         style="display:inline-block;background:#1c140f;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:13px 28px;border-radius:8px;">
        Direkt antworten
      </a>
    </td>
  </tr>
</table>

<p style="margin-top:22px;font-size:12px;color:#8a8178;">Diese Nachricht wurde über das Kontaktformular auf der Webseite gesendet. Ein Klick auf "Direkt antworten" adressiert die Antwort automatisch an {{ $email }}.</p>

@endcomponent
