<?php

namespace Pondol\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Pondol\Auth\Notifications\sendEmailVerificationNotification;

class EmailVerificationNotificationController extends Controller
{
  /**
   * Send a new email verification notification.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request)
  {
    if ($request->user()->hasVerifiedEmail()) {
      return redirect()->intended(RouteServiceProvider::HOME);
    }

    $request->user()->notify(new sendEmailVerificationNotification);

    return back()->with('status', 'verification-link-sent');
  }
}
