<?php
return [
  'activate'=>'auto',
  'template'=>['user'=>'default','mail'=>'default'],
  'roles' => [
    'default_role' => 'user'
  ],
  'point' => [
    'register' => 0,
    'login' => 0,
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
  ]
];
