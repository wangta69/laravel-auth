<?php

namespace Pondol\Auth\Http\Controllers\Social;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use JWTAuth;

use Laravel\Socialite\Facades\Socialite;


use Pondol\Auth\Models\Role\Role;
use Pondol\Auth\Models\User\User;
use Pondol\Auth\Models\User\SocialAccount;
use Pondol\Common\Facades\JsonKeyValue;
use Pondol\Auth\Traits\AuthenticatedSession;


use App\Http\Controllers\Controller;

// https://vuxy.tistory.com/entry/Laravel-8-%EC%86%8C%EC%85%9C%EB%A1%9C%EA%B7%B8%EC%9D%B8Laravel-Socialite-1
class AuthenticatedSessionController extends Controller
{

  use AuthenticatedSession;

  public function __construct() 
  {

  }

  public function redirectToProvider($provider)
  {
    //리프레시 토큰을 가져오려면 옵션파라미터 'access_type'=>'offline', 'prompt'=>'consent' 으로 설정해줘야합니다.
    switch($provider) {
      case 'github':
        return Socialite::driver($provider)->scopes(['read:user', 'public_repo'])->redirect();
        break;
      case 'google':
        return Socialite::driver('google')->with(['access_type'=>'offline', 'prompt'=>'consent' ])->redirect();
        // return Socialite::driver('google')->with(['access_type'=>'offline', 'prompt'=>'consent' ])->redirect();
        break;
      default:
        return Socialite::driver($provider)->redirect();
        break;
    }
  }

  // 2. handleProviderCallback () 로그인한 후에 이미 만들어진 아이디인지 확인후 처리
  public function handleProviderCallback($provider)
  {
    $socialUser = Socialite::driver($provider)->stateless()->user();
    $defaultPath = config('pondol-auth.default_redirect_path', '/');

    // 유저가 이미 회원인지 확인하는 메서드입니다.
    $result = $this->findOrCreateUser($provider, $socialUser);


    $user = $result['user'];
    $type = $result['type'];

  //$user뒤의 내용을 true로 설정하면 리멤버토큰(자동로그인)이 발급됩니다.
    \Auth::login($user, false);

    $userToken=JWTAuth::fromUser($user);

  //토큰을 활용하기위해 로컬에 저장해도 되고 세션에 저장하거나 쿠키에 저장해서 활용할 수 있겠습니다.
    if($type=="register") {
      return redirect()->route('register.success');
    } else { // login
      return redirect()->intended($defaultPath);
    }

  }

  /**
  * 아이디 존재하지않으면 새로 생성 하는 메서드
  * 먼저 email을 기준으로 현재 회원정보가 있는지 확인 (없으면 새로운 회원을 만들어 준다.)
  */
  private function findOrCreateUser($provider, $socialUser){

    $auth_cfg = JsonKeyValue::getAsJson('auth');

    $user = User::where('email', $socialUser->getEmail())
      ->first();

    $type = 'login';
    if(!$user) {
      $type = 'register';
      $user = new User;
      $user->name = $socialUser->getName();
      $user->email = $socialUser->getEmail();

      if($auth_cfg->activate == "auto" || $auth_cfg->activate == "email") { // social login은 이메일을 인증 받은 것으로 간주한다.
        $user->active = 1;
      }

      $user->save();
  
      // 추가 (기본 role 적용)
      $user->roles()->attach(Role::firstOrCreate(['name' =>config('pondol-auth.roles.default_role')]));
      event(new Registered($user));
    }

    $user->logined_at = date("Y-m-d H:i:s");
    $user->save();
    $this->storeToLog($user);
    // 회원 정보가 입력되면 social_accounts에도 입력을 처리한다.
    $this->findOrCreateSocialAccount($user, $provider, $socialUser);
    return ['user'=>$user, 'type'=>$type];
  }

  public function findOrCreateSocialAccount($user, $provider, $socialUser){
    $existAccount = SocialAccount::where('provider', $provider)->where('provider_id', $socialUser->id)->first();
    if($existAccount){
      $existAccount->name = $socialUser->getName();
      $existAccount->avatar = $socialUser->getAvatar();
      $existAccount->token = $socialUser->token;
      if($socialUser->refreshToken){
        $existAccount->refresh_token = $socialUser->refreshToken;
      }
      $existAccount->save();
    } else{
      $socialAccount = SocialAccount::firstOrCreate([
        'user_id' => $user->id,
        'name'  => $socialUser->getName(), //이름가져오기
        'provider' => $provider,
        'provider_id'  => $socialUser->getId(), // 구글내부 아이디값 가져오기
        'email' => $socialUser->getEmail(), // 구글 이메일 가져오기
        'avatar' =>$socialUser->getAvatar(), // 구글내 프로필 이미지
        'token'=> $socialUser->token,
        'refresh_token'=> $socialUser->refreshToken
      ]);
    }
  }
}
