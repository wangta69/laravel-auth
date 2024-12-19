<?php

namespace Pondol\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
  /**
   * Display the password reset view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function create(Request $request)
  {
    // return view('auth.reset-password', ['request' => $request]);
    return view('auth.templates.views.'.config('pondol-auth.template.user').'.reset-password', compact('request'));
  }

  /**
   * Handle an incoming new password request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request)
  {
    $request->validate([
      'token' => ['required'],
      'email' => ['required', 'email'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // $validator = Validator::make($request->all(), [
    //   'email' => ['required', 'string', 'email'],
    //   'password' => ['required', 'string'],
    // ]);

    // if ($validator->fails()) {
    //   return redirect()->back()
    //   ->withErrors($validator->errors())
    //   ->withInput($request->except('password'));
    // }

    // Here we will attempt to reset the user's password. If it is successful we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise we will parse the error and return the response.
    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user) use ($request) {
        $user->forceFill([
          'password' => Hash::make($request->password),
          'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));
      }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error we can
    // redirect them back to where they came from with their error message.

    // session()->remove('url.intended'); // 로긴 페이지에서 previous 로 가는 것을 막음 
    return $status == Password::PASSWORD_RESET
      ? redirect()->route('login')->with('status', __($status))
      : back()->withInput($request->only('email'))
        ->withErrors(['email' => __($status)]);
  }
}
