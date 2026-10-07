# 라라벨용 회원관리프로그램 (wangta69/laravel-auth)

## 공식문서

[Doc](https://www.onstory.fun/packages/laravel-auth)

## 제공 기능

- **Role & 권한 관리**: 다중 역할(Admin, Manager, User 등) 관리 및 미들웨어 검증
- **Social Login**: 구글, 깃허브, 카카오, 네이버 간편 로그인 및 기존 계정 연동 지원
- **Public Profile (공개 프로필)**: 커뮤니티, 게시판 연동형 타인 프로필 열람 페이지
- **Google 2FA**: Google Authenticator 기반 2단계 보안 인증
- **범용 포인트 시스템**: 무료/유료/정산용 포인트 분리 관리 및 차감 정책(FIFO/LIFO)
- **이메일 수신 거부**: 서명된(Signed) 안전한 URL 기반 원클릭 Unsubscribe
- **JWTAuth**: 모바일 앱 및 REST API 대응 인증 토큰 발급

---

## Installation (설치) \* 필독

### 1. Composer install

```bash
composer require wangta69/laravel-auth
php artisan pondol:install-auth
```

### 2. Create User (관리자 생성)

기본 설정 완료 후 최초 관리자용 계정을 생성합니다.

```bash
php artisan pondol:create-auth
```

### 3. Auth Model 변경

아래 두 가지 방법 중 하나를 선택하여 처리합니다.

#### 3.1 Extends 사용 (추천)

`app/Models/User.php`에서 패키지의 `PondolUser`를 상속(extends)받도록 변경합니다.

```php
<?php

namespace App\Models;

use Pondol\Auth\Models\User\User as PondolUser;

class User extends PondolUser
{
}
```

#### 3.2 config 또는 .env 변경

- **Laravel 11 이하:** `config/auth.php` 파일 직접 수정

  ```php
  'providers' => [
      'users' => [
          'driver' => 'eloquent',
          'model' => Pondol\Auth\Models\User\User::class,
      ],
  ],
  ```

- **Laravel 12 이상:** `.env` 파일에 환경 변수 추가
  ```env
  AUTH_MODEL=Pondol\Auth\Models\User\User
  ```

---

## How to Use

### 1. 관리자 페이지 접근 (Admin Page)

세팅이 완료되면 브라우저에서 `/auth/admin`으로 접속합니다.

- `https://yourdomain.com/auth/admin`

### 2. 일반 프론트 페이지 링크

프론트엔드용 라우트(`routes/auth.php`)에서 기본 제공되는 대표 링크 목록입니다:

- **로그인 / 회원가입**: `route('login')`, `route('register')`
- **마이페이지 (내 정보 수정)**: `route('user.profile')`, `route('user.edit')`
- **비밀번호 변경**: `route('user.change-password')`
- **2FA 보안 설정**: `route('2fa.setting')`
- **회원 탈퇴**: `route('cancel.account')`

---

## 타인 공개 프로필 (Public Profile) 연동

게시판(`laravel-bbs`)이나 댓글 등에서 작성자의 이름을 클릭했을 때 해당 사용자의 프로필 카드(아바타, 역할, 가입일 등)를 열람할 수 있는 공개 프로필 기능을 기본 제공합니다.

### 1. 링크 사용법 (Blade)

게시글 목록 또는 상세 화면의 작성자명에 아래와 같이 라우트를 연결합니다:

```blade
<a href="{{ route('user.public-profile', $article->user_id) }}" class="text-decoration-none">
    {{ $article->writer_name }}
</a>
```

### 2. 공개 프로필 옵션 설정 (`config/pondol-auth.php`)

타인에게 노출되는 정보 범위를 자유롭게 제어할 수 있습니다:

```php
'public_profile' => [
    // 마스킹된 이메일 노출 여부 (예: pon***@naver.com)
    'show_email' => false,

    // 가입일 노출 여부
    'show_joined_at' => true,

    // 역할(Role) 배지 표시 여부
    'show_roles' => true,
],
```

---

## 포인트 시스템 설정 (`config/pondol-auth.php`)

서비스 내 유/무상 포인트 및 정산 포인트 구분을 위한 전략을 지원합니다.

```php
'point' => [
    'default_type' => 0,    // 기본 포인트 타입
    'free_type' => 0,       // 무상/이벤트 포인트 식별자
    'paid_type' => 1,       // 유상/충전 포인트 식별자
    'earning_type' => 2,    // 마스터 수익/정산 포인트 식별자 (길라 사주인 등)
    'strategies' => [
        'purchase_order' => 'asc',  // 구매 차감 순서: asc(FIFO 오래된순), desc(LIFO 최신순)
        'refund_order'   => 'asc',  // 환불 차감 순서: asc(FIFO 오래된순), desc(LIFO 최신순)
    ],
    'initial_register_point' => 0, // 가입 시 축하 포인트
    'daily_login_point' => 0,      // 1일 1회 로그인 지급 포인트
],
```

---

## 권한 설정이 안 될 경우 (Laravel 11 이상)

Laravel 11 이상 버전에서는 `bootstrap/app.php`에 미들웨어 별칭(Alias)을 등록해 주어야 합니다:

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \Pondol\Auth\Http\Middleware\CheckRole::class,
            'role'  => \Pondol\Auth\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## 소셜 로그인 (Socialite) 연동 가이드

> 공식 문서: https://laravel.com/docs/socialite

### 1. 패키지 설치

```bash
composer require socialiteproviders/kakao socialiteproviders/naver
```

### 2. Event Listener 등록

- **Laravel 10 이하:** `app/Providers/EventServiceProvider.php`

```php
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        \SocialiteProviders\Kakao\KakaoExtendSocialite::class,
        \SocialiteProviders\Naver\NaverExtendSocialite::class,
    ],
];
```

- **Laravel 11 이상:** `bootstrap/providers.php` 또는 `AppServiceProvider::boot()`에서 이벤트 리스너 등록

### 3. `config/services.php` 설정

```php
'kakao' => [
    'client_id' => env('KAKAO_CLIENT_ID'),
    'client_secret' => env('KAKAO_CLIENT_SECRET'),
    'redirect' => env('KAKAO_REDIRECT_URI'),
],

'naver' => [
    'client_id' => env('NAVER_CLIENT_ID'),
    'client_secret' => env('NAVER_CLIENT_SECRET'),
    'redirect' => env('NAVER_REDIRECT_URI'),
],
```

### 4. `.env` 파일 환경 변수 설정

```env
# GOOGLE
GOOGLE_CLIENT_ID='xxxxxxxx-xxxxxxxx.apps.googleusercontent.com'
GOOGLE_CLIENT_SECRET='GOCSPX-xxxxxxx'

# GITHUB
GITHUB_CLIENT_ID=xxxxxxxx
GITHUB_CLIENT_SECRET=xxxxxxxx

# KAKAO
KAKAO_CLIENT_ID=REST_API_키
KAKAO_CLIENT_SECRET=보안코드
KAKAO_REDIRECT_URI=https://도메인/auth/social/kakao/callback
KAKAO_APP_REDIRECT_URI=https://도메인/api/v1/auth/social/kakao/callback

# NAVER
NAVER_CLIENT_ID=Client_ID
NAVER_CLIENT_SECRET=Client_Secret
NAVER_REDIRECT_URI=https://도메인/auth/social/naver/callback
NAVER_APP_REDIRECT_URI=https://도메인/api/v1/auth/social/naver/callback
```

---

### 🟡 카카오 (Kakao Developers) 설정 요약

1. [카카오 개발자 센터](https://developers.kakao.com/) 접속 및 앱 생성
2. **요약 정보**의 `REST API 키` 복사 -> `KAKAO_CLIENT_ID`
3. **플랫폼** > `Web` 플랫폼 등록 (사이트 도메인 등록)
4. **카카오 로그인** > 활성화 설정 `ON` 변경 후 **Redirect URI** 등록:
   - `https://도메인/auth/social/kakao/callback`
5. **동의항목**: `닉네임`(필수), `카카오계정 이메일`(선택/필수) 설정
6. **보안**: `Client Secret` 코드 생성 및 활성화 -> `KAKAO_CLIENT_SECRET`

---

### 🟢 네이버 (Naver Developers) 설정 요약

1. [네이버 개발자 센터](https://developers.naver.com/) 접속 및 Application 등록
2. 사용 API: `네이버 로그인` 선택
3. 제공 항목: 이름, 이메일, 별명 필수 선택
4. **환경 설정**: `PC 웹` 선택 후 서비스 URL 및 Callback URL 입력:
   - `https://도메인/auth/social/naver/callback`
5. 발급된 `Client ID`, `Client Secret` 확인 후 `.env`에 기입

---

## 메일 및 큐 (Queue) 세팅

가입 인증 메일, 비밀번호 재설정 메일 등은 Event & Queue Job을 통해 비동기로 발송됩니다. 반드시 큐 리스너를 실행해 주세요.

```bash
nohup php artisan queue:listen >> storage/logs/laravel.log &
```

---

## 패키지 통합 관리자단 구성

`wangta69/laravel-auth`는 게시판(`laravel-bbs`), 쇼핑몰(`laravel-market`) 등 당사의 다른 패키지와 독립적인 관리자 환경을 공유할 수 있도록 설계되어 있습니다.

[통합 관리자단 만드는 방법 보러가기](https://www.onstory.fun/packages/laravel-package-admin-merge)

## 실제 사용 사이트

This library is used in the production of [길라(gilra.kr) ](https://www.gilra.kr) (Online Fortune Service).
