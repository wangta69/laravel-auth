<?php
use Pondol\Common\Facades\JsonKeyValue;

if (!function_exists('auth_theme')) {
  function auth_theme($theme){
    $template = JsonKeyValue::getAsJson('auth');
    switch($theme) {
      case 'mail':
        return 'auth.templates.mail.'.$template->template->mail;
      default:
        return 'auth.templates.views.'.$template->template->user;
    }
  }
}