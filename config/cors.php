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

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Domain production Vercel kamu. Tambahin domain lain di sini kalau ada
    // (custom domain, dst).
    'allowed_origins' => [
        'https://cafe-frontend-umber.vercel.app',
    ],

    // Biar semua preview deployment Vercel (*-itci, *-git-branch, dst) juga
    // otomatis kena izin, tanpa perlu update allowed_origins tiap deploy baru.
    'allowed_origins_patterns' => [
        '#^https://cafe-frontend.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Tetap false karena kamu pakai Bearer token di localStorage, bukan cookie.
    'supports_credentials' => false,

];