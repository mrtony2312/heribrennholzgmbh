<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin-Anmeldung – {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: Georgia, "Times New Roman", serif; background: linear-gradient(160deg, #f4efe6 0%, #e8dfd0 45%, #d9cfc0 100%); color: #1c140f; }
        .card { width: min(420px, 92vw); background: rgba(255,252,247,.96); border: 1px solid #ddd2c2; border-radius: 14px; padding: 28px 26px; box-shadow: 0 18px 40px rgba(40,28,16,.08); }
        h1 { margin: 0 0 6px; font-size: 1.45rem; }
        p { margin: 0 0 18px; color: #6b6156; font-size: .95rem; }
        label { display: block; font-size: .85rem; margin-bottom: 6px; color: #4d453c; }
        input[type=email], input[type=password] { width: 100%; padding: 11px 12px; border: 1px solid #d5c9b8; border-radius: 8px; font: inherit; margin-bottom: 14px; background: #fff; }
        .row { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; font-size: .9rem; }
        button { width: 100%; border: 0; border-radius: 8px; padding: 12px 14px; background: #1c140f; color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        .alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 14px; font-size: .9rem; }
        .alert-error { background: #fdecea; color: #8a1f11; }
        .alert-ok { background: #e8f5e9; color: #1b5e20; }
        .errors { color: #8a1f11; font-size: .85rem; margin: -8px 0 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Bankkonto-Verwaltung</h1>
        <p>Nur für Administratoren. Hier können Sie die Bankdaten der Kasse ändern.</p>

        @if (session('status'))
            <div class="alert alert-ok">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label for="email">E-Mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="errors">{{ $message }}</div> @enderror

            <label for="password">Passwort</label>
            <input id="password" type="password" name="password" required>
            @error('password') <div class="errors">{{ $message }}</div> @enderror

            <div class="row">
                <input type="checkbox" name="remember" id="remember" value="1">
                <label for="remember" style="margin:0;">Angemeldet bleiben</label>
            </div>

            <button type="submit">Anmelden</button>
        </form>
    </div>
</body>
</html>
