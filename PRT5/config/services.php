<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Admin API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the Admin API backend.
    | - api.base_url: Used by PHP/server-side code (inside Docker uses host.docker.internal)
    | - api.public_url: Used by JavaScript/browser code (uses localhost)
    | - storefront.url: Base URL for the storefront (for images/assets)
    |
    */

    'api' => [
        'base_url' => env('API_BASE_URL', 'http://localhost:8300/api/v1'),
        'public_url' => env('API_PUBLIC_URL', 'http://localhost:8300/api/v1'),
        'timeout' => env('API_TIMEOUT', 5),
    ],

    'storefront' => [
        'url' => env('APP_URL', 'http://localhost:8300'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
