<?php

return [
    'guard' => 'web',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,
    'home' => '/dashboard',
    'prefix' => trim((string) env('BASE_PATH', ''), '/'),
    'domain' => null,
    'middleware' => ['web'],
    'limiters' => [
        'login' => 'login',
    ],
    'views' => true,
    'features' => [
        // Only login is enabled — registration, password reset, email verification, and 2FA are disabled
    ],
];
