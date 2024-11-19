<?php

namespace Pondol\Auth\Http\Middleware;

use Closure;

class VerifyEmail
{
  public function handle($request, Closure $next)
  {
    if ($request->user() && config('pondol-auth.activate') == 'email' && !$request->user()->email_verified_at) {
      return redirect()->route('verification.notice');
    }
    return $next($request);
  } 
}
