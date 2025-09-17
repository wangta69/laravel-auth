<?php
namespace Pondol\Auth\Http\Middleware;
use Closure;
class CheckRole
{
  public function handle($request, Closure $next, $role = null)
  {   
    if (!$request->user()) {
      return redirect('login');
    }
    
    if (is_null($role)) {
        // middleware('admin') 처럼 파라미터 없이 호출된 경우
        // 현재 라우트의 미들웨어 이름에서 역할을 추출합니다.
        $routeName = $request->route()->getName(); // (대안) 라우트 이름을 사용할 수도 있음
        
        // 현재 적용된 미들웨어의 별칭(alias)을 가져옵니다.
        $middlewareName = Str::after(last($request->route()->gatherMiddleware()), ':');
        $role = $middlewareName;
    }
    // ------------------------------------

    if (!$request->user()->hasRole($role)) {
      // 권한이 없을 때 403 Forbidden 오류를 반환하는 것이 더 일반적입니다.
      abort(403, '이 작업을 수행할 권한이 없습니다.');
      // 또는 기존처럼 리다이렉트
      // return redirect('/'); 
    }

    return $next($request);
  }
}