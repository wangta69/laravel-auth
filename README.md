# 라라벨용 회원관리프로그램
## 공식문서 
[Doc](https://www.onstory.fun/packages/laravel-auth)
## Installation
```
composer require wangta69/laravel-auth
php artisan pondol:install-auth
```
## Crate user
> 세팅이후 관리자용 계정을 세팅합니다.
```
php artisan pondol:create-auth
```

## 제공 기능
- role 기능
- social login 기능
- JWTAuth

## How to Use
### Admin Page
- yourdomain.com/auth/admin

### 일반링크
> routes 폴더에 auth.php (프론트용) 및 auth-admin.php (관리자용) 이 있으므로 보시고 적절한 링크를 이용하시면 됩니다.

## laravel/socialite 세팅
> https://laravel.com/docs/11.x/socialite 참조하시어 생성 하시면 됩니다.
.env
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

## Important
> 아래내용은 수동으로 처리해 주어야 합니다.

### .env
> laravel 12 이상에서는 아래와 같이 .evn 파일을 변경해 주시기 바랍니다.
```
AUTH_MODEL=Pondol\Auth\Models\User\User
```
> laravel 11 이하 버전 (자동으로 변경)
```
/config/auth.php
'model' => App\Models\User::class,", 
=>
'model' => Pondol\Auth\Models\User\User::class,"
```
### bootstrap/app.php
> laravel 12 이상에서는 아래와 같이 설정을 추가해야 합니다.(12 미만 버전에서는  자동으로 처리됨)
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