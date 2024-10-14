<?php

namespace App\Http\Controllers\Auth\Social;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use JWTAuth;

use Laravel\Socialite\Facades\Socialite;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Auth\User\SocialAccount;

use App\Providers\RouteServiceProvider;
// use App\Http\Controllers\Services\ConfigService;
use App\Http\Controllers\Controller;

use App\Traits\Auth\Login;
use App\Events\Registered as MarketRegistered;

class AuthenticatedSessionController extends Controller
{

  use Login;

  public function __construct() // ConfigService $configSvc
  {
    // $this->configSvc = $configSvc;
  }
  // 1. redirectToProvider() 구글에 로그인요청
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
    }
  }

// 2. handleProviderCallback () 로그인한 후에 이미 만들어진 아이디인지 확인후 처리
  public function handleProviderCallback($provider)
  {
    switch($provider) {
      case 'github': case 'google':
        //구글에서 로그인확인후 정보 제공
        $socialUser = Socialite::driver($provider)->stateless()->user();
        break;
    }

   
    // 유저가 이미 회원인지 확인하는 메서드입니다.
    $result = $this->findOrCreateUser($provider, $socialUser);
    $user = $result['user'];
    $type = $result['type'];

  //$user뒤의 내용을 true로 설정하면 리멤버토큰(자동로그인)이 발급됩니다.
    \Auth::login($user, false);

    $userToken=JWTAuth::fromUser($user);

  //토큰을 활용하기위해 로컬에 저장해도 되고 세션에 저장하거나 쿠키에 저장해서 활용할 수 있겠습니다.
    if($type=="register") {
      event(new MarketRegistered($user));
      return redirect()->route('register.success');
    } else { // login
      return redirect()->intended(RouteServiceProvider::HOME);
    }

  }

  /**
  * 아이디 존재하지않으면 새로 생성 하는 메서드
  * 먼저 email을 기준으로 현재 회원정보가 있는지 확인 (없으면 새로운 회원을 만들어 준다.)
  */
  private function findOrCreateUser($provider, $socialUser){

    $user = User::where('name', $socialUser->getName())
      ->where('email', $socialUser->getEmail())
      ->first();
    $type = 'login';
    if(!$user) {
      $type = 'register';
      $user = new User;
      $user->name = $socialUser->getName();
      $user->email = $socialUser->getEmail();

      $usercfg = $this->configSvc->get('user');
      if($usercfg['active'] == "auto") {
        $user->active = 1;
      }
  
      // 추가 (기본 role 적용)
      if (config('auth.roles.default_role')) {
        $user->roles()->attach(Role::firstOrCreate(['name' => config('auth.roles.default_role')]));
      }

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
