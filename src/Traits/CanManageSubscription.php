<?php

namespace Pondol\Auth\Traits;

use Illuminate\Http\Request;

trait CanManageSubscription
{
    /**
     * 사용자의 수신 동의 여부 업데이트 로직
     */
    protected function updateSubscriptionLogic(Request $request, $user)
    {
        // is_subscribed 필드가 요청에 포함되어 있다면 처리
        // 폼 체크박스는 체크가 안 되면 필드 자체가 전송 안 될 수 있으므로 boolean() 처리가 유용함
        if ($request->has('is_subscribed') || $request->has('email_valid')) {
            if ($request->has('is_subscribed')) {
                $user->is_subscribed = $request->boolean('is_subscribed');
            }

            if ($request->has('email_valid')) {
                $user->email_valid = $request->boolean('email_valid');
            }

            $user->save();
        }
    }
}
