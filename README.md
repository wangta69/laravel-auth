# 라라벨용 회원관리프로그램

## 공식문서

[Doc](https://www.onstory.fun/packages/laravel-auth)

## 제공 기능

- role 기능
- social login 기능
- JWTAuth

## Installation (설치) \* 필독

### 1. Composer install

```
composer require wangta69/laravel-auth
php artisan pondol:install-auth
```

## 2. Crate user

세팅이후 관리자용 계정을 세팅합니다.

```
php artisan pondol:create-auth
```

## 3. Auth Model 변경

아래 두가지 방법중 하나를 선택하여 처리

### 3.1 Extends 사용(추천)

app\Model\User 를 extends 처리

```
<?php

namespace App\Models;

use Pondol\Auth\Models\User\User as PondolUser;

class User extends PondolUser
{
}
```

### 3.2 config mode 변경

laravel 12 이하는 직접 config/auth.php 수정

```
'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => Pondol\Auth\Models\User\User::class,
        ],
    ],
```

laravel 12 이상은 .env 파일의 AUTH_MODEL에서 변경 혹은 추가

```
AUTH_MODEL=Pondol\Auth\Models\User\User
```

## How to Use

### Admin Page

- yourdomain.com/auth/admin

### 일반링크

> routes 폴더에 auth.php (프론트용) 및 auth-admin.php (관리자용) 이 있으므로 보시고 적절한 링크를 이용하시면 됩니다.

## 권한설정이 안될 경우

laravel 12 이상에서는 아래와 같이 bootstrap/app.php 설정을 추가해야 합니다.(12 미만 버전에서는 자동으로 처리됨)

```
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \Pondol\Auth\Http\Middleware\CheckRole::class,
        // 필요하다면 다른 역할에 대한 별칭도 추가할 수 있습니다.
        // 'manager' => \Pondol\Auth\Http\Middleware\CheckRole::class,
    ]);
})
```

```
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'admin:administrator']) // 적용할 미들웨어 그룹
                 ->prefix('admin')                  // URL에 '/admin' 접두사 자동 추가
                 ->name('admin.')                   // 라우트 이름에 'admin.' 접두사 자동 추가
                 ->group(base_path('routes/admin.php')); // 로드할 라우트 파일 경로
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
        'admin' => \Pondol\Auth\Http\Middleware\CheckRole::class,
        // 필요하다면 다른 역할에 대한 별칭도 추가할 수 있습니다.
        // 'manager' => \Pondol\Auth\Http\Middleware\CheckRole::class,
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

```

## laravel/socialite 세팅

> https://laravel.com/docs/11.x/socialite 참조하시어 생성 하시면 됩니다.
> .env

```
GOOGLE_CLIENT_ID='xxxxxxxx-xxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com'
GOOGLE_CLIENT_SECRET='GOCSPX-xxxxxxx_xxxxxx'

GITHUB_CLIENT_ID=xxxxxxxx
GITHUB_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

## 메일관련 세팅

> 메일은 Event 및 Job으로 처리되므로 아래와 같이 세팅해 주어야 합니다.

```
nohup php artisan queue:listen >> storage/logs/laravel.log &
```
