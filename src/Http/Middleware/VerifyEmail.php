<?php

namespace Pondol\Auth\Http\Middleware;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Contracts\Routing\Middleware;
use Closure;

class VerifyEmail // implements Middleware 
{
  // public function __construct(Route $route)
  // {
  //     $this->route = $route;
  // }

  public function handle($request, Closure $next)
  {

    if (Auth::check() && config('pondol-auth.activate') == 'email' && !in_array('bypassverify', $request->route()->action['middleware']) &&  !Auth::user()->email_verified_at) {
      return redirect()->route('verification.notice');
    } 

    return $next($request);
  } 
}
