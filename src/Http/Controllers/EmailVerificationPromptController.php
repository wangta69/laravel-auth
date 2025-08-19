<?php

namespace Pondol\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
  /**
   * Display the email verification prompt.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return mixed
   */
  public function __invoke(Request $request)
  {
    $defaultPath = config('pondol-auth.default_redirect_path', '/');
    return $request->user()->hasVerifiedEmail()
      ? redirect()->intended($defaultPath)
      : view(auth_theme('user').'.verify-email');
  }
}
