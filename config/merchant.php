<?php

/**
 * Google Merchant Center + storefront source of truth for NAP, shipping,
 * returns, currency and API credentials. Feed / JSON-LD should stay aligned
 * with config/feed.php (CH market).
 */

return [

    'feed_token' => env('MERCHANT_FEED_TOKEN', ''),
    'currency' => env('MERCHANT_CURRENCY', 'CHF'),
    'target_country' => env('MERCHANT_TARGET_COUNTRY', 'CH'),
    'content_language' => env('MERCHANT_CONTENT_LANGUAGE', env('FEED_CONTENT_LANGUAGE', 'de')),
    'feed_label' => env('MERCHANT_FEED_LABEL', env('MERCHANT_TARGET_COUNTRY', 'CH')),
    'default_brand' => env('MERCHANT_DEFAULT_BRAND', 'Heri Brennholz'),
    'feed_id_prefix' => 'hb-',

    'reference_prices_verified' => (bool) env('MERCHANT_REFERENCE_PRICES_VERIFIED', false),

    /*
     | Merchant API (productInputs.insert).
     | Auth like Naturalenha / Casacuberta: OAuth (GOOGLE_CLIENT_*) OR service-account JSON.
     | https://developers.google.com/merchant/api/guides/quickstart
     */
    'api' => [
        'enabled' => (bool) env('MERCHANT_API_ENABLED', false),
        'account_id' => env('MERCHANT_ACCOUNT_ID', env('GOOGLE_MERCHANT_ACCOUNT_ID', '')),
        'data_source_id' => env('MERCHANT_DATA_SOURCE_ID', env('GOOGLE_MERCHANT_DATA_SOURCE_ID', '')),
        // Absolute path, or relative to storage/app/ (e.g. google/merchant-sa.json)
        'credentials' => env('MERCHANT_API_CREDENTIALS', env('GOOGLE_MERCHANT_CREDENTIALS', 'google/merchant-sa.json')),
        'timeout' => (int) env('MERCHANT_API_TIMEOUT', 30),
    ],

    'oauth' => [
        'client_id' => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN', ''),
    ],

    'nap' => [
        'legal_name' => 'Heri Brennholz GmbH',
        'vat_id' => 'CHE-228.719.493',
        'tax_id' => 'CHE-228.719.493',
        'email' => 'kontakt@heribrennholzgmbh.com',
        'telephone' => '+41786099516',
        'telephone_display' => '+41 78 609 95 16',
        'street' => 'Fiderholzstrasse 7',
        'postal_code' => '4562',
        'locality' => 'Biberist',
        'region' => 'Solothurn',
        'country' => 'CH',
        'country_name' => 'Schweiz',
        'address_line' => 'Fiderholzstrasse 7, 4562 Biberist, Schweiz',
    ],

    'shipping' => [
        'country' => 'CH',
        'service' => env('MERCHANT_SHIPPING_SERVICE', 'Standardversand'),
        'price' => env('MERCHANT_SHIPPING_PRICE', '0.00'),
        'min_days' => (int) env('MERCHANT_SHIPPING_MIN_DAYS', 1),
        'max_days' => (int) env('MERCHANT_SHIPPING_MAX_DAYS', 2),
        'handling_min' => (int) env('FEED_HANDLING_TIME_MIN', 1),
        'handling_max' => (int) env('FEED_HANDLING_TIME_MAX', 1),
        'transit_min' => (int) env('FEED_TRANSIT_TIME_MIN', 0),
        'transit_max' => (int) env('FEED_TRANSIT_TIME_MAX', 1),
        'zone_label' => 'gesamte Schweiz',
    ],

    'returns' => [
        'days' => (int) env('MERCHANT_RETURN_DAYS', 14),
        'customer_pays_return_shipping' => (bool) env('MERCHANT_CUSTOMER_PAYS_RETURN_SHIPPING', true),
        'return_shipping_cost' => env('MERCHANT_RETURN_SHIPPING_COST', '19.90'),
        'refund_days' => 14,
    ],
];
