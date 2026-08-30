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

    'location' => [
        'testing_enabled'      => env('LOCATION_TESTING_ENABLED', false),
        'testing_country_code' => env('LOCATION_TESTING_COUNTRY_CODE', ''),
    ],

    'paymob' => [
        // Master kill switch, read from this one place. When false, checkout
        // shows a maintenance message and creates NO order row
        // (PurchaseController::initiatePayment(), Batch 5).
        'enabled' => env('PAYMOB_ENABLED', false),

        'base_url' => env('PAYMOB_BASE_URL'),

        'secret_key'  => env('PAYMOB_SECRET_KEY'),
        'public_key'  => env('PAYMOB_PUBLIC_KEY'),
        'hmac_secret' => env('PAYMOB_HMAC_SECRET'),

        // Integration IDs keyed by payment method. Only 'card' is wired up in
        // Batch 5; 'wallet' is configured now so this array never needs
        // reshaping later.
        'integrations' => [
            'card'   => env('PAYMOB_INTEGRATION_ID_CARD'),
            'wallet' => env('PAYMOB_INTEGRATION_ID_WALLET'),
        ],

        // This merchant account is EGP-only (see docs/paymob-migration-audit.md, D1).
        'charge_currency' => env('PAYMOB_CHARGE_CURRENCY', 'EGP'),

        'http_timeout' => (int) env('PAYMOB_HTTP_TIMEOUT', 30),

        // TEMPORARY (Batch 6 live sandbox test only) — logs the full raw
        // webhook payload (hmac/card-pan redacted) so the real shape can be
        // captured. Defaults OFF. Remove alongside the logging block in
        // PaymobWebhookController once the sandbox capture is done.
        'log_raw_webhook_payload' => env('PAYMOB_LOG_RAW_WEBHOOK_PAYLOAD', false),
    ],

    'fx' => [
        // Which FxRateProvider implementation fx:refresh uses. Switching is
        // a config change (App\Services\Fx\FxProviderResolver), not a
        // rewrite. Valid values: 'er_api', 'currency_api'.
        'primary' => env('FX_PROVIDER_PRIMARY', 'er_api'),
        'fallback' => env('FX_PROVIDER_FALLBACK', 'currency_api'),
        'http_timeout' => (int) env('FX_HTTP_TIMEOUT', 5),
    ],

];
