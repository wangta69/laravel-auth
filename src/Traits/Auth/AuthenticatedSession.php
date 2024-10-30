<?php
namespace Pondol\Auth\Traits\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait AuthenticatedSession {



/**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function _destroy($request)
    {
      Auth::guard('web')->logout();

      $request->session()->invalidate();

      $request->session()->regenerateToken();
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
  protected function ensureIsNotRateLimited($request)
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
  protected function throttleKey($request)
  {
    return Str::lower($request->input('email')).'|'.$request->ip();
  }

  private function storeToLog($user) {
    $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']:"";
    $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN']:"";
    $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']:"";
    $remote_addr = $this->getRealIpAddr();

    $log = new \Pondol\Auth\Models\User\UserLog;
    $log->user_id = $user->id;
    $log->http_referer = $http_referer;
    $log->http_origin = $http_origin;
    $log->http_user_agent = $http_user_agent;
    $log->remote_addr = $remote_addr;

    $log->save();
    return array("result"=>true);//, "inserted_id"=>$betting->id
  }


  private function getRealIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP']) && getenv('HTTP_CLIENT_IP')){
      return $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && getenv('HTTP_X_FORWARDED_FOR')){
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else if(!empty($_SERVER['REMOTE_HOST']) && getenv('REMOTE_HOST')){
      return $_SERVER['REMOTE_HOST'];
    }else if(!empty($_SERVER['REMOTE_ADDR']) && getenv('REMOTE_ADDR')){
      return $_SERVER['REMOTE_ADDR'];
    }
    return false;
  }
  
}