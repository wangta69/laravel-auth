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
    'redirect_after_login' => ['administrator' => '/admin', 'manager' => '/', 'user' => '/'],
    'default_redirect_path' => '/home',

    /*
  |--------------------------------------------------------------------------
  | Point System Settings (범용 포인트 설정)
  |--------------------------------------------------------------------------
  */
    'point' => [
        'default_type' => 0,    // 기본 포인트 타입
        'free_type' => 0,    // 무상/이벤트 포인트 식별자
        'paid_type' => 1,    // 유상/충전 포인트 식별자
        'earning_type' => 2,    // 마스터 수익/정산 포인트 식별자 (길라 사주인 등에서 활용)

        'initial_register_point' => 0, // 회원가입 시 지급 포인트
        'daily_login_point' => 0, // 일일 로그인 지급 포인트
    ],
];
