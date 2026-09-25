<?php

return [

    // Schweizer Mehrwertsteuer-Normalsatz (seit 1.1.2024: 8.1 %). Brennholz, Pellets, Briketts
    // und Öfen unterliegen dem Normalsatz. Alle Shop-Preise sind Bruttopreise (inkl. MWST).
    'vat_rate' => (float) env('SHOP_VAT_RATE', 8.1),

    // Zahlungsarten. NUR aktivieren, was tatsächlich abgewickelt werden kann.
    // TWINT, Kreditkarte und PostFinance setzen einen Vertrag mit einem Zahlungsanbieter
    // (PSP) und eine technische Anbindung voraus – erst dann auf true setzen.
    'payment_methods' => [
        'rechnung' => ['label' => 'Kauf auf Rechnung (QR-Rechnung)', 'enabled' => true],
        'vorkasse' => ['label' => 'Vorkasse (Banküberweisung)', 'enabled' => true],
        'twint' => ['label' => 'TWINT', 'enabled' => (bool) env('SHOP_PAY_TWINT', false)],
        'karte' => ['label' => 'Kreditkarte (Visa, Mastercard)', 'enabled' => (bool) env('SHOP_PAY_CARD', false)],
        'postfinance' => ['label' => 'PostFinance', 'enabled' => (bool) env('SHOP_PAY_POSTFINANCE', false)],
    ],
];
