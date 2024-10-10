<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::group(['namespace' => 'App\Http\Controllers\Auth', 'middleware' => ['web']], function () { // 'as' => 'auth.', 
  // Route::get('/', function() {})->name('main');
  // Route::get('route-url', 'Services\ServiceController@routeUrl');
  Route::get('auth/route-url', function (Request $request) {
    try {
      return route($request->name, $request->params);
    } catch (\Exception $e) {
   
    }
  });


  // route-url

  // Auth
  Route::get('register', 'RegisterController@create')->name('register')->middleware('guest');
  Route::post('register', 'RegisterController@store')->middleware('guest');
  Route::get('register/agreement', 'RegisterController@agreement')->name('register.agreement')->middleware('guest');
  Route::post('register/agreement', 'RegisterController@agreementstore')->middleware('guest');
  Route::get('register/success', 'RegisterController@success')->name('register.success');
  Route::get('login', 'AuthenticatedSessionController@create')->name('login')->middleware('guest');
  Route::post('login', 'AuthenticatedSessionController@store')->middleware('guest');
  Route::get('logout', 'AuthenticatedSessionController@destroy')->name('logout')->middleware('auth');
  Route::get('validation/email/{email}', 'CommonController@validationEmail')->name('validation.email');



  // Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
  // Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
  // Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
  // Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

  Route::get('forgot-password', 'PasswordResetLinkController@create')->name('password.request');
  Route::post('forgot-password', 'PasswordResetLinkController@store')->name('password.email');
  Route::get('reset-password/{token}', 'NewPasswordController@create')->name('password.reset');
  Route::post('reset-password', 'NewPasswordController@store')->name('password.update');


  Route::get('cancel-account', 'DestroyController@delete')->name('cancel.account')->middleware('auth');
  Route::delete('cancel-account', 'DestroyController@destroy')->middleware('auth');
  Route::get('cancel-account/success', 'DestroyController@success')->name('cancel.account.success');

  // 로그인창 가져오기위한 라우터
  Route::get('/auth/social/{provider}/redirect', 'Social\AuthenticatedSessionController@redirectToProvider');

  // 로그인 인증후 데이터를 제공해주는 라우터
  Route::get('/auth/social/{provider}/callback', 'Social\AuthenticatedSessionController@handleProviderCallback');
  Route::get('/auth/social/{provider}/login', 'Social\LoginAppController@handleProviderAppCallback');
  Route::get('/auth/social/{provider}/logout', 'Social\LoginAppController@logout');


  Route::get('verify-email', 'EmailVerificationPromptController@__invoke')->name('verification.notice')->middleware('auth');
  // Route::get('verify-email/{id}/{hash}', 'VerifyEmailController@__invoke')->name('verification.verify');
  Route::get('verify-email/{id}/{hash}', 'VerifyEmailController@__invoke')->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
  Route::post('email/verification-notification', 'EmailVerificationNotificationController@store')->middleware('auth', 'throttle:6,1')->name('verification.send');

  // 마이페이지
  // 개인정보
  // Route::get('auth/user', 'Mypage\UserController@index')->name('mypage.user');
  // Route::put('auth/user/update/email', 'Mypage\UserController@updateEmail')->name('mypage.user.update.email');
  // Route::put('auth/user/update/password', 'Mypage\UserController@updatePassword')->name('mypage.user.update.password');
  // Route::put('auth/user/update/mobile', 'Mypage\UserController@updateMobile')->name('mypage.user.update.mobile');
});
/*
Route::middleware('guest')->group(function () {
  Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
  Route::post('register', [RegisteredUserController::class, 'store']);
  Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
  Route::post('login', [AuthenticatedSessionController::class, 'store']);
  Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
  Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
  Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
  Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
  Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
  Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
  Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
  Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
  Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
  Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
*/
