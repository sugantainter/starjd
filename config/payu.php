<?php

return [
    'merchant_key' => env('PAYU_MERCHANT_KEY', ''),
    'salt' => env('PAYU_SALT', ''),
    'test_mode' => env('PAYU_TEST_MODE', true),
    'base_url' => env('PAYU_BASE_URL', 'https://test.payu.in/_payment'),
    'surl' => env('APP_URL', '') . '/payment/callback/success',
    'furl' => env('APP_URL', '') . '/payment/callback/failure',
];
