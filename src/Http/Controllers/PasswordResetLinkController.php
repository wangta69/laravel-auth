<?php

namespace Pondol\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

use Illuminate\Support\Str;

use Pondol\Auth\Models\User\User;

use Pondol\Auth\Notifications\sendEmailResetPasswordToken;
// use App\Services\LocaleService;

class PasswordResetLinkController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Password Reset Controller
  |--------------------------------------------------------------------------
  |
  | This controller is responsible for handling password reset emails and
  | includes a trait which assists in sending these notifications from
  | your application to your users. Feel free to explore this trait.
  |
  */

  use SendsPasswordResetEmails;

  public function __construct() // OpenSSLCryptoService $cryptoSvc
  {
  }

  protected function create() {
    // return view('pages/ko/auth/password_reset');
    return view('auth.templates.views.'.config('pondol-auth.template.user').'.forgot-password');
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' =>  ['required', 'email'],
    ]);

    // $validator =$request->validate([
    //   'email' => ['required', 'email'],
    // ]);

    if ($validator->fails()) {
      return redirect()->back()->withInput()->withErrors($validator->errors());
      // return response()->json(['error'=>$validator->errors()->first()], 203);
    }

      // We will send the password reset link to this user. Once we have attempted
      // to send the link, we will examine the response then see the message we
      // need to show to the user. Finally, we'll send out a proper response.

    // $status = Password::sendResetLink(
    // $status = $this->broker()->sendResetLink(
    //   $request->only('email')
    // );
    $user = User::where('email', $request->email)->first();
    if (!$user) {
      return back()->withErrors(['email' => '등록되지 않은 이메일 입니다.']);
    }


    $user->notify(new sendEmailResetPasswordToken);
    return back()->with(['status' => '등록된 이메일로 비밀번호 초기화 링크를 전송하였습니다.']);
    // return $status === Password::RESET_LINK_SENT
    // ? back()->with(['status' => __($status)])
    // : back()->withErrors(['email' => __($status)]);

  }

  public function resetPassword(Request $request) {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function (User $user, string $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

          $user->save();

          event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
      ? redirect()->route('login')->with('status', __($status))
      : back()->withErrors(['email' => [__($status)]]);
  }

}
