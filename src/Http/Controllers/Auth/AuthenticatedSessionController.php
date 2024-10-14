<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\Auth\AuthenticatedSession;

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
  }




  /**
   * 로그인 폼 출력
   */
  public function create(Request $request)
  {
    $f = $request->f; // f 가  auth.mypage.order 이면 주문내역 확인 이므로 비회원인 경우 주문내역으로 바록가게 하고 없으면 일반적 로그인이므로 비회원 주문확인을 삭제한다.
    // return view('auth.'.config('auth.template.auth.theme').'.login', ['f'=>$f]);
    return view('auth.templates.views.'.config('auth-pondol.template.user').'.login', ['f'=>$f]);
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

    if($user->active == 0 && config('auth-pondol.activate') != 'email') {
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
   * The user has been authenticated.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  mixed $user
   * @return mixed
   */
  protected function authenticate($request)
  {
    $this->ensureIsNotRateLimited($request);

    if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
          'email' => trans('auth.failed'),
        ]);
    }

    RateLimiter::clear($this->throttleKey($request));
  }

  /**
   * Ensure the login request is not rate limited.
   *
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  private function ensureIsNotRateLimited($request)
  {
    if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
      return;
    }

    event(new Lockout($request));

    $seconds = RateLimiter::availableIn($this->throttleKey($request));

    throw ValidationException::withMessages([
        'email' => trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]),
    ]);
  }
  /**
   * Get the rate limiting throttle key for the request.
   *
   * @return string
   */
  public function throttleKey($request)
  {
    return Str::lower($request->input('email')).'|'.$request->ip();
  }

  

   /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
      Auth::guard('web')->logout();

      $request->session()->invalidate();

      $request->session()->regenerateToken();

      return redirect('/');
    }
}
