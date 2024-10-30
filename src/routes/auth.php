<?php

// Route::group(['namespace' => 'App\Http\Controllers\Auth', 'middleware' => ['web']], function () { // 'as' => 'auth.', 
// });
Route::get('register', 'RegisterController@create')->name('register')->middleware('guest');
Route::post('register', 'RegisterController@store')->middleware('guest');
Route::get('register/agreement', 'RegisterController@agreement')->name('register.agreement')->middleware('guest');
Route::post('register/agreement', 'RegisterController@agreementstore')->middleware('guest');
Route::get('register/success', 'RegisterController@success')->name('register.success');
Route::get('login', 'AuthenticatedSessionController@create')->name('login')->middleware('guest');
Route::post('login', 'AuthenticatedSessionController@store')->middleware('guest');
Route::get('logout', 'AuthenticatedSessionController@destroy')->name('logout')->middleware('auth');
Route::get('auth/validation/email/{email}', 'CommonController@validationEmail')->name('validation.email');

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
Route::get('verify-email/{id}/{hash}', 'VerifyEmailController@__invoke')->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
Route::post('email/verification-notification', 'EmailVerificationNotificationController@store')->middleware('auth', 'throttle:6,1')->name('verification.send');
