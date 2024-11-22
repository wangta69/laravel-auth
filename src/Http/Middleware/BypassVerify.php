<?php
namespace Pondol\Auth\Http\Middleware;
use Closure;
class BypassVerify
{
  public function handle($request, Closure $next)
  {
    return $next($request);
  }
}