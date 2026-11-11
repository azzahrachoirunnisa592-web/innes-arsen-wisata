<?php

return [
    'package_categories' => [
        'Domestik',
        'Nusantara',
        'Study Tour',
        'Ziarah Wali Songo',
        'Corporate/Instansi',
    ],
    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrator'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
    'payment' => [
        'method_name' => env('PAYMENT_METHOD_NAME', 'QRIS / DANA'),
        'account_name' => env('PAYMENT_ACCOUNT_NAME', 'PT. Innes Arsen Wisata'),
        'account_number' => env('PAYMENT_ACCOUNT_NUMBER'),
        'simulation_enabled' => env('PAYMENT_SIMULATION_ENABLED', false),
    ],
];
