<?php

return [
    'http_timeout' => (int) env('MARKETS_HTTP_TIMEOUT', 10),

    'fx' => [
        'url' => env('MARKETS_FX_URL', 'https://api.frankfurter.dev/v2/rates'),
        'base' => env('MARKETS_BASE_CURRENCY', 'USD'),
        'currencies' => [
            'USD',
            'EUR',
            'GBP',
            'ILS',
            'JOD',
            'SAR',
            'AED',
            'EGP',
            'TRY',
            'KWD',
            'QAR',
            'JPY',
            'CHF',
            'CAD',
            'AUD',
            'CNY',
        ],
        'cache_seconds' => (int) env('MARKETS_FX_CACHE_SECONDS', 21600),
    ],

    'metals' => [
        'url' => env('MARKETS_METALS_URL', 'https://api.gold-api.com'),
        'symbols' => ['XAU', 'XAG'],
        'display_currencies' => ['USD', 'ILS', 'JOD', 'EUR'],
        'cache_seconds' => (int) env('MARKETS_METALS_CACHE_SECONDS', 300),
    ],

    'gold_karats' => [
        24 => 1.0,
        22 => 22 / 24,
        21 => 21 / 24,
        18 => 18 / 24,
    ],

    'troy_ounce_grams' => 31.1034768,
];