<?php
return [
  'roles' => [
    'default_role' => 'user'
  ],
  'route_auth'=>[
    'prefix'=>'',
    'as'=>'',
    'middleware'=>['web'],
  ],
  'route_auth_admin'=>[
    'prefix'=>'auth/admin',
    'as'=>'auth.admin.',
    'middleware'=>['web', 'admin'],
  ],
  'component' => ['admin'=>['layout'=>'pondol-common::common-admin', 'lnb'=>'pondol-auth::lnb-partial']],
];
