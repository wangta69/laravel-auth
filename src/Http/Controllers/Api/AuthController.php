<?php

namespace Pondol\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Pondol\Auth\Models\User\SocialAccount;

class AuthController extends Controller
{
    /**
     * 1. 소셜 로그인 시작 (리다이렉트)
     * GET /api/v1/auth/social/{provider}/redirect
     */
    public function redirectToProvider(Request $request, $provider)
    {
        // 앱 전용 리다이렉트 URI 가져오기
        $redirectKey = strtoupper($provider).'_APP_REDIRECT_URI';
        $appRedirectUri = env($redirectKey);

        // .redirectUrl() 을 사용하여 실시간으로 콜백 주소를 변경합니다.
        return Socialite::driver($provider)
            ->stateless()
            ->redirectUrl($appRedirectUri) // 이 부분이 핵심입니다!
            ->redirect();
    }

    /**
     * 2. 소셜 로그인 콜백 처리 (핵심)
     * GET /api/v1/auth/social/{provider}/callback
     */
    public function handleSocialCallback(Request $request, $provider)
    {
        try {
            // 소셜라이트가 'code'를 가지고 자동으로 access_token 교체 및 유저 정보 획득을 수행합니다.
            // 기존에 Guzzle로 길게 작성했던 getKakaoUserData 로직이 이 한 줄로 대체됩니다.
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // 이메일이 없는 경우 처리 (기존 로직 유지)
            $email = $socialUser->getEmail() ?? $socialUser->getId().'@'.$provider.'.social';
            $name = $socialUser->getName() ?? $socialUser->getNickname() ?? $socialUser->getId();

            // 1. 기존에 사용하시던 유저 생성 로직 호출
            $user = $this->manualUserCreate($name, $email);

            // 2. 기존에 사용하시던 소셜 계정 연동 로직 호출
            $this->manualSocialCreate(
                $user,
                $provider,
                $socialUser->getId(),
                $name,
                $email,
                $socialUser->refreshToken ?? ''
            );

            // 3. Sanctum 토큰 발행
            $token = $user->createToken('mobile-app')->plainTextToken;

            // ------------------------------------------------------------
            // [중요] 로컬 테스트 환경을 위한 리다이렉트 처리
            // ------------------------------------------------------------
            // f=app 파라미터가 있거나 API 요청인 경우 프론트엔드(Angular) 주소로 토큰을 실어서 보냅니다.
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:4200');

            return redirect($frontendUrl.'/auth/social-success?token='.$token);

        } catch (\Exception $e) {
            Log::error("Social Login Error [{$provider}]: ".$e->getMessage());

            return response()->json(['error' => '소셜 로그인 중 오류가 발생했습니다.'], 500);
        }
    }

    /**
     * 3. 일반 이메일 로그인
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => '패스워드가 일치하지 않거나 존재하지 않는 회원입니다.'], 422);
        }

        $user->logined_at = now();
        $user->save();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user->load('profile'),
        ]);
    }

    /**
     * [기존 로직] 유저 생성 및 포인트 지급
     */
    private function manualUserCreate($name, $email)
    {
        $user = User::where('email', $email)->first();
        $isFirst = false;

        if (! $user) {
            $user = new User;
            $user->email = $email;
            $user->point = 0;
            $user->bonus = 0;
            $isFirst = true;
        }
        $user->name = $name;
        $user->logined_at = now();
        $user->save();

        // 회원가입 포인트 지급 등 기존 로직 추가 위치
        if ($isFirst) {
            // CfgEvent 등을 활용한 기존 포인트 로직을 여기에 그대로 넣으시면 됩니다.
        }

        return $user;
    }

    /**
     * [기존 로직] 소셜 계정 정보 업데이트 및 생성
     */
    private function manualSocialCreate($user, $provider, $provider_id, $name, $email, $refresh_token, $info = [])
    {
        SocialAccount::updateOrCreate(
            ['provider' => $provider, 'provider_id' => $provider_id],
            [
                'user_id' => $user->id,
                'name' => $name,
                'email' => $email,
                'refresh_token' => $refresh_token,
                'info' => json_encode($info),
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'success', 'message' => 'Logged out']);
    }
}
