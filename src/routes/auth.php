<?php

use Illuminate\Support\Facades\Route;


// 'prefix' => 'market', 
Route::group(['as' => 'auth.', 'namespace' => 'App\Http\Controllers\Auth', 'middleware' => ['web']], function () {
  Route::get('/', 'MainController@index')->name('main');

  // Auth
  Route::get('register', 'RegisterController@create')->name('register')->middleware('guest');
  Route::post('register', 'RegisterController@store')->middleware('guest');
  Route::get('register/agreement', 'RegisterController@agreement')->name('register.agreement')->middleware('guest');
  Route::post('register/agreement', 'RegisterController@agreementstore')->middleware('guest');
  Route::get('register/success', 'RegisterController@success')->name('register.success');
  Route::get('login', 'LoginController@create')->name('login')->middleware('guest');
  Route::post('login', 'LoginController@store')->middleware('guest');
  Route::get('logout', 'LoginController@destroy')->name('logout');
  Route::get('validation/email/{email}', 'CommonController@validationEmail')->name('validation.email');



  Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
  Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
  Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
  Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

  Route::get('cancel-account', 'DestroyController@delete')->name('cancel.account')->middleware('auth');
  Route::delete('cancel-account', 'DestroyController@destroy')->middleware('auth');
  Route::get('cancel-account/success', 'DestroyController@success')->name('cancel.account.success');

  // 로그인창 가져오기위한 라우터
  Route::get('/auth/social/{provider}/redirect', 'Social\LoginController@redirectToProvider');

  // 로그인 인증후 데이터를 제공해주는 라우터
  Route::get('/auth/social/{provider}/callback', 'Social\LoginController@handleProviderCallback');
  Route::get('/auth/social/{provider}/login', 'Social\LoginAppController@handleProviderAppCallback');
  Route::get('/auth/social/{provider}/logout', 'Social\LoginAppController@logout');

  // 마이페이지
  // 개인정보
  Route::get('auth/user', 'Mypage\UserController@index')->name('mypage.user');
  Route::put('auth/user/update/email', 'Mypage\UserController@updateEmail')->name('mypage.user.update.email');
  Route::put('auth/user/update/password', 'Mypage\UserController@updatePassword')->name('mypage.user.update.password');
  Route::put('auth/user/update/mobile', 'Mypage\UserController@updateMobile')->name('mypage.user.update.mobile');
});
