<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

use App\Models\Auth\User\User;
use App\Models\Auth\User\SocialAccount;
// use App\Models\CfgEvent;
use JWTAuth;
use GuzzleHttp\Client;

class LoginAppController extends Controller
{
    // 1. redirectToProvider() 구글에 로그인요청
    public function redirectToProvider($provider)
    {
		//리프레시 토큰을 가져오려면 옵션파라미터 'access_type'=>'offline', 'prompt'=>'consent' 으로 설정해줘야합니다.
        switch($provider) {
            case 'google':
                return Socialite::driver('google')->with(['access_type'=>'offline', 'prompt'=>'consent' ])->redirect();
                break;
        }

    }

    // 앱에서는 인앱브라우저를 통해서 수신된 토큰을 제공받아 처리한다.
    public function handleProviderAppCallback($provider, Request $request)
    {
        switch ($provider) {
            case 'kakao':
                // 1. code를 받는다.
                $code = $request->code;

                // echo 'code:'.$code;

                // 2. access token을 호출한다.
                $client = new \GuzzleHttp\Client();
                $endpoint = 'https://kauth.kakao.com/oauth/token';
                $form_params= [
                    'grant_type'=>'authorization_code',
                    // 'client_id'=>'89db3221a60ba7bcaa6be61bffa00ff5',
                    'client_id'=> \Config::get('services.kakao.client_id'),
                    'code'=>$code,
                    // 'redirect_uri'=> 'http://admin.saju.fxstar.co.kr/auth/social/kakao/login'
                    'redirect_uri'=> \Config::get('services.kakao.redirect')
                ];

                $response = $client->request('POST', $endpoint, ['form_params' => $form_params]);
                $res = json_decode($response->getBody(), true); // for json if error occur DB rollback
                $access_token = $res['access_token'];

                // 사용자 정보를 획득한다.
                $endpoint = 'https://kapi.kakao.com/v1/oidc/userinfo';
                $response = $client->request('GET', $endpoint, ['headers' => ['Authorization' => 'Bearer '.$access_token]]);
                $res = json_decode($response->getBody(), true); // for json if error occur DB rollback

                // [sub] => 2399840325 [nickname] => 폰돌 [email] => pondol@kakao.com [email_verified] => 1 [gender] => male [birthdate] => 0000-10-07
                // db 처리
                // 유저가 이미 회원인지 확인하는 메서드입니다.
                // $user = $this->findOrCreateUser($provider, $socialUser);

                $email = isset($res['email']) &&  $res['email'] ? $res['email'] : $res['sub'].'@kakao.com';
                $name = isset($res['nickname']) ? $res['nickname']: $res['sub'];
                $user = $this->manualUserCreate($name, $email);

                $provider_id = $res['sub'];
                $refresh_token = '';
                $name = isset($res['name']) ? $res['name'] : null; // 회원명과는 다름
                $email = isset($res['email']) ? $res['email']: null;

                // 회원 정보가 입력되면 social_accounts에도 입력을 처리한다.
                // $this->findOrCreateSocialAccount($user, $provider, $socialUser);
                // return $user;
                $this->manualSocialCreate($user, $provider, $provider_id, $name, $email, $refresh_token);

            	//$user뒤의 내용을 true로 설정하면 리멤버토큰(자동로그인)이 발급됩니다.
                \Auth::login($user, false);
                $userToken=JWTAuth::fromUser($user);

                $rtn = ['error'=>false, 'userToken'=>$userToken, 'user'=>$user, 'profile' => $user->profile];
                break;
            case 'naver':
                // 1. code를 받는다.
                $code = $request->code;

                $client = new \GuzzleHttp\Client();
                $endpoint = 'https://nid.naver.com/oauth2.0/token';
                $form_params= [
                    'grant_type'=>'authorization_code',
                    // 'client_id'=>'89db3221a60ba7bcaa6be61bffa00ff5',
                    'client_id'=> \Config::get('services.naver.client_id'),
                    'client_secret'=> \Config::get('services.naver.client_secret'),
                    'code'=>$code,
                    // 'redirect_uri'=> 'http://admin.saju.fxstar.co.kr/auth/social/kakao/login'
                    'redirect_uri'=> \Config::get('services.naver.redirect')
                ];

                $response = $client->request('POST', $endpoint, ['form_params' => $form_params]);
                $res = json_decode($response->getBody(), true); // for json if error occur DB rollback
                // $res = $res['response'];

                $access_token = $res['access_token'];


                // $access_token = $request->access_token;

                // echo 'code:'.$code;

                // 2. access token을 호출한다.
                $client = new \GuzzleHttp\Client();
                $endpoint = 'https://openapi.naver.com/v1/nid/me';
                // 사용자 정보를 획득한다.
                $response = $client->request('GET', $endpoint, ['headers' => ['Authorization' => 'Bearer '.$access_token]]);
                $res = json_decode($response->getBody(), true); // for json if error occur DB rollback

                $res = $res['response'];


                // [id] => xP80i53TWNl2a0q7YAPxlWc4UqnHJheQRo0HwVHaAic
                //     [gender] => M
                //     [email] => wangta69@naver.com
                //     [name] => 류영형
                $email = isset($res['email']) &&  $res['email'] ? $res['email'] : $res['id'].'@naver.com';
                $name = isset($res['nickname']) ? $res['nickname']: $res['id'];
                $user = $this->manualUserCreate($name, $email);

                $provider_id = $res['id'];
                $refresh_token = '';
                $name = isset($res['name']) ? $res['name'] : null; // 회원명과는 다름
                $email = isset($res['email']) ? $res['email']: null;

                // 회원 정보가 입력되면 social_accounts에도 입력을 처리한다.
                // $this->findOrCreateSocialAccount($user, $provider, $socialUser);
                // return $user;
                $this->manualSocialCreate($user, $provider, $provider_id, $name, $email, $refresh_token);

                //$user뒤의 내용을 true로 설정하면 리멤버토큰(자동로그인)이 발급됩니다.
                \Auth::login($user, false);
                $userToken=JWTAuth::fromUser($user);
                $rtn = ['error'=>false, 'userToken'=>$userToken, 'user'=>$user, 'profile' => $user->profile];
                break;
        }
        LOG::info(json_encode($rtn));
        return view('pages.kakao', [
            'rtn'=>json_encode($rtn)
        ]);
        // return response()->json(['error'=>false, 'info'=>$res, 'code'=>$code], 200);
    }

    /**
    * 웹에서 로그인 처리후 데이타를 받아서 처리
    */
    public function socialLogin(Request $request) {

        $provider = $request->provider;
        $provider_id = $request->provider_id;
        $name = $request->name;
        $name = $name ? $name:  $provider_id;

        $email = $request->email;
        $refresh_token = $request->refresh_token;
        $info = []; //기타 정보 입력

        switch($provider) { // 네이버는 별도 처리한다.
            case 'naver':
                    // age: this.naverApi.user.age,
                    // birthday: this.naverApi.user.birthday,
                    // birthyear: this.naverApi.user.birthyear,
                    // gender: this.naverApi.user.gender,
                    // email: this.naverApi.user.email,
                    // id: this.naverApi.user.id,
                    // name: this.naverApi.user.name,
                    // nickname: this.naverApi.user.nickname,
                    // profile_image: this.naverApi.user.profile_image

                    $birthday = $request->birthday; // mm-dd
                    $birthyear = $request->birthyear; // yyyy
                    $gender = $request->gender; // M W
                    $birthymd = '';
                    if ($birthday && $birthyear) {
                        $md = explode('-', $birthday);
                        $birthymd = $birthyear.$md[0].$md[1];
                    }

                    $info = ['gender'=>$gender, 'birthymd'=>$birthymd];

                break;
        }


        if (!$name) {
            $name = $provider_id;
        }
        if (!$email) {
            $email = $provider_id.'@'.$provider;
        }


        $user = $this->manualUserCreate($name, $email);

        // 회원 정보가 입력되면 social_accounts에도 입력을 처리한다.
        // $this->findOrCreateSocialAccount($user, $provider, $socialUser);
        // return $user;
        $this->manualSocialCreate($user, $provider, $provider_id, $name, $email, $refresh_token, $info);

        //$user뒤의 내용을 true로 설정하면 리멤버토큰(자동로그인)이 발급됩니다.

        \Auth::login($user, false);
        $userToken=JWTAuth::fromUser($user);

        $user->profile; // user에 profile을 덧 붙인다.
        return response()->json(['error'=>false, 'userToken'=>$userToken, 'user' => $user], 200);
    }

    /**
     * 이메일로 로그인 한 경우
     */
    public function emailLogin(Request $request) {
        $email = $request->email;
        $password = $request->password;

        if (\Auth::attempt([
            'email' => $request->email,
            'password' => $request->password])
        ){
            // 정상적으로 실행되면 authenticated 가 자동 실행
            $user = \Auth::user();
            $user->logined_at = date("Y-m-d H:i:s");
            $user->save();

            $userToken=JWTAuth::fromUser($user);
            $user->profile; //  = $user->profile; // profile을 덧 붙인다.
            return response()->json(['error'=>false, 'userToken'=>$userToken, 'user'=>$user], 200);
        }
        return response()->json(['error'=>'패스워드가 일치하지 않거나 존재하지 않는 회원입니다.'], 203);
    }

    /*
    * 회원정보를 받아서 생성한다.
    */
    private function manualUserCreate($name, $email) {

        $user = User::where('email', $email)->first();
        $firstResist = false;

        if (!$user) {
            $user = new User;
            $user->email = $email;
            $user->point = 0;
            $user->bonus = 0;

            $firstResist = true;
        }
        $user->name = $name;
        $user->logined_at = date('Y-m-d H:i:s');
        $user->save();

        if ($firstResist === true) {
            // 회원가입 이벤트를 가져와서 포인트를 입력한다.
            $event = CfgEvent::select('val')->where('key', 'resistPoint')->first();
            // echo 'event->val:', $event->val.PHP_EOL;
            if ($event->val != '0') {
                $valid = date('Y-m-d H:i:s', strtotime(' + 30 day'));
                \App\Services\BonusService::insertBonus($user, (int)$event->val, $valid, 'event', 'regist');
            }
        }
        //
        // $user = User::firstOrCreate([
        //     // 'name'  => $name, //이름가져오기
        //     'email' => $email, // 구글 이메일 가져오기
        // ]);
        // $user->name = $name;
        // $user->save();


        return $user;
    }

    private function manualSocialCreate($user, $provider, $provider_id, $name, $email, $refresh_token, $info = []) {
        $existAccount = SocialAccount::where('provider', $provider)->where('provider_id', $provider_id)->first();
        if($existAccount){
            $existAccount->name = $name;
            $existAccount->avatar = '';
            $existAccount->info = json_encode($info);
            // if($socialUser->refreshToken){
            //     $existAccount->refresh_token = $socialUser->refreshToken;
            // }
            $existAccount->save();
        }
        else{
            $socialAccount = SocialAccount::firstOrCreate([
                'user_id' => $user->id,
                'name'  => $name, //이름가져오기
                'provider' => $provider,
                'provider_id' => $provider_id, // 구글내부 아이디값 가져오기
                'email' => $email, // 구글 이메일 가져오기
                // 'avatar' =>$socialUser->getAvatar(), // 구글내 프로필 이미지
                'refresh_token'=> $refresh_token,
                'info' => json_encode($info)
            ]);
        }
    }

    public function logout() {
        $client = new \GuzzleHttp\Client();
        $endpoint = 'https://kapi.kakao.com/v1/user/logout';
        $access_token = 'neKjY5dsecfxvDfpRHffah0P3B4BmSpEKfQ-dugfCinJXgAAAYLJGk0R';
        $admin_key='6b10bcbc66661d82d7bccf0a48c6e7b3';

        $target_id = '12345678';
        $form_params= [
           //  'headers' => [
           //     'Authorization' => 'Bearer '.$access_token
           // ]
           'headers' => [
              'Authorization' => 'KakaoAK '.$admin_key
          ],
          'target_id_type'=>'user_id',
          'target_id'=>$target_id
        ];

        $response = $client->request('POST', $endpoint, ['form_params' => $form_params]);
        // print_r($response->getBody()->getContents());
        $res = json_decode($response->getBody(), true); // for json if error occur DB rollback
    }



// 2. handleProviderCallback () 로그인한 후에 이미 만들어진 아이디인지 확인후 처리
    public function handleProviderCallback($provider)
    {
        switch($provider) {
            case 'google':
                //구글에서 로그인확인후 정보 제공
                $socialUser = Socialite::driver('google')->user();
                break;
        }

        // 유저가 이미 회원인지 확인하는 메서드입니다.
        $user = $this->findOrCreateUser($provider, $socialUser);

    	//$user뒤의 내용을 true로 설정하면 리멤버토큰(자동로그인)이 발급됩니다.
        \Auth::login($user, false);

        $userToken=JWTAuth::fromUser($user);

    	//토큰을 활용하기위해 로컬에 저장해도 되고 세션에 저장하거나 쿠키에 저장해서 활용할 수 있겠습니다.
        // return redirect('/');

        // 페이지를 리다이렉팅되는 페이지를 index로 구성하고 토큰값을 전달합니다.
        // return view('index')->with('access_token', $socialUser->token);
        return response()->json([
            'error'=>false,
            'userToken' => $userToken,
            'user'=>[
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ], 200);//500, 203
    }

    /**
    * 아이디 존재하지않으면 새로 생성 하는 메서드
    * 먼저 email을 기준으로 현재 회원정보가 있는지 확인 (없으면 새로운 회원을 만들어 준다.)
    */
    public function findOrCreateUser($provider, $socialUser){

        $user = User::firstOrCreate([
            'name'  => $socialUser->getName(), //이름가져오기
            'email' => $socialUser->getEmail(), // 구글 이메일 가져오기
        ]);

        // 회원 정보가 입력되면 social_accounts에도 입력을 처리한다.
        $this->findOrCreateSocialAccount($user, $provider, $socialUser);
        return $user;
    }

    public function findOrCreateSocialAccount($user, $provider, $socialUser){
        $existAccount = SocialAccount::where('provider', $provider)->where('provider_id', $socialUser->id)->first();
        if($existAccount){
            $existAccount->name = $socialUser->getName();
            $existAccount->avatar = $socialUser->getAvatar();
            if($socialUser->refreshToken){
                $existAccount->refresh_token = $socialUser->refreshToken;
            }
            $existAccount->save();
        }
        else{
            $socialAccount = SocialAccount::firstOrCreate([
                'user_id' => $user->id,
                'name'  => $socialUser->getName(), //이름가져오기
                'provider' => $provider,
                'provider_id'  => $socialUser->getId(), // 구글내부 아이디값 가져오기
                'email' => $socialUser->getEmail(), // 구글 이메일 가져오기
                'avatar' =>$socialUser->getAvatar(), // 구글내 프로필 이미지
                'refresh_token'=> $socialUser->refreshToken
            ]);
        }
    }
}
