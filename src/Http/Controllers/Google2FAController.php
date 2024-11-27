<?php

namespace Pondol\Auth\Http\Controllers;

use Crypt;
use Google2FA;
use Illuminate\Http\Request;
use Pondol\Auth\Http\Requests\ValidateSecretRequest;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

class Google2FAController extends Controller
{

	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function setting(Request $request)
	{
		return view('auth.templates.views.'.config('pondol-auth.template.user').'.google2fa.welcome');
	}


	/**
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function enableTwoFactor(Request $request)
	{
			//generate new secret

		$secret = $this->generateSecret();


		//get user
		$user = $request->user();

		//encrypt and then save secret
		$user->google2fa_secret = Crypt::encrypt($secret);
		$user->save();


		$imageDataUri = Google2FA::getQRCodeInline(
			$request->getHttpHost(),
			$user->email,
			$secret,
			200
		);

		return view('auth.templates.views.'.config('pondol-auth.template.user').'.google2fa.enableTwoFactor', ['image' => $imageDataUri,
			'secret' => $secret]);

	}

	/**
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function disableTwoFactor(Request $request)
	{
		$user = $request->user();

		//make secret column blank
		$user->google2fa_secret = null;
		$user->save();
		return view('auth.templates.views.'.config('pondol-auth.template.user').'.google2fa.welcome');
	}

	/**
	 * Generate a secret key in Base32 format
	 *
	 * @return string
	 */
	private function generateSecret()
	{
		return Google2FA::generateSecretKey();
	}


	// 2fa 페이지 호출
	public function getValidateToken()
	{
		if (session('2fa:user:id')) {
			return view('auth.templates.views.'.config('pondol-auth.template.user').'.google2fa.validate');
		}
		return redirect('login');
	}

public function postValidateToken(ValidateSecretRequest $request)
  {
    $userId = $request->session()->pull('2fa:user:id'); // 기존 세션값에서 userId 가져옮
    $key = $userId . ':' . $request->totp; // userId와 입력값(totp: google 2fa 값)을 이용하여 키를 만들어 준다.

    //use cache to store token to blacklist
    \Cache::add($key, true, 4); // 키값을 추가한다.

    //login and redirect user
    $user = \Auth::loginUsingId($userId); // 로그인 처리

    return redirect()->intended('/');
  }

}
