<?php

namespace Pondol\Auth\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use Pondol\Auth\Events\Registered;
use Pondol\Auth\Models\Role\Role;
use Pondol\Auth\Models\User\SocialAccount;
use Pondol\Auth\Traits\AuthenticatedSession;
use Pondol\Common\Facades\JsonKeyValue;

class AuthenticatedSessionController extends Controller
{
    use AuthenticatedSession;

    protected $userModel;

    public function __construct()
    {
        $modelClass = config('auth.providers.users.model', \Pondol\Auth\Models\User\User::class);
        $this->userModel = new $modelClass;
    }

    public function redirectToProvider($provider)
    {
        switch ($provider) {
            case 'github':
                return Socialite::driver($provider)->scopes(['read:user', 'public_repo'])->redirect();

            case 'google':
                return Socialite::driver('google')
                    ->with(['access_type' => 'offline', 'prompt' => 'consent'])
                    ->redirect();

            case 'kakao':
                // 카카오는 별도 옵션 없이 기본 리다이렉트
                return Socialite::driver('kakao')->redirect();

            case 'naver':
                // 네이버도 기본 리다이렉트
                return Socialite::driver('naver')->redirect();

            default:
                return Socialite::driver($provider)->redirect();
        }
    }

    // 2. handleProviderCallback () 로그인한 후에 이미 만들어진 아이디인지 확인후 처리
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => '소셜 인증에 실패했습니다.']);
        }

        // ============================================================
        // 이미 로그인된 상태라면? -> 계정 연동(Link) 처리
        // ============================================================
        if (Auth::check()) {
            return $this->linkSocialAccount($provider, $socialUser);
        }

        $defaultPath = config('pondol-auth.default_redirect_path', '/');

        // 동적 모델을 사용하는 메서드 호출
        $result = $this->findOrCreateUser($provider, $socialUser);
        $user = $result['user'];
        $type = $result['type'];

        \Auth::login($user, true); // remember me

        // $userToken = JWTAuth::fromUser($user); // 필요시 사용

        if ($type == 'register') {
            return redirect()->route('register.success');
        } else {
            return redirect()->intended($defaultPath);
        }
    }

    /**
     * 소셜 계정 연동 처리
     */
    private function linkSocialAccount($provider, $socialUser)
    {
        $currentUser = Auth::user();

        // [핵심] 설정 파일에서 '연동 후 이동할 라우트 이름'을 가져옴 (기본값: 'home')
        // pondol-auth.php 설정 파일에 'route_after_social_link' 키를 추가할 예정
        $redirectRoute = config('pondol-auth.route_after_social_link', 'home');

        $existingAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($existingAccount) {
            if ($existingAccount->user_id == $currentUser->id) {
                return redirect()->route($redirectRoute)
                    ->with('status', '이미 연동된 계정입니다.');
            }

            return redirect()->route($redirectRoute)
                ->withErrors(['error' => '이미 다른 회원에게 연결된 소셜 계정입니다.']);
        }

        $this->findOrCreateSocialAccount($currentUser, $provider, $socialUser);

        return redirect()->route($redirectRoute)
            ->with('status', $provider.' 계정이 성공적으로 연동되었습니다.');
    }

    /**
     * 아이디 존재하지않으면 새로 생성 하는 메서드
     * 먼저 email을 기준으로 현재 회원정보가 있는지 확인 (없으면 새로운 회원을 만들어 준다.)
     */
    private function findOrCreateUser($provider, $socialUser)
    {

        $auth_cfg = JsonKeyValue::getAsJson('auth');

        // 이메일 처리 (카카오 등 이메일 없을 경우 대비)
        $email = $socialUser->getEmail();
        if (! $email) {
            $email = $socialUser->getId().'@'.$provider.'.social';
        }

        // [변경] User::where -> $this->userModel->where
        $user = $this->userModel->withTrashed()->where('email', $email)->first();

        // 삭제된 회원이라면 복구(Restore) 시킵니다.
        if ($user && $user->trashed()) {
            $user->restore();
            // 필요하다면 탈퇴 시 지워진 정보 재설정 로직 추가 가능
        }

        $type = 'login';
        if (! $user) {
            $type = 'register';

            // [변경] new User -> new $this->userModel
            $class = get_class($this->userModel);
            $user = new $class;

            $user->name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Guest';
            $user->email = $email;
            $user->password = null; //  소셜 로그인은 비밀번호 없음

            if ($auth_cfg->activate == 'auto' || $auth_cfg->activate == 'email') {
                $user->active = 1;
            }

            $user->save();

            $user->roles()->attach(Role::firstOrCreate(['name' => config('pondol-auth.roles.default_role')]));
            event(new Registered($user));
        }

        // 로그인 시간 기록 (App\Models\User의 fillable에 logined_at이 있어야 함)
        $user->logined_at = now();
        $user->save();

        // Trait에 있는 메서드라면 $this->storeToLog($user) 호출

        $this->findOrCreateSocialAccount($user, $provider, $socialUser);

        return ['user' => $user, 'type' => $type];
    }

    public function findOrCreateSocialAccount($user, $provider, $socialUser)
    {
        // SocialAccount 모델도 동적으로 처리할 수 있으나, 보통 User에 종속적이므로 그대로 둬도 무방하나
        // 완벽을 기한다면 SocialAccount::where... 그대로 사용

        $existAccount = SocialAccount::where('provider', $provider)->where('provider_id', $socialUser->getId())->first();

        if ($existAccount) {
            $existAccount->name = $socialUser->getName() ?? $socialUser->getNickname();
            $existAccount->avatar = $socialUser->getAvatar();
            $existAccount->token = $socialUser->token;
            if ($socialUser->refreshToken) {
                $existAccount->refresh_token = $socialUser->refreshToken;
            }
            $existAccount->save();
        } else {
            SocialAccount::create([
                'user_id' => $user->id,
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'email' => $socialUser->getEmail() ?? '',
                'avatar' => $socialUser->getAvatar(),
                'token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]);
        }
    }
}
