<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth/admin', 'as' => 'auth.admin.', 'namespace' => 'App\Http\Controllers\Auth\Admin', 'middleware' => ['web', 'admin']], function () {

    // 회원관리
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('users', 'UserController@index')->name('users');
    // Route::get('user/create', 'UserController@create')->name('user.create');
    // Route::post('user/create', 'UserController@store');

    Route::get('user/{user}', 'UserController@show')->name('user');
    // Route::get('user/join', 'UserController@join')->name('user.join-list');
    Route::get('user/{user}/edit', 'UserController@edit')->name('user.edit');
    Route::put('user/{user}', 'UserController@update');
    Route::put('user/{user_id}/active/{active}', 'UserController@updateActive')->name('active.update'); // 
    Route::get('user/{user_id}/active/{active}', 'UserController@updateActive'); // for test
    Route::delete('user/{user}', 'UserController@destroy')->name('user.destroy');
    Route::get('user/login/{user}', 'UserController@login')->name('user.login'); // 현재 회원으로 로그인

    Route::get('config', 'ConfigController@index')->name('config'); // 관리
    Route::put('config', 'ConfigController@update')->name('config.update'); 
});
