<?php

return [

    'brand' => env('MERCHANT_DEFAULT_BRAND', env('FEED_BRAND', 'Heri Brennholz')),

    'known_brands' => [
        'Invicta', 'La Nordica', 'FreePoint', 'Sannover', 'Pellini', 'OLIMP', 'LAVA',
        'Ardenforest', 'BADGER', 'PIKS', 'HS Timber', 'Starforest', 'Arapellet',
        'Heizfuxx', 'MAGIC POLAR', 'Rochefort', 'ANVIL', 'Limouzi', 'Naturkraft', 'Dragon', 'HELIOS',
        'Crépito', 'Piveteau', 'Woodstock', 'TotalEnergies', 'SunFire', 'VALBOVAL', 'HOYER', 'EPH',
        'Westerwälder', 'Godin', 'MCZ', 'Austroflamm', 'Interstoves',
    ],

    // Marken, die nicht im Google-Feed erscheinen sollen (z. B. in der Schweiz nicht lieferbar).
    // Beispiel: ['Piveteau', 'Limouzi']. Leer = alle Marken im Feed.
    'excluded_brands' => array_filter(array_map('trim', explode(',', (string) env('FEED_EXCLUDED_BRANDS', '')))),

    'currency' => env('MERCHANT_CURRENCY', env('FEED_CURRENCY', 'CHF')),

    'condition' => 'new',

    'shipping_countries' => ['CH'],

    'shipping_service' => env('MERCHANT_SHIPPING_SERVICE', 'Standardversand (1–2 Werktage)'),

    'shipping_price' => (float) env('MERCHANT_SHIPPING_PRICE', 0),

    'shipping_min_days' => (int) env('MERCHANT_SHIPPING_MIN_DAYS', 1),

    'shipping_max_days' => (int) env('MERCHANT_SHIPPING_MAX_DAYS', 2),

    'google_product_category' => env('FEED_GOOGLE_CATEGORY', '6229'),

    'google_product_category_map' => [
        'holzpellets' => '6229',
        'scheitholz' => '6229',
        'holzbriketts' => '6229',
        'brennholz' => '6229',
        'kaminholz' => '6229',
        'pelletoefen' => '6431',
        'kaminoefen' => '6431',
        'kamineinsaetze' => '6431',
    ],

    'content_language' => env('MERCHANT_CONTENT_LANGUAGE', env('FEED_CONTENT_LANGUAGE', 'de')),

    'target_country' => env('MERCHANT_TARGET_COUNTRY', env('FEED_TARGET_COUNTRY', 'CH')),

    'feed_label' => env('MERCHANT_FEED_LABEL', env('MERCHANT_TARGET_COUNTRY', 'CH')),

    'shipping_handling_time_min' => (int) env('FEED_HANDLING_TIME_MIN', 1),
    'shipping_handling_time_max' => (int) env('FEED_HANDLING_TIME_MAX', 1),
    'shipping_transit_time_min' => (int) env('FEED_TRANSIT_TIME_MIN', 0),
    'shipping_transit_time_max' => (int) env('FEED_TRANSIT_TIME_MAX', 1),

    'return_days' => (int) env('MERCHANT_RETURN_DAYS', 14),

    'token' => env('MERCHANT_FEED_TOKEN', ''),

    'cache_ttl' => (int) env('FEED_CACHE_TTL', 900),

    'title' => env('FEED_TITLE', 'Heri Brennholz GmbH – Produktkatalog'),
    'description' => env(
        'FEED_DESCRIPTION',
        'Brennholz, Holzpellets und Holzbriketts. Kostenlose Lieferung in der Schweiz in 1 bis 2 Werktagen.'
    ),

    'company' => [
        'legal_name' => 'Heri Brennholz GmbH',
        'street' => 'Fiderholzstrasse 7',
        'postal_code' => '4562',
        'city' => 'Biberist',
        'country' => 'CH',
        'phone' => '+41 78 609 95 16',
        'email' => 'kontakt@heribrennholzgmbh.com',
        'uid' => 'CHE-228.719.493',
        'hr' => 'CH-241.4.020.905-9',
    ],
];
