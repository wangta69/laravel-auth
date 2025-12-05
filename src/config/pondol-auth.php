<?php

return [
    'roles' => [
        'default_role' => 'user',
    ],
    'route_auth' => [
        'prefix' => '',
        'as' => '',
        'middleware' => ['web'],
    ],
    'route_auth_admin' => [
        'prefix' => 'auth/admin',
        'as' => 'auth.admin.',
        'middleware' => ['web', 'admin'],
    ],
    'component' => ['admin' => ['layout' => 'pondol-common::common-admin', 'lnb' => 'pondol-auth::lnb-partial']],
    'route_after_social_link' => 'home',
    'redirect_after_login' => ['administrator' => '/admin', 'manager' => '/', 'user' => '/'],
    'default_redirect_path' => '/home',
];
