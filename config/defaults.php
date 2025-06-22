<?php

return [
    'app_name' => env('APP_NAME', 'Your App'),
    'country' => env('DEFAULT_COUNTRY', 'MY'),
    'state' => env('DEFAULT_STATE', 'Kuala Lumpur'),
    'timezone' => env('ADMIN_TIMEZONE', 'Asia/Kuala_Lumpur'),
    'currency' => env('DEFAULT_CURRENCY', 'MYR'),
    'currency_display' => env('DEFAULT_CURRENCY_DISPLAY', 'RM'),
    
    'api' => [
        'base_uri' => env('API_BASE_URI'),
        'client_id' => env('API_CLIENT_ID'),
        'client_secret' => env('API_CLIENT_SECRET'),
    ],
]; 