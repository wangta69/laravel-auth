<?php
namespace Pondol\Auth\Traits\Auth\Admin;
use Pondol\Auth\Models\User\UserConfig;

trait Config
{
  public function __construct()
  {

  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function _index($request)
  {

    $user = config('pondol-auth');
    $termsOfUse = UserConfig::where('key', 'termsOfUse')->first();
    $privacyPolicy = UserConfig::where('key', 'privacyPolicy')->first();

    // auth
    $templates = [];
    $template_dir =  resource_path('views/auth/templates/views');
    $templates['user'] = array_map('basename',\File::directories($template_dir));

    $template_dir =  resource_path('views/auth/templates/mail');
    $templates['mail'] = array_map('basename',\File::directories($template_dir));

    return [
      'user'=>$user,
      'templates'=>$templates,
      'termsOfUse' => $termsOfUse->value,
      'privacyPolicy' => $privacyPolicy->value,
    ];
  }

  public function _update($request) {
    UserConfig::where('key', 'termsOfUse')->update(['value'=>$request->termsOfUse]);
    UserConfig::where('key', 'privacyPolicy')->update(['value'=>$request->privacyPolicy]);

    configSet('pondol-auth', [
      'activate' => $request->activate, 
      'template.user'=>$request->t_user, 
      'template.mail'=>$request->t_mail,
      'point.register'=>$request->input('r_point', 0),
      'point.login'=>$request->input('l_point', 0),
    ]); //  
    return (object)['error'=>false];
  }

}
