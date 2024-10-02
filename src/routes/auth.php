<?php

use Illuminate\Support\Facades\Route;


// 'prefix' => 'market', 
Route::group(['as' => 'market.', 'namespace' => 'App\Http\Controllers\Auth', 'middleware' => ['web']], function () {
  Route::get('/', 'MainController@index')->name('main');

  // Auth
  Route::get('register', 'Auth\RegisterController@create')->name('register')->middleware('guest');
  Route::post('register', 'Auth\RegisterController@store')->middleware('guest');
  Route::get('register/agreement', 'Auth\RegisterController@agreement')->name('register.agreement')->middleware('guest');
  Route::post('register/agreement', 'Auth\RegisterController@agreementstore')->middleware('guest');
  Route::get('register/success', 'Auth\RegisterController@success')->name('register.success');
  Route::get('login', 'Auth\LoginController@create')->name('login')->middleware('guest');
  Route::post('login', 'Auth\LoginController@store')->middleware('guest');
  Route::get('logout', 'Auth\LoginController@destroy')->name('logout');
  Route::get('validation/email/{email}', 'CommonController@validationEmail')->name('validation.email');



  Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
  Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
  Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
  Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

  Route::get('cancel-account', 'Auth\DestroyController@delete')->name('cancel.account')->middleware('auth');
  Route::delete('cancel-account', 'Auth\DestroyController@destroy')->middleware('auth');
  Route::get('cancel-account/success', 'Auth\DestroyController@success')->name('cancel.account.success');

  // 로그인창 가져오기위한 라우터
  Route::get('/auth/social/{provider}/redirect', 'Auth\Social\LoginController@redirectToProvider');

  // 로그인 인증후 데이터를 제공해주는 라우터
  Route::get('/auth/social/{provider}/callback', 'Auth\Social\LoginController@handleProviderCallback');
  Route::get('/auth/social/{provider}/login', 'Auth\Social\LoginAppController@handleProviderAppCallback');
  Route::get('/auth/social/{provider}/logout', 'Auth\Social\LoginAppController@logout');

  // 마이페이지
  // 개인정보
  Route::get('auth/user', 'Mypage\UserController@index')->name('mypage.user');
  Route::put('auth/user/update/email', 'Mypage\UserController@updateEmail')->name('mypage.user.update.email');
  Route::put('auth/user/update/password', 'Mypage\UserController@updatePassword')->name('mypage.user.update.password');
  Route::put('auth/user/update/mobile', 'Mypage\UserController@updateMobile')->name('mypage.user.update.mobile');
});
