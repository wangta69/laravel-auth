<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
  }
  /**
   * 회원가입시 이메일 유효성 및 중복 체크
   */
  public function validationEmail($email) {
    $validator = Validator::make(['email' => $email], [
      // 'user_id' => ['required', 'alpha_num', 'min:5', 'max:20', 'unique:users']
      'email' => ['required', 'email', 'unique:users']

      ], [
        'email.required' =>'이메일을 입력해주세요.',
        'email.email' =>'이메일형식이 잘못되었습니다.',
        'email.unique' =>'사용하려는 이메일이 이미 존재합니다.',
      ]
    );

    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()->first()], 203);
    } else {
      return response()->json(['error'=>false], 200);
    }
  }
}
