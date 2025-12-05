<?php

namespace Pondol\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Password Rules 사용 시 필요
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Pondol\Auth\Events\Registered;
use Pondol\Auth\Models\Role\Role;
use Pondol\Auth\Notifications\sendEmailVerificationNotification;
use Pondol\Auth\Traits\Register;
use Pondol\Common\Facades\JsonKeyValue;

class RegisterController extends Controller
{
    use Register;

    // 모델을 동적으로 가져오기 위한 속성
    protected $userModel;

    public function __construct(
    ) {

        $modelClass = config('auth.providers.users.model', \App\Models\User::class);
        $this->userModel = new $modelClass;
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
    public function create(Request $request)
    {

        if (\View::exists($view = auth_theme('user').'.register-agreement')
            && ! $request->session()->has('agreement')) {
            return redirect()->route('register.agreement');
        }

        $termsOfUse = JsonKeyValue::get('user.aggrement.term-of-use');
        $privacyPolicy = JsonKeyValue::get('user.aggrement.privacy-policy');

        return view(auth_theme('user').'.register', [
            'termsOfUse' => $termsOfUse,
            'privacyPolicy' => $privacyPolicy,
            'agreements' => $request->session()->get('agreement'),
        ]);
    }

    public function agreement(Request $request)
    {
        $termsOfUse = JsonKeyValue::get('user.aggrement.term-of-use');
        $privacyPolicy = JsonKeyValue::get('user.aggrement.privacy-policy');

        return view(auth_theme('user').'.register-agreement', [
            'termsOfUse' => $termsOfUse,
            'privacyPolicy' => $privacyPolicy,
        ]);
    }

    public function agreementstore(AgreementRequest $request)
    {
        // 1. 세션 저장 (유효성 검사는 이미 통과됨)
        // validated()는 규칙에 정의된 필드만 안전하게 반환함
        $request->session()->put('agreement', $request->validated());

        // 2. 응답 분기 (JSON vs Redirect)
        if ($request->wantsJson()) {
            return response()->json(['error' => false, 'next' => route('register')]);
        }

        // 일반 웹 요청은 바로 페이지 이동
        return redirect()->route('register');
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

        // 1. 데이터 전처리 (Mobile 하이픈 제거)
        if ($request->has('mobile')) {
            $request->merge(['mobile' => str_replace('-', '', $request->mobile)]);
        }

        // 2. 유효성 검사 (기존 validator 메서드 활용)
        $validator = $this->validator($request->all());

        // 약관 동의 체크 (뷰 파일 존재 시)
        $hasAgreement = \View::exists(auth_theme('user').'.register-agreement');
        $validator->sometimes(['aggree_terms_of_use', 'privacy_policy'], 'required', function () use ($hasAgreement) {
            return $hasAgreement;
        });

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        DB::beginTransaction();
        try {
            // [핵심] 모델의 fillable 필드만 추출하여 저장 (동적 처리)
            // 이를 통해 active, point, mobile 등 커스텀 필드를 패키지 수정 없이 처리 가능
            $userData = $request->only($this->userModel->getFillable());

            // 비밀번호 암호화
            if ($request->has('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // 자동 활성화 처리 (request에 없고, 설정이 auto인 경우)
            if (! isset($userData['active']) && isset($auth_cfg->activate) && $auth_cfg->activate == 'auto') {
                $userData['active'] = 1;
            }

            // User 생성
            $user = $this->userModel->create($userData);

            // 기본 Role 할당
            $defaultRole = config('pondol-auth.roles.default_role', 'user');
            $user->roles()->attach(Role::firstOrCreate(['name' => $defaultRole]));

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            // 후속 처리 (이메일 인증 vs 가입 완료)
            if (isset($auth_cfg->activate) && $auth_cfg->activate == 'email') {
                $user->notify(new sendEmailVerificationNotification);

                return redirect()->route('verification.notice');
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'redirect' => route('register.success')]);
            }

            return redirect()->route('register.success');

        } catch (\Exception $e) {
            DB::rollback();
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->withErrors(['register' => $e->getMessage()]);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [

            'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
            'name' => ['required', 'string', 'min:2', 'max:50'],
            // 'national_code' => ['required', 'numeric'],
            'mobile' => ['sometimes', 'nullable', 'numeric'], // , 'unique:users'
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'aggree_terms_of_use.required' => '이용약관에 동의해 주세요',
            'privacy_policy.required' => '개인정보 수집 및 이용에 동의해 주세요',
            'name.required' => '이름을 입력하세요',
            'name.min' => '이름은 최소 2자리 이상입니다.',
            'name.max' => '이름은 최대 20자리 미만입니다',

            'national_code.required' => '국가번호를 입력해 주세요',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '이메일형식이 잘못되었습니다.',
            'email.unique' => '사용하려는 이메일이 이미 존재합니다.',

            'password.required' => '패스워드를 입력해 주세요',
            'password.min' => '패스워드는 최소 8자 이상입니다',
            'password.confirmed' => '패스워드가 일치하지 않습니다',
        ]);
    }

    /**
     * 완료후 이동 페이지
     */
    public function success(Request $request)
    {
        return view(auth_theme('user').'.register-success', [
        ]);
    }
}
