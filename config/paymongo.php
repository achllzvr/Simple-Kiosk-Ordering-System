<?php

return [
    'enabled' => filter_var(env('PAYMONGO_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'secret_key' => env('PAYMONGO_SECRET_KEY', ''),
    'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET', ''),
    'api_base' => rtrim(env('PAYMONGO_API_BASE', 'https://api.paymongo.com'), '/'),
    'payment_method_types' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PAYMONGO_PAYMENT_METHOD_TYPES', 'card,gcash,paymaya,qrph'))
    ))),
];
