<?php

namespace Pondol\Auth\Http\Controllers;

use Crypt;
use Google2FA;
use Hash;
use Validator;
use DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
// use Illuminate\Support\Facades\Crypt;
use Pondol\Auth\Http\Requests\ValidateSecretRequest;
use Pondol\Auth\Models\User\User;
use Pondol\Auth\Notifications\sendEmailReset2fa;

use App\Http\Controllers\Controller;


use Carbon\Carbon;

class Google2FAPasswordResetLinkController extends Controller
{

  use SendsPasswordResetEmails;
	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

  protected function create() {
    // return view('pages/ko/auth/password_reset');
    return view('auth.templates.views.'.config('pondol-auth.template.user').'.google2fa.email-reset-link');
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' =>  ['required', 'email'],
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withInput()->withErrors($validator->errors());
      // return response()->json(['error'=>$validator->errors()->first()], 203);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return back()->withErrors(['email' => '등록되지 않은 이메일 입니다.']);
    }

    // $createToken = $user->createToken('2fa')->plainTextToken;
    // $token =   Hash::make($createToken);

    // DB::table('password_resets')->where('email', $user->email)->delete();
    // DB::table('password_resets')->insert(['email'=>$user->email, 'token'=>$token]);

    // echo 'token:'.$token.PHP_EOL;
    
    // return;

    print_r($user->email);

    $user->notify(new sendEmailReset2fa);
    echo "=====";
    // return back()->with(['status' => '등록된 이메일로 비밀번호 초기화 링크를 전송하였습니다.']);
    // return $status === Password::RESET_LINK_SENT
    // ? back()->with(['status' => __($status)])
    // : back()->withErrors(['email' => __($status)]);

  }

  public function reset2faForm(Request $request) {
    return view('auth.templates.views.'.config('pondol-auth.template.user').'.google2fa.forgot-2fa', compact('request'));
  }

  public function resetsfa(Request $request) {
    $request->validate([
      'token' => 'required',
      'email' => ['required', 'email'],
    ]);

    $reset = DB::table('password_resets')->where('email', $request->email)->first();


    if (Hash::check($request->token, $reset->token)) {
      // 토큰이 맞다면
      User::where('email', $request->email)->update(['google2fa_secret'=>null]);
      return redirect()->route('login');
    } else {
      return back()->withErrors(['token' => 'Token is invalid!']);
    }



    // return $status === Password::PASSWORD_RESET
    //   ? redirect()->route('login')->with('status', __($status))
    //   : back()->withErrors(['email' => [__($status)]]);
  }




}
