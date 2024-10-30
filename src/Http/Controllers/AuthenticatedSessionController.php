<?php

namespace Pondol\Auth\Http\Controllers;


use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;
use Validator;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Pondol\Auth\Traits\Auth\AuthenticatedSession;

class AuthenticatedSessionController extends Controller
{

  use AuthenticatedSession;
  // use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  // protected $redirectTo = RouteServiceProvider::ADMIN;
  // protected $redirectTo = '/';

  // protected function redirectTo()
  // {
  //   if (auth()->user()->role == 'administrator') {
  //     return '/admin';
  //   }
  //   return '/admin1';
  // }

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    // $this->middleware('guest')->except('logout');
    \Log::info('AuthenticatedSessionController __construc');
    \Log::info(url()->previous());
  }




  /**
   * 로그인 폼 출력
   */
  public function create(Request $request)
  {
    $f = $request->f; // f 가  auth.mypage.order 이면 주문내역 확인 이므로 비회원인 경우 주문내역으로 바록가게 하고 없으면 일반적 로그인이므로 비회원 주문확인을 삭제한다.   
    session()->put('url.intended',url()->previous());
    return view('auth.templates.views.'.config('pondol-auth.template.user').'.login', ['f'=>$f]);
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
    // $request->session()->regenerate();

    $user = \Auth::user();

  
    if($user->active == 0 && config('pondol-auth.activate') != 'email') {
      // 이메일의 경우 로그인 후 인증받아야 함
      auth()->logout();  // logout
      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return redirect()->back()
      ->withErrors(['active'=>'인증대기중입니다.'])
      ->withInput($request->except('password'));
    }

    $user->logined_at = date("Y-m-d H:i:s");
    $user->save();
    $this->storeToLog($user);
 

    return redirect()->intended(RouteServiceProvider::HOME);
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
