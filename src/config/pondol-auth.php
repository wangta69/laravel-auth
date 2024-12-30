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
  'component' => ['admin'=>['layout'=>'pondol-auth::admin', 'lnb'=>'pondol-auth::navigation']],
];
