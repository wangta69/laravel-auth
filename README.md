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

### Admin Page 접근

세팅이 완료되면 브라우저 입력창에 auth/admin을 입력하면 관리자 페이지로 접근됩니다.

- yourdomain.com/auth/admin

### 일반페이지 링크

> routes 폴더에 auth.php (프론트용) 및 auth-admin.php (관리자용) 이 있으므로 보시고 적절한 링크를 이용하시면 됩니다.

## 권한설정이 안될 경우

laravel 11 이상에서는 아래와 같이 bootstrap/app.php 설정을 추가해야 합니다.(11 미만 버전에서는 자동으로 처리됨)

```
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            // 1. 일반 웹 라우트 로드
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // 2. 관리자 라우트 로드 (auth와 admin 미들웨어를 순차적으로 적용)
            Route::middleware(['web', 'auth', 'admin']) // <- 핵심!
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
            'role' => \Pondol\Auth\Http\Middleware\CheckRole::class,
        ]);
        //
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

### 네이버, 카카오 세팅

#### composer install

```
composer require socialiteproviders/kakao socialiteproviders/naver
```

#### app/Providers/EventServiceProvider.php

```
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        // ... 기존 드라이버들 ...
        \SocialiteProviders\Kakao\KakaoExtendSocialite::class,
        \SocialiteProviders\Naver\NaverExtendSocialite::class,
    ],
];
```

#### config/services.php

env 파일에 아래 정보 추가

```
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

.env

```
KAKAO_CLIENT_ID=your_kakao_client_id
KAKAO_CLIENT_SECRET=your_kakao_client_secret
KAKAO_REDIRECT_URI=http(s)://도메인/auth/social/kakao/callback

NAVER_CLIENT_ID=your_naver_client_id
NAVER_CLIENT_SECRET=your_naver_client_secret
NAVER_REDIRECT_URI=http(s)://도메인auth/social/naver/callback
```

---

### 1. 🟡 카카오 (Kakao Developers)

카카오는 **REST API 키**를 `Client ID`로 사용합니다.

1.  **접속 및 로그인:**
    - [카카오 개발자 센터](https://developers.kakao.com/)에 접속하여 카카오 계정으로 로그인합니다.
2.  **애플리케이션 추가:**
    - 메뉴: `내 애플리케이션` > `애플리케이션 추가하기`
    - 앱 이름: **길라잡이** / 사업자명: (본인 이름 또는 회사명) 입력 후 저장.
3.  **키 값 확인 (중요):**
    - 생성된 앱을 클릭하고 좌측 메뉴 `요약 정보` 또는 `앱 키`를 누릅니다.
    - **REST API 키**: 이것이 **`KAKAO_CLIENT_ID`** 입니다. (복사해두세요)
4.  **플랫폼 설정:**
    - 좌측 메뉴: `플랫폼` > `Web 플랫폼 등록`
    - 사이트 도메인:
      - `https://도메인`
      - `http://localhost:8000` (개발 테스트용)
      - (줄바꿈으로 모두 등록)
5.  **카카오 로그인 활성화 & Redirect URI:**
    - 좌측 메뉴: `카카오 로그인`
    - 활성화 설정: `OFF`를 눌러 **`ON`**으로 변경.
    - **Redirect URI 등록**: 하단 버튼 클릭 후 입력.
      - `https://도메인/auth/social/kakao/callback`
      - (개발용) `http://localhost:8000/auth/social/kakao/callback`
6.  **동의 항목 설정 (정보 수집):**
    - 좌측 메뉴: `카카오 로그인` > `동의항목`
    - **닉네임**: 필수 동의
    - **카카오계정(이메일)**: 권한 없음 or 선택 동의 (비즈니스 앱 전환 시 필수 가능)
    - **성별, 생일, 출생연도**: 운세 사이트이므로 가능하다면 '선택 동의'로 설정해두면 좋습니다.
7.  **Client Secret (보안 코드):**
    - 좌측 메뉴: `카카오 로그인` > `보안`
    - Client Secret 코드를 `생성` 버튼 눌러서 발급.
    - 이 값이 **`KAKAO_CLIENT_SECRET`** 입니다. (활성화 상태 '사용함' 체크 필수)

---

### 2. 🟢 네이버 (Naver Developers)

네이버는 회원가입 후 애플리케이션 등록 승인이 필요 없으며 즉시 발급됩니다.

1.  **접속 및 로그인:**
    - [네이버 개발자 센터](https://developers.naver.com/)에 접속하여 로그인합니다.
2.  **애플리케이션 등록:**
    - 상단 메뉴: `Application` > `애플리케이션 등록`
    - 애플리케이션 이름: **길라잡이**
    - **사용 API**: `네이버 로그인` 선택.
3.  **정보 제공 동의 설정:**
    - 필수/추가/사용안함 선택 화면이 나옵니다.
    - **회원 이름**: 필수
    - **이메일 주소**: 필수
    - **별명**: 필수
    - **성별**: 필수 (운세용)
    - **생일**: 필수 (운세용)
    - **출생연도**: 필수 (운세용)
    - _(참고: 검수 단계 전에는 '필수'로 체크해도 개발 중에는 다 넘어옵니다.)_
4.  **환경 설정 (로그인 오픈 API 서비스 환경):**
    - `PC 웹` 선택.
    - **서비스 URL**: `https://도메인` (대표 도메인)
    - **Callback URL**:
      - `https://도메인/auth/social/naver/callback`
      - `http://localhost:8000/auth/social/naver/callback` (개발용 추가 가능)
5.  **키 값 확인:**
    - 등록 완료 후 `내 애플리케이션` 메뉴에서 방금 만든 앱 선택.
    - **Client ID**: **`NAVER_CLIENT_ID`**
    - **Client Secret**: **`NAVER_CLIENT_SECRET`** (보기 버튼 눌러서 확인)

---

### 3. 📝 Laravel .env 파일 적용

위에서 복사한 키 값들을 프로젝트 루트의 `.env` 파일 맨 아래에 붙여넣으세요.

```ini
# .env 파일

# ==================================
# KAKAO LOGIN
# ==================================
KAKAO_CLIENT_ID=복사한_REST_API_키
KAKAO_CLIENT_SECRET=복사한_보안_코드
# 개발 환경이면 localhost, 배포 환경이면 실제 도메인
KAKAO_REDIRECT_URI=https://도메인/auth/social/kakao/callback

# ==================================
# NAVER LOGIN
# ==================================
NAVER_CLIENT_ID=복사한_Client_ID
NAVER_CLIENT_SECRET=복사한_Client_Secret
NAVER_REDIRECT_URI=https://도메인/auth/social/naver/callback
```

### 💡 주의 사항

- **Redirect URI 불일치:** 개발자 센터에 등록한 주소와 `.env`에 적은 주소가 **글자 하나라도(http/https, www 유무, 끝에 슬래시 등) 다르면** 오류가 발생합니다. 정확히 일치시켜 주세요.
- **서비스 URL:** 네이버의 경우 서비스 URL(`https://도메인`)과 실제 접속해서 로그인을 시도하는 도메인이 다르면 오류가 날 수 있습니다. 로컬 개발 시에는 호스트 파일 설정 등을 확인하세요.
-

## 메일관련 세팅

> 메일은 Event 및 Job으로 처리되므로 아래와 같이 세팅해 주어야 합니다.

```
nohup php artisan queue:listen >> storage/logs/laravel.log &
```

## 통합관리자단 만들기

현재 제공중인 회원관리프로그램(wangta69/laravel-auth) 은 많은 패키지중 일부 입니다. 별도로 bbs나 market등 기타 package등도 제작/배포중에 있습니다.
이들은 각각 별도의 관리자단을 가지고 있으며 이를 통합하기 위해서는 아래 링크를 참조해 주시기 바랍니다.

[통합관리자단 만드는 방법 보기](https://www.onstory.fun/packages/laravel-package-admin-merge)
