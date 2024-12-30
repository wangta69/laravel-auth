<?php
namespace Pondol\Auth\Traits\Admin;
use Pondol\Common\Facades\JsonKeyValue;

trait Config
{
  public function getConfig()
  {
    $user = JsonKeyValue::getAsArray('auth');

    // auth
    $templates = [];
    $template_dir =  resource_path('views/auth/templates/views');
    $templates['user'] = array_map('basename',\File::directories($template_dir));

    $template_dir =  resource_path('views/auth/templates/mail');
    $templates['mail'] = array_map('basename',\File::directories($template_dir));

    return [
      'user'=>$user,
      'templates'=>$templates
    ];
  }
  
  public function _update($request) {
    $config = [
      'activate' => $request->activate, 
      'template'=>['user'=>$request->t_user, 'mail'=>$request->t_mail],
      'point'=>['register'=>$request->input('r_point', 0), 'login'=>$request->input('l_point', 0)]
    ];


    
    JsonKeyValue::update('auth', $config);

    return (object)['error'=>false];
  }

  public function getAgreement($key) {
    return JsonKeyValue::get($key);
  }

  public function setAgreement($key, $value) {
    return JsonKeyValue::set($key, $value);
  }

}
