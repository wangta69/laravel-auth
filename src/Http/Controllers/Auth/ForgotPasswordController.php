<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

use Illuminate\Support\Str;

use App\Models\Auth\User\User;
use App\Models\CodeRequest;
use App\Services\SmsService;
// use App\Services\LocaleService;

class ForgotPasswordController extends Controller
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

  protected function showLinkRequestForm() {
    // return view('pages/ko/auth/password_reset');
    return view('auth.'.config('market.template.auth.theme').'.forgot-password');
  }

  public function sendResetLinkEmail(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' =>  ['required', 'email'],
    ]);

    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()->first()], 203);
    }

      // We will send the password reset link to this user. Once we have attempted
      // to send the link, we will examine the response then see the message we
      // need to show to the user. Finally, we'll send out a proper response.

      
    $status = $this->broker()->sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
    ? back()->with(['status' => __($status)])
    : back()->withErrors(['email' => __($status)]);

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
