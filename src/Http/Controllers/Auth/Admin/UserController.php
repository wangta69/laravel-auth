<?php

namespace App\Http\Controllers\Auth\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notifiable;
// use App\Notifications\CountChanged;
use Validator;
use DB;

use App\Models\Auth\User\User;
use App\Models\Auth\Role\Role;

class UserController extends Controller
{
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
    // DB::enableQueryLog();
    $sk = $request->sk;
    $sv = $request->sv;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $active = $request->active;
    // $role = $request->role;

    // $users =  User::with('roles')->sortable(['created_at' => 'desc']);
    $users = app('App\Models\Auth\User\User')
    ->select(
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
        // $q->where('sex', Auth::user()->sex)->orWhere('sex', 0);
        $q->whereRaw("DATE(users.created_at) >= '".$from_date."' AND DATE(users.created_at)<= '".$to_date."'" );
      });
    }

    if($active) {
      $users = $users->whereIn('users.active', $active);
    }

    $users = $users->withTrashed()->orderBy('id', 'desc')->paginate(20)->appends(request()->query());
    // var_dump(DB::getQueryLog());
    // 회원전체 통계관련 Summary
    return view('auth.admin.users.index', [
      'users' => $users
    ]);
  }


  /**
   * Display the specified resource.
   *
   * @param User $user
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function show($user)
  {
    $user = User::withTrashed()->find($user);
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

    $validator = Validator::make($request->all(), [
      'name' => 'required|max:50',
    //    'email' => 'required|email|max:255'
    ]);


    $validator->sometimes('password', 'min:8|confirmed', function ($input) {
      return $input->password;
    });

    $validator->sometimes('security_password', 'numeric|digits:4|confirmed', function ($input) {
      return $input->security_password;
    });

    if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());


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

    return redirect()->intended(route('auth.admin.user', [$user->id]));

  }
  

  /**
   * Display the specified resource.
   *
   * @param User $user
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function show_waiting(User $user)
  {
    // 총레벨
    return view('auth.admin.users.show_waiting', [
      'user' => $user,
      'roles' => Role::get(),
    ]);
    //return view('admin.users.show_waiting', ['user' => $user, 'bank' => $bank]);
  }



  /**
   * 회원 등록 폼
   */
  public function create()
  {
    return view('admin.users.create', [
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
      //    'security_password' => ['required', 'numeric', 'digits:4', 'confirmed'],
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
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)//
  {
    if ($user->active == 0) {
      // $user->notify(new CountChanged('substract', 'users'));
    }
    $user->delete();
  }

  /**
   * @param Number $active :  0: 미인가, 1: 인가, 2: 차단, 8: 탈퇴신청, 9: 탈퇴
   */
  public function updateActive($user_id, $active) { // User $user로 받을 경우 deleted_at이 있으므로 찾지 못하는 에러 발생
  //->withTrashed()->
      // $user = User::find($user_id);
      $user = User::where('id', $user_id)->withTrashed()->first();
      if ($active == 0) {
          // $user->notify(new CountChanged('add', 'users'));
      } else if ($user->active == 0) {
          // $user->notify(new CountChanged('substract', 'users'));
      }

      if ($active == 8) {
          // $user->notify(new CountChanged('add', 'deactivate'));
      } else if ($user->active == 8) {
          // $user->notify(new CountChanged('substract', 'deactivate'));
      }

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

      return response()->json(['error'=>false], 200);
  }

  /**
   * 회원정보로 강제 로그인 (메뉴단에서는 현재 숨기고 url로 바로 접근)
   * /admin/user/login/{user}
   */
  public function login(User $user) {
    $auth = \Illuminate\Support\Facades\Auth::user();
    if($auth->hasRole("administrator")){
      \Illuminate\Support\Facades\Auth::guard()->login($user);
      return redirect('/');
    }
  }

  /**
   * 현재 접속한 users (socket으로 처리)
   */
  public function currentConnectors() {
    return view('admin.users.connectors');
  }

  public function deactivate(Request $request) {
    $sk = $request->sk;
    $sv = $request->sv;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $users = \DB::table('users_deactivate as d')
      ->select(
          'd.reason', 'd.created_at',
          'u.id', 'u.email', 'u.name', 'u.mobile', 'u.tester', 'u.active', 'u.level', 'u.point', 'u.created_at as user_created_at',
          'a.name as agent_name'
      )
      ->leftjoin('users as u', function($join){
          $join->on('d.user_id', '=', 'u.id');
      })
      ->whereNull('d.deleted_at');

    if ($sv) {
        if ($sk == 'u.mobile') {
            $sv = (int)preg_replace("/[^0-9]+/", "", $sv);;
        }
        $users = $users->where($sk, 'like', '%' . $sv . '%');
    }

    if ($from_date) {
        if (!$to_date) {
            $to_date = date("Y-m-d");
        }
        $users = $users->where(function ($q) use($from_date, $to_date) {
            // $q->where('sex', Auth::user()->sex)->orWhere('sex', 0);
            $q->whereRaw("DATE(users.created_at) >= '".$from_date."' AND DATE(users.created_at)<= '".$to_date."'" );
        });
    }

    $users = $users->orderBy('d.id', 'desc')
        ->paginate(20)->appends(request()->query());
    return view('admin.users.deactivate', [
        'users' => $users
    ]);
  }
}
