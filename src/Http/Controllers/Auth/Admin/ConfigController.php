<?php
namespace App\Http\Controllers\Auth\Admin;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

use Validator;
use DB;

use App\Models\Auth\User\UserConfig;

use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
  public function __construct()
  {

  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {

    $user = config('auth-pondol');
    $termsOfUse = UserConfig::where('key', 'termsOfUse')->first();
    $privacyPolicy = UserConfig::where('key', 'privacyPolicy')->first();

    // auth
    $templates = [];
    $template_dir =  resource_path('views/auth/templates/views');
    $templates['user'] = array_map('basename',\File::directories($template_dir));

    $template_dir =  resource_path('views/auth/templates/mail');
    $templates['mail'] = array_map('basename',\File::directories($template_dir));

    return view('auth.admin.users.config', [
      'user'=>$user,
      'templates'=>$templates,
      'termsOfUse' => $termsOfUse->value,
      'privacyPolicy' => $privacyPolicy->value,
    ]);
  }

  public function update(Request $request) {
    UserConfig::where('key', 'termsOfUse')->update(['value'=>$request->termsOfUse]);
    UserConfig::where('key', 'privacyPolicy')->update(['value'=>$request->privacyPolicy]);
    
    configSet('auth-pondol', ['activate' => $request->activate, 'template.user'=>$request->t_user, 'template.mail'=>$request->t_mail]); //  

    return response()->json(['error'=>false]);
  }

}
