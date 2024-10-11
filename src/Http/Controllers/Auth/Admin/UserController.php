<?php

namespace App\Http\Controllers\Auth\Admin;

use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\Auth\User\User;
use App\Models\Auth\Role\Role;
use App\Http\Controllers\Auth\Traits\Admin\User as tUser;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

  use tUser;

  public function __construct()
  {

  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $users = $this->_index($request);

    $users = $users->withTrashed()->orderBy('id', 'desc')->paginate(20)->appends(request()->query());

    return view('auth.admin.users.index', [
      'users' => $users
    ]);
  }

    /**
   * 회원 등록 폼
   */
  public function create()
  {
    return view('auth.admin.users.create', [
      'roles' => Role::get(),
    ]);
  }

  /**
   * 회원 생성
   */
  public function store(Request $request){
    $request->mobile = str_replace('-','', $request->mobile);
    $validator = Validator::make($request->all(), [
      'email' => ['required', 'string', 'email', 'unique:users'],
      'name' => ['required', 'string', 'min:2', 'max:10'], // , 'unique:users'
      'password' => ['required', 'string', 'min:8', 'confirmed'],
      'mobile' => ['required', 'unique:users'],
    ]);

    if ($validator->fails()) return redirect()->back()->withInput()->withErrors($validator->errors());

    $user = new User;
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

    return redirect()->intended(route('auth.admin.users'));
  }


  /**
   * Display the specified resource.
   *
   * @param User $user
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function show($user)
  {
    $user = $this->_show($user);
    return view('auth.admin.users.show', [
      'user' => $user,
    ]);
  }

  /**
   * 회원정보 수정 폼
   *
   * @param User $user
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
    // 총레벨
    return view('auth.admin.users.edit', [
      'user' => $user,
      'roles' => Role::get(),
    ]);
  }

    /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param User $user
   * @return mixed
   */
  public function update(Request $request, User $user)
  {
    $result = $this->_update($request, $user);

    if($result->error == 'validator') {
      return redirect()->back()->withErrors($result->validator->errors());
    }

    return redirect()->intended(route('auth.admin.user', [$user->id]));
  }
  

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)//
  {
    $result = $this->destroy($user);
  }

  /**
   * @param Number $active :  0: 미인가, 1: 인가, 2: 차단, 8: 탈퇴신청, 9: 탈퇴
   */
  public function updateActive($user_id, $active) { // User $user로 받을 경우 deleted_at이 있으므로 찾지 못하는 에러 발생
    $result = $this->_updateActive($user_id, $active);
    return response()->json(['error'=>false], 200);
  }

  /**
   * 회원정보로 강제 로그인 (메뉴단에서는 현재 숨기고 url로 바로 접근)
   * /admin/user/login/{user}
   */
  public function login(User $user) {
    $result = $this->_login($user);
    if($result) {
      return redirect('/');
    }
  }
}
