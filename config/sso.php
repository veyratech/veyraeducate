<?php

return [
    'secret_key' => env('SSO_SECRET_KEY', env('APP_ENV') === 'production' ? 'd65585e5da280c4f036b8f1b6e9ff6cfe4c061b59515c379b6842f7023f21ebb9a1b2c3d4e5f60718293a4b5c6d7e8f9' : 'd65585e5da280c4f036b8f1b6e9ff6cfe4c061b59515c379b6842f7023f21ebb'),
    'cbt_url' => rtrim(env('SSO_CBT_URL', env('APP_ENV') === 'production' ? 'https://cbt.veyracreative.site' : 'http://localhost/cbtonline'), '/'),
    'allowed_hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env('SSO_ALLOWED_HOSTS', ''))))),
    'token_ttl' => (int) env('SSO_TOKEN_TTL', 300),
];
