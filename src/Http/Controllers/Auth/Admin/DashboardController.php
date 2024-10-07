<?php

namespace App\Http\Controllers\Auth\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notifiable;
// use App\Notifications\CountChanged;
use Validator;
use DB;



class DashboardController extends Controller
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
    // / 최근 가입한 회원
    $users = config('auth.providers.users.model')::orderBy('created_at', 'desc')->skip(0)->take(5)->get();
    return view('auth.admin.dashboard', [
      'users'=>$users
    ]);
  }

}