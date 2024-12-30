<?php

namespace Pondol\Auth\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Pondol\Common\Facades\JsonKeyValue;
class VerifyEmail // implements Middleware 
{
  // public function __construct(Route $route)
  // {
  //     $this->route = $route;
  // }

  public function handle($request, Closure $next)
  {
    $auth_cfg = JsonKeyValue::getAsJson('auth');
    if (Auth::check() && $auth_cfg->activate == 'email' && !in_array('bypassverify', $request->route()->action['middleware']) &&  !Auth::user()->email_verified_at) {
      return redirect()->route('verification.notice');
    } 

    return $next($request);
  } 
}
