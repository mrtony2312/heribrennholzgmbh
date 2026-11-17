<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Bankkonto bearbeiten – {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Georgia, "Times New Roman", serif; background: linear-gradient(160deg, #f4efe6 0%, #e8dfd0 45%, #d9cfc0 100%); color: #1c140f; }
        .wrap { width: min(640px, 94vw); margin: 40px auto; background: rgba(255,252,247,.96); border: 1px solid #ddd2c2; border-radius: 14px; padding: 28px 26px; box-shadow: 0 18px 40px rgba(40,28,16,.08); }
        header { display: flex; justify-content: space-between; gap: 12px; align-items: start; margin-bottom: 18px; }
        h1 { margin: 0 0 4px; font-size: 1.4rem; }
        .sub { margin: 0; color: #6b6156; font-size: .92rem; }
        label { display: block; font-size: .85rem; margin: 12px 0 6px; color: #4d453c; }
        input, textarea { width: 100%; padding: 11px 12px; border: 1px solid #d5c9b8; border-radius: 8px; font: inherit; background: #fff; }
        textarea { min-height: 90px; resize: vertical; }
        .actions { display: flex; gap: 10px; margin-top: 18px; flex-wrap: wrap; }
        button, .btn { border: 0; border-radius: 8px; padding: 11px 16px; font: inherit; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        button { background: #1c140f; color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #cbbdaa; color: #1c140f; }
        .alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 14px; font-size: .9rem; background: #e8f5e9; color: #1b5e20; }
        .errors { color: #8a1f11; font-size: .85rem; margin-top: 4px; }
        .hint { font-size: .82rem; color: #7a7166; margin-top: 4px; }
        .preview { margin-top: 22px; padding: 14px 16px; border-radius: 10px; background: #f7f2ea; border: 1px solid #e4dacb; font-size: .92rem; }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <div>
                <h1>Bankkonto der Kasse</h1>
                <p class="sub">Nur diese Angaben werden auf der Bestellseite angezeigt. Keine anderen Shop-Einstellungen.</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn-ghost">Abmelden</button>
            </form>
        </header>

        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.bank.update') }}">
            @csrf
            @method('PUT')

            <label for="account_holder">Kontoinhaber *</label>
            <input id="account_holder" name="account_holder" value="{{ old('account_holder', $bank['account_holder']) }}" required>
            @error('account_holder') <div class="errors">{{ $message }}</div> @enderror

            <label for="bank_name">Bankname</label>
            <input id="bank_name" name="bank_name" value="{{ old('bank_name', $bank['bank_name']) }}">
            @error('bank_name') <div class="errors">{{ $message }}</div> @enderror

            <label for="iban">IBAN *</label>
            <input id="iban" name="iban" value="{{ old('iban', $bank['iban']) }}" required placeholder="CH00 0000 0000 0000 0000 0">
            @error('iban') <div class="errors">{{ $message }}</div> @enderror

            <label for="bic">BIC / SWIFT</label>
            <input id="bic" name="bic" value="{{ old('bic', $bank['bic']) }}">
            @error('bic') <div class="errors">{{ $message }}</div> @enderror

            <label for="note">Hinweis für Kunden</label>
            <textarea id="note" name="note">{{ old('note', $bank['note']) }}</textarea>
            <div class="hint">Wird unter den Bankdaten auf der Kasse angezeigt.</div>
            @error('note') <div class="errors">{{ $message }}</div> @enderror

            <div class="actions">
                <button type="submit">Bankkonto speichern</button>
                <a class="btn btn-ghost" href="{{ route('checkout.index') }}" target="_blank" rel="noopener">Kasse ansehen</a>
            </div>
        </form>

        <div class="preview">
            <strong>Aktuelle Vorschau</strong><br>
            {{ $bank['account_holder'] ?: '—' }}<br>
            @if ($bank['bank_name']){{ $bank['bank_name'] }}<br>@endif
            IBAN: {{ format_iban($bank['iban']) ?: '—' }}
            @if ($bank['bic']) · BIC: {{ $bank['bic'] }}@endif
        </div>
    </div>
</body>
</html>
