<?php

namespace App\Http\Controllers\Auth\Traits\Admin;

use Validator;

use App\Models\Auth\User\User as mUser;

trait User 
{


  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function _index($request)
  {
    $sk = $request->sk;
    $sv = $request->sv;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $active = $request->active;

    // $users = app('App\Models\Auth\User\User')
    $users = mUser::select(
      'users.id', 'users.email', 'users.name', 'users.active', 'users.point', 'users.logined_at', 'users.created_at', 'users.deleted_at'
    );

    if ($sv) {
      if ($sk == 'users.mobile') {
        $sv = (int)preg_replace("/[^0-9]+/", "", $sv);;
      }
      $users = $users->where($sk, 'like', '%' . $sv . '%');
    }

    if ($from_date) {
      if (!$to_date) {
        $to_date = date("Y-m-d");
      }
      $users = $users->where(function ($q) use($from_date, $to_date) {
        $q->whereRaw("DATE(users.created_at) >= '".$from_date."' AND DATE(users.created_at)<= '".$to_date."'" );
      });
    }

    if($active) {
      $users = $users->whereIn('users.active', $active);
    }
    return $users;
  }


  /**
   * Display the specified resource.
   *
   * @param User $user
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function _show($user)
  {
    $user = mUser::withTrashed()->find($user);
    return $user;
  }


    /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param User $user
   * @return mixed
   */
  public function _update($request, $user)
  {
    $result = new \stdClass();
    $validator = Validator::make($request->all(), [
      'name' => 'required|max:50',
    //    'email' => 'required|email|max:255'
    ]);


    $validator->sometimes('password', 'min:8|confirmed', function ($input) {
      return $input->password;
    });


    if ($validator->fails()) {
      $result->error = 'validator';
      $result->validator = $validator;
      return $result;
    }


    $user->name = $request->get('name');

    if ($request->has('password') && trim($request->password)) {
      $user->password = \Hash::make($request->password);
    }

    if ($request->has('security_password') && trim($request->security_password)) {
      $user->security_password = \Hash::make($request->security_password);
    }
    $user->save();

    //roles
    if ($request->has('roles')) {
      $user->roles()->detach();
      if ($request->get('roles')) {
        $user->roles()->attach($request->get('roles'));
      }
    }
    $result->error = false;
    return $result;

  }
  

  /**
   * 회원 생성
   */
  public function _store($request){
      $request->mobile = str_replace('-','', $request->mobile);
      $validator = Validator::make($request->all(), [
        'email' => ['required', 'string', 'email', 'unique:users'],
        'name' => ['required', 'string', 'min:2', 'max:10'], // , 'unique:users'
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    //    'security_password' => ['required', 'numeric', 'digits:4', 'confirmed'],
        'mobile' => ['required', 'unique:users'],
      ]);

      if ($validator->fails()) return redirect()->back()->withInput()->withErrors($validator->errors());

      $user = new mUser;
      $user->email = $request->get('email');
      $user->name = $request->get('name');
      $user->active = $request->get('active', 0);
      $user->password = \Hash::make($request->password);

      $user->save();
      // $user->notify(new CountChanged('add', 'users'));
      //roles
      if ($request->has('roles')) {
        $user->roles()->detach();

        if ($request->get('roles')) {
          $user->roles()->attach($request->get('roles'));
        }
      }

      return;
  }




  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function _destroy($user)//
  {
    return $user->delete();
  }

  /**
   * @param Number $active :  0: 미인가, 1: 인가, 2: 차단, 8: 탈퇴신청, 9: 탈퇴
   */
  public function _updateActive($user_id, $active) { // User $user로 받을 경우 deleted_at이 있으므로 찾지 못하는 에러 발생

    $user = mUser::where('id', $user_id)->withTrashed()->first();

    if ($active != 9) {
      $user->deleted_at = null;
    } else {
      $deactivate = \App\Models\Auth\User\UserDeactivate::where('user_id', $user_id)->first();
      $deactivate->delete();
    }

    $user->active = $active;
    $user->save();

    if ($active == 9) {
      $user->delete();
    }

    return;
  }

  /**
   * 회원정보로 강제 로그인 (메뉴단에서는 현재 숨기고 url로 바로 접근)
   * /admin/user/login/{user}
   */
  public function _login($user) {
    $auth = \Illuminate\Support\Facades\Auth::user();
    if($auth->hasRole("administrator")){
      \Illuminate\Support\Facades\Auth::guard()->login($user);
      return true;
    }
    return false;
  }

}
