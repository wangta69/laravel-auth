<?php

namespace Pondol\Auth\Http\Controllers;


use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Pondol\Common\Facades\JsonKeyValue;
use App\Http\Controllers\Controller;

class VerifyEmailController extends Controller
{
  /**
   * Mark the authenticated user's email address as verified.
   *
   * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(EmailVerificationRequest $request)
  {

    $auth_cfg = JsonKeyValue::getAsJson('auth');
    $defaultPath = config('pondol-auth.default_redirect_path', '/');

    if ($request->user()->hasVerifiedEmail()) {
      return redirect()->intended($defaultPath.'?verified=1');
    }

    if ($request->user()->markEmailAsVerified()) {
      event(new Verified($request->user()));

      if($auth_cfg->activate == "email") {
        $request->user()->active = 1;
        $request->user()->save();
      }
    }

    return redirect()->intended($defaultPath.'?verified=1');
  }
}
