<?php

return [
    'merchant_key' => env('PAYU_MERCHANT_KEY', ''),
    'salt' => env('PAYU_SALT', ''),
    'test_mode' => env('PAYU_TEST_MODE', true),
    'base_url' => env('PAYU_BASE_URL', 'https://test.payu.in/_payment'),
    // endpoint for transaction/status verification (merchant post service)
    'status_url' => env('PAYU_STATUS_URL', 'https://secure.payu.in/merchant/postservice.php?form=2'),
    'surl' => env('APP_URL', '') . '/payment/callback/success',
    'furl' => env('APP_URL', '') . '/payment/callback/failure',
];
