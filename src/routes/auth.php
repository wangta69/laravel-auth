<?php
Route::get('register', 'RegisterController@create')->name('register')->middleware('guest');
Route::post('register', 'RegisterController@store')->middleware('guest');
Route::get('register/agreement', 'RegisterController@agreement')->name('register.agreement')->middleware('guest');
Route::post('register/agreement', 'RegisterController@agreementstore')->middleware('guest');
Route::get('register/success', 'RegisterController@success')->name('register.success');
Route::get('login', 'AuthenticatedSessionController@create')->name('login')->middleware('guest');
Route::post('login', 'AuthenticatedSessionController@store')->middleware('guest');
Route::get('logout', 'AuthenticatedSessionController@destroy')->name('logout')->middleware(['auth', 'bypassverify']);
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

Route::get('verify-email', 'EmailVerificationPromptController@__invoke')->name('verification.notice')->middleware(['auth', 'bypassverify']);
Route::get('verify-email/{id}/{hash}', 'VerifyEmailController@__invoke')->middleware(['auth', 'signed', 'bypassverify', 'throttle:6,1'])->name('verification.verify');
Route::post('email/verification-notification', 'EmailVerificationNotificationController@store')->middleware('auth', 'bypassverify', 'throttle:6,1')->name('verification.send');


Route::get('/user', 'UserController@profile')->name('user.profile')->middleware(['auth']); // , 'verified'
Route::get('/user/edit', 'UserController@edit')->name('user.edit')->middleware('auth');
Route::put('/user/edit', 'UserController@update')->middleware('auth');
Route::get('/user/password', 'UserController@changePassword')->name('user.change-password')->middleware('auth');
Route::put('/user/password', 'UserController@updatePassword')->middleware('auth');

// 2fa
Route::get('2fa/setting', 'Google2FAController@setting')->name('2fa.setting');
Route::get('2fa/enable', 'Google2FAController@enableTwoFactor')->name('2fa.enable');
Route::get('2fa/disable', 'Google2FAController@disableTwoFactor')->name('2fa.disable');

Route::get('/2fa/validate', 'Google2FAController@getValidateToken')->name('2fa.validate');
Route::post('/2fa/validate', 'Google2FAController@postValidateToken');

Route::get('forgot-2fa', 'Google2FAPasswordResetLinkController@create')->name('2fa.request');
Route::post('forgot-2fa', 'Google2FAPasswordResetLinkController@store')->name('2fa.email');
Route::get('reset-2fa/{token}', 'Google2FAPasswordResetLinkController@reset2faForm')->name('2fa.reset');
Route::post('reset-2fa', 'Google2FAPasswordResetLinkController@resetsfa')->name('2fa.cleare');
