<?php

namespace Pondol\Auth\Http\Middleware;

use Closure;
use Illuminate\Support\Str; // [추가] Str 파사드를 사용하기 위해 임포트합니다.

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        // 이 부분은 로그인 여부를 먼저 확인하는 auth 미들웨어 뒤에 실행되므로,
        // 사실상 불필요하지만 안전을 위해 그대로 둡니다.
        if (! $request->user()) {
            return redirect('login');
        }

        // [수정] 파라미터가 없을 때, 현재 실행된 미들웨어의 '별칭'을 역할로 사용합니다.
        if (is_null($role)) {
            // 현재 라우트 객체에서 실행 중인 컨트롤러 액션 정보를 가져옵니다.
            $routeAction = $request->route()->getAction();

            // 액션 정보의 'middleware' 배열에서 마지막에 실행된 미들웨어 별칭을 찾습니다.
            // (예: ['web', 'auth', 'admin'] -> 'admin'을 가져옴)
            $middlewareAlias = last($routeAction['middleware'] ?? []);

            // 만약 'admin:administrator' 와 같이 파라미터가 있는 형태일 경우, ':' 앞부분만 추출합니다.
            $role = Str::before($middlewareAlias, ':');
        }

        // 역할(role)이 정상적으로 설정되지 않았을 경우를 대비한 방어 코드
        if (empty($role)) {
            abort(500, '미들웨어 역할(Role)이 지정되지 않았습니다.');
        }

        if (! $request->user()->hasRole($role)) {
            // 권한이 없을 때 403 Forbidden 오류를 반환합니다.
            abort(403, '이 작업을 수행할 권한이 없습니다.');
        }

        return $next($request);
    }
}
