<?php

namespace Pondol\Auth\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Validator;

// use App\Providers\RouteServiceProvider;
use Pondol\Auth\Traits\AuthenticatedSession;
use Pondol\Common\Facades\JsonKeyValue;

use App\Http\Controllers\Controller;

class AuthenticatedSessionController extends Controller
{

  use AuthenticatedSession;
  /**
   * Where to redirect users after login.
   *
   * @var string
   */

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * 로그인 폼 출력
   */
  public function create(Request $request)
  {
    $f = $request->f; // f 가  auth.mypage.order 이면 주문내역 확인 이므로 비회원인 경우 주문내역으로 바록가게 하고 없으면 일반적 로그인이므로 비회원 주문확인을 삭제한다.   
    session()->put('url.intended',url()->previous());
    return view(auth_theme('user').'.login', ['f'=>$f]);
  }

  /** @POST
  */
  public function store(Request $request){

    
    // \Redirect::setIntendedUrl($request->getUri());
    $validator = Validator::make($request->all(), [
      'email' => ['required', 'string', 'email'],
      'password' => ['required', 'string'],
    ]);

    if ($validator->fails()) {
      return redirect()->back()
      ->withErrors($validator->errors())
      ->withInput($request->except('password'));
    }
  


    $this->authenticate($request);    //authenticate 시  자동으로 Login event 발생 event(new Login(config('auth.defaults.guard'), $user, ''));

    $user = \Auth::user();

    $auth_cfg = JsonKeyValue::getAsJson('auth');


    if($user->active == 0 && $auth_cfg->activate != 'email') {
      // 이메일의 경우 로그인 후 인증받아야 함
      auth()->logout();  // logout
      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return redirect()->back()
      ->with('error', '인증대기중입니다.')
      // ->withErrors(['active'=>'인증대기중입니다.'])
      ->withInput($request->except('password'));
    }



    // 2fa 관련 처리
    if ($user->google2fa_secret) { // google2fa_secret 가 세팅된 상태라면
      \Auth::logout(); // 로그아웃 시킨다.
      $request->session()->put('2fa:user:id', $user->id); // 현재값은 세션에 넣어 둔다.
      return redirect()->route('2fa.validate'); // 2fa를 입력하는 창으로 이동 시킨다. (아래 getValidateToken() 호출)
    }

    // $user->logined_at = date("Y-m-d H:i:s");
    $user->logined_at = now();
    $user->save();
    $this->storeToLog($user, $request);

    // 1. 설정 파일에서 리디렉션 경로들을 가져옵니다.
    $redirectsByRole = config('pondol-auth.redirect_after_login', []);
    $defaultPath = config('pondol-auth.default_redirect_path', '/');

    // 2. 사용자의 역할 이름들을 배열로 가져옵니다. (캐싱하면 더 좋습니다)
    //    사용자 모델에 roles() 관계가 반드시 정의되어 있어야 합니다.
    try {
      $userRoles = $user->roles()->pluck('name')->all();
      \Log::debug('User roles: ', $userRoles);
    } catch (\Throwable $th) {
      // roles() 관계가 없거나 오류 발생 시 기본 경로로 이동
      \Log::error('Error fetching user roles: ' . $th->getMessage());
      return redirect()->intended($defaultPath);
    }

    // 3. 리디렉션할 경로를 결정합니다. 기본값으로 시작합니다.
    $redirectTo = $defaultPath;

    // 4. 설정 파일에 정의된 역할 순서대로 순회하며 사용자가 해당 역할을 가졌는지 확인합니다.
    //    (이것이 우선순위를 보장하는 핵심입니다.)
    foreach ($redirectsByRole as $role => $path) {

    \Log::debug("Checking role: $role with path: $path");
      if (in_array($role, $userRoles)) {
        $redirectTo = $path; // 일치하는 첫 번째 역할을 찾으면 경로를 설정하고
        return redirect($redirectTo);
        break; // 즉시 루프를 탈출합니다.
      }
    }

   \Log::debug("Redirecting to: $redirectTo");
 
    return redirect()->intended($redirectTo);
  
  }


   /**
   * Destroy an authenticated session.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Request $request)
  {
    $this->_destroy($request);
    // session()->forget('url.intented');
    return redirect('/');
  }
}
