<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

  'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Batasi metode yang diizinkan
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    // Batasi asal yang diizinkan
    'allowed_origins' => [
        'https://example.com', // Ganti dengan domain Anda
        'https://another-trusted-site.com', // Domain lain yang tepercaya
    ],

    'allowed_origins_patterns' => [],

    // Batasi header yang diizinkan
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Jika Anda perlu mendukung autentikasi dengan cookie, ubah ini menjadi true
    'supports_credentials' => false,

];
