<?php

namespace Pondol\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Pondol\Auth\Models\User\User;
use Pondol\Auth\Traits\CanManageSubscription;
use Validator;

class UserController extends Controller
{
    use CanManageSubscription;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function profile(Request $request)
    {

        $user = $request->user();

        return view(auth_theme('user').'.profile', compact('user'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        return view(auth_theme('user').'.edit', compact('user'));
    }

    /**
     * profile update
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|current_password:web',
            'is_subscribed' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['error' => $validator->errors()->first()], 203); // 500, 203
            } else {
                return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password'));
            }
        }

        $user->name = $request->name;
        $this->updateSubscriptionLogic($request, $user);
        $user->save();
        if ($request->ajax()) {
            return response()->json(['error' => false], 200); // 500, 203
        } else {
            return redirect()->route('user.profile');
        }
    }

    /**
     * 패스워드 변경
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        return view(auth_theme('user').'.edit-password', compact('user'));
    }

    /**
     * 패스워드 변경
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
            'current_password' => 'required|current_password:web',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['error' => $validator->errors()->first()], 203); // 500, 203
            } else {
                return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password'));
            }
        }
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->ajax()) {
            return response()->json(['error' => false], 200); // 500, 203
        } else {
            return redirect()->route('user.profile');
        }
    }

    /**
     * 이메일 변경
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'required|current_password:web',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['error' => $validator->errors()->first()], 203); // 500, 203
            } else {
                return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password'));
            }
        }

        $user->email = $request->email;
        $user->save();
        if ($request->ajax()) {
            return response()->json(['error' => false], 200); // 500, 203
        } else {
            return redirect()->back();
        }
    }

    /**
     * 모바일 변경
     */
    public function updateMobile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'mobile' => 'sometimes|min:8',
            'mobile_1' => 'sometimes|min:2',
            'mobile_2' => 'sometimes|min:3',
            'mobile_3' => 'sometimes|min:4',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['error' => $validator->errors()->first()], 203); // 500, 203
            } else {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
        }
        $user->mobile = str_replace('-', '', $request->mobile);
        $user->save();

        if ($request->ajax()) {
            return response()->json(['error' => false], 200); // 500, 203
        } else {
            return redirect()->back();
        }
    }

    /**
     * 서명된 링크를 통한 수신 거부 처리
     */
    public function unsubscribe(Request $request, User $user)
    {
        // 1. 상태 변경 (Trait 메소드 대신 직접 처리하거나 Trait을 활용)
        $user->is_subscribed = false;
        $user->save();

        // 2. 패키지 전용 뷰 반환 (사용자가 나중에 커스텀할 수 있도록 설계)
        return view('pondol-auth::user.unsubscribed', compact('user'));
    }
}
