<?php

function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', 'XXX'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', 'XXX'),
        'redirect_uri' => 'http://localhost:8000/api/auth/callback',
    ],
];
