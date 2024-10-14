<?php
return [
  'activate'=>'email',
  'template'=>['user'=>'default','mail'=>'default'],
  'roles' => [
    'default_role' => 'user'
  ],
  'point' => [
    'register' => 0,
    'login' => 0,
  ]
];
