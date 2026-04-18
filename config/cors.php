<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Allows the four Wenza frontend origins (marketing, LMS, certificates,
    | scholarship) to call the API. All four localhost ports are allowed in
    | development; production origins come from env vars.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        env('APP_FRONTEND_URL', 'http://localhost:3001'),
        env('CERT_FRONTEND_URL', 'http://localhost:3002'),
        env('SCHOLARSHIP_FRONTEND_URL', 'http://localhost:3003'),
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
