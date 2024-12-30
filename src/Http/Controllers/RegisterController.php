<?php
namespace Pondol\Auth\Http\Controllers;

use App\Providers\RouteServiceProvider;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use DB;


// use Pondol\Auth\Notifications\sendEmailRegisteredNotification;
use Pondol\Auth\Notifications\sendEmailVerificationNotification;
use Pondol\Auth\Models\Role\Role;
use Pondol\Auth\Models\User\User;
use Pondol\Auth\Traits\Register;
use Pondol\Auth\Events\Registered;
use Pondol\Common\Facades\JsonKeyValue;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{

  use Register;
  /*
  |--------------------------------------------------------------------------
  | Register Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles the registration of new users as well as their
  | validation and creation. By default this controller uses a trait to
  | provide this functionality without requiring any additional code.
  |
  */

  // use RegistersUsers;

  /**
   * Where to redirect users after registration.
   *
   * @var string
   */
  protected $redirectTo = RouteServiceProvider::HOME;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(
  ){
  }

  // protected function authenticated(Request $request, User $user)
  // {
  //   return redirect()->intended('/register/success');
  // }

  /**
   * Get a validator for an incoming registration request.
   *
   * @param  array  $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {

    return Validator::make($data, [


      'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
      'name' => ['required', 'string', 'min:2', 'max:50'],
      // 'national_code' => ['required', 'numeric'],
      'mobile' => ['sometimes', 'numeric', 'digits_between:9,11'], // , 'unique:users'
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ], [
      'aggree_terms_of_use.required' => '이용약관에 동의해 주세요',
      'privacy_policy.required' => '개인정보 수집 및 이용에 동의해 주세요',
      'name.required' =>'이름을 입력하세요',
      'name.min' =>'이름은 최소 2자리 이상입니다.',
      'name.max' =>'이름은 최대 20자리 미만입니다',

      'national_code.required' =>'국가번호를 입력해 주세요',
      'email.required' =>'이메일을 입력해주세요.',
      'email.email' =>'이메일형식이 잘못되었습니다.',
      'email.unique' =>'사용하려는 이메일이 이미 존재합니다.',

      'password.required' =>'패스워드를 입력해 주세요',
      'password.min' =>'패스워드는 최소 8자 이상입니다',
      'password.confirmed' =>'패스워드가 일치하지 않습니다',
    ]);
  }

    /**
   * 회원가입폼 출력
   * 회원가입형식에 따라 각각 분기 시킨다. 
   * 일바적인 양식 : 
   * - 1. register  
   * - 2. register success (simple)
   * - 3. agreement register, success (default)
   * - register addmoredata(sns일경우 추가 정보를 받는 방식), success
   * agreement, register, done
   */
  public function create(Request $request) {

    if (\View::exists($view = auth_theme('user').'.register-agreement') 
        && !$request->session()->has('agreement')) {
      return redirect()->route('register.agreement');
    }

    // if ($request->session()->has('agreement')) {
    //   return view(auth_theme('user').'.register', [
    //     'agreements' => $request->session()->get('agreement')
    //   ]);
    // } else {
      $termsOfUse = JsonKeyValue::get('user.aggrement.term-of-use');
      $privacyPolicy = JsonKeyValue::get('user.aggrement.privacy-policy');
      return view(auth_theme('user').'.register', [
        'termsOfUse' => $termsOfUse,
        'privacyPolicy' => $privacyPolicy,
        'agreements' => $request->session()->get('agreement')
      ]);
    // }
  }

  public function agreement(Request $request) {
    $termsOfUse = JsonKeyValue::get('user.aggrement.term-of-use');
    $privacyPolicy = JsonKeyValue::get('user.aggrement.privacy-policy');
    return view(auth_theme('user').'.register-agreement', [
      'termsOfUse' => $termsOfUse,
      'privacyPolicy' => $privacyPolicy
    ]);
  }

  public function agreementstore(Request $request) {
    $validator = Validator::make($request->all(), [
      'aggree_terms_of_use' => ['required'],
      'privacy_policy' => ['required'],
    ], [
      'aggree_terms_of_use.required' => '이용약관에 동의해 주세요',
      'privacy_policy.required' => '개인정보 수집 및 이용에 동의해 주세요',
    ]);

    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()->first()]);
      // return redirect()->back()->withInput()->withErrors($validator->errors());
    }

    $request->session()->put('agreement', [
      'aggree_terms_of_use'=>$request->aggree_terms_of_use,
      'privacy_policy'=>$request->privacy_policy
    ]);

    return response()->json(['error'=>false, 'next'=>route('register')]);
  }

  /**
   * Create a new user instance after a valid registration.
   *
   * @param  array  $data
   * @return \App\User
   */
  public function store(Request $request)
  {

    $auth_cfg = JsonKeyValue::getAsJson('auth');

    if (isset($request->mobile)) {
      $mobile = str_replace('-', '', $request->mobile);
      $request->merge(['mobile' => $mobile]);
    }

    $validator = $this->validator($request->all());

    $agreement = \View::exists($view = auth_theme('user').'.register-agreement') ? true : false; 

    $validator->sometimes('aggree_terms_of_use', 'required', function () use ($agreement) {
      return $agreement;
    });
    $validator->sometimes('privacy_policy', 'required', function () use ($agreement) {
      return $agreement;
    });



    if ($validator->fails()) {
      if($request->ajax()){
        return response()->json(['error'=>$validator->errors()->first()], 203);//500, 203
      } else {
        return redirect()->back()->withInput()->withErrors($validator->errors());
      }
      
    }
    
    DB::beginTransaction();
    try {
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        // 'mobile' => $request->mobile,
        'password' => Hash::make($request->password),
      ]);

      if (isset($request->mobile)) {
        $user->mobile = $request->mobile;
      }

      // $usercfg = $this->configSvc->get('user');
      if($auth_cfg->activate == "auto") {
        $user->active = 1;
      }

      $user->save();

      // 추가 (기본 role 적용)

      $user->roles()->attach(Role::firstOrCreate(['name' =>config('pondol-auth.roles.default_role')]));
      // }
      DB::commit();

      event(new Registered($user));
      Auth::login($user);
      
      if($auth_cfg->activate == "email") {
        $user->notify(new sendEmailVerificationNotification);
        return redirect()->route('verification.notice');
      } else {
        return redirect()->route('register.success');
      }

    } catch (\Exception $e) {
      DB::rollback();
      print_r($e->getMessage());
      if($request->ajax()){
        return response()->json(['error'=>$e->getMessage()], 203);//500, 203
      } else {
        return redirect()->back()->withInput()->withErrors(['register'=>$e->getMessage()]);
      }
    }
  }



  /**
   * 완료후 이동 페이지
   */
  public function success(Request $request) {
    return view(auth_theme('user').'.register-success', [
    ]);
  }
}
