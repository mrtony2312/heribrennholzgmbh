<?php

return [

    // Schweizer Mehrwertsteuer-Normalsatz (seit 1.1.2024: 8.1 %). Brennholz, Pellets, Briketts
    // und Öfen unterliegen dem Normalsatz. Alle Shop-Preise sind Bruttopreise (inkl. MWST).
    'vat_rate' => (float) env('SHOP_VAT_RATE', 8.1),

    // Zahlungsarten. NUR aktivieren, was tatsächlich abgewickelt werden kann.
    // Aktuell nur Banküberweisung (Vorkasse) mit Zahlungsnachweis beim Checkout.
    'payment_methods' => [
        'rechnung' => ['label' => 'Kauf auf Rechnung (QR-Rechnung)', 'enabled' => false],
        'vorkasse' => ['label' => 'Banküberweisung (Vorkasse)', 'enabled' => true],
        'twint' => ['label' => 'TWINT', 'enabled' => (bool) env('SHOP_PAY_TWINT', false)],
        'karte' => ['label' => 'Kreditkarte (Visa, Mastercard)', 'enabled' => (bool) env('SHOP_PAY_CARD', false)],
        'postfinance' => ['label' => 'PostFinance', 'enabled' => (bool) env('SHOP_PAY_POSTFINANCE', false)],
    ],

    // Standardwerte für das Bankkonto (werden in site_settings überschrieben).
    'bank' => [
        'account_holder' => env('SHOP_BANK_ACCOUNT_HOLDER', 'Heri Brennholz GmbH'),
        'bank_name' => env('SHOP_BANK_NAME', ''),
        'iban' => env('SHOP_BANK_IBAN', ''),
        'bic' => env('SHOP_BANK_BIC', ''),
        'note' => env('SHOP_BANK_NOTE', 'Bitte geben Sie Ihre Bestellnummer als Verwendungszweck an und laden Sie den Zahlungsnachweis hoch.'),
    ],

    // Geheimer Admin-Pfad nur für die Bankkonto-Verwaltung (ohne führenden Slash).
    'admin_secret_path' => env('ADMIN_SECRET_PATH', 'hb-bank-verwaltung-k7m4n9qx2p'),
];
