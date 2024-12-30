<?php
namespace Pondol\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Validator;

use App\Http\Controllers\Controller;
use Pondol\Auth\Models\User\User;

class UserController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {

  }


  public function profile(Request $request){

    

    $user = $request->user();

    // $tmp = User::where('email', $user->email)->first();
    // $createToken = $tmp->createToken('nomalAdmin')->plainTextToken;
    // print_r($createToken);
    // $k = $request->user()->generateTwoFactorCode();
    return view(auth_theme('user').'.profile', compact('user'));
  }

  public function edit(Request $request){
    $user = $request->user();
    return view(auth_theme('user').'.edit', compact('user'));
  }

  /**
   * profile update
   */
  public function update(Request $request){
    $user = $request->user();
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'password' => 'required|current_password:web'
    ]);
   
    if ($validator->fails()) {
      if($request->ajax()){
        return response()->json(['error'=>$validator->errors()->first()], 203);//500, 203
      } else {
        return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password'));
      }
    }

    $user->name = $request->name;
    $user->save();
    if($request->ajax()){
      return response()->json(['error'=>false], 200);//500, 203
    } else {
      return redirect()->route('user.profile');
    }
  }

  /**
   * 패스워드 변경
   */
  public function changePassword(Request $request){
    $user = $request->user();
    return view(auth_theme('user').'.edit-password', compact('user'));
  }

  /**
   * 패스워드 변경
   */
  public function updatePassword(Request $request) {
    $user = $request->user();

    $validator = Validator::make($request->all(), [
      'password' => 'required|min:8|confirmed',
      'current_password' => 'required|current_password:web'
    ]);

    if ($validator->fails()) {
      if($request->ajax()){
        return response()->json(['error'=>$validator->errors()->first()], 203);//500, 203
      } else {
        return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password'));
      }
    }
    $user->password = Hash::make($request->password);
    $user->save();

    if($request->ajax()){
      return response()->json(['error'=>false], 200);//500, 203
    } else {
      return redirect()->route('user.profile');
    }
  }

   /**
   * 이메일 변경
   */
  public function updateEmail(Request $request) {
    $user = $request->user();

    $validator = Validator::make($request->all(), [
      'email' => 'required|unique:users,email,'.$user->id,
      'password' => 'required|current_password:web'
    ]);
   
    if ($validator->fails()) {
      if($request->ajax()){
        return response()->json(['error'=>$validator->errors()->first()], 203);//500, 203
      } else {
        return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password'));
      }
    }

    $user->email = $request->email;
    $user->save();
    if($request->ajax()){
      return response()->json(['error'=>false], 200);//500, 203
    } else {
      return redirect()->back();
    }
  }
  



  /**
   * 모바일 변경
   */
  public function updateMobile(Request $request) {
    $user = $request->user();

    $validator = Validator::make($request->all(), [
      'mobile' => 'sometimes|min:8',
      'mobile_1' => 'sometimes|min:2',
      'mobile_2' => 'sometimes|min:3',
      'mobile_3' => 'sometimes|min:4',
    ]);

    if ($validator->fails()) {
      if($request->ajax()){
        return response()->json(['error'=>$validator->errors()->first()], 203);//500, 203
      } else {
        return redirect()->back()->withErrors($validator->errors())->withInput();
      }
    }
    $user->mobile = str_replace('-', '', $request->mobile);
    $user->save();

    if($request->ajax()){
      return response()->json(['error'=>false], 200);//500, 203
    } else {
      return redirect()->back();
    }
  }


}
