<?php

namespace App\Http\Middleware;

use Closure;

class VerifyEmail
{
  public function handle($request, Closure $next)
  {
    if ($request->user() && config('auth-pondol.activate') == 'email' && !$request->user()->email_verified_at) {
      return redirect()->route('verification.notice');
    }
    return $next($request);
  } 
}