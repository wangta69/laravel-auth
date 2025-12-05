<?php

namespace Pondol\Auth\Services;

use Illuminate\Support\Facades\DB;
use Pondol\Auth\Models\User\UserPoint;
use Pondol\Common\Facades\JsonKeyValue;

class PointService
{
    /**
     * 포인트 적립/차감 (핵심 로직)
     * - DB 트랜잭션 적용으로 안전성 확보
     */
    public function record($user, $amount, $item, $sub_item = null, $rel_item = null, $is_paid = false)
    {
        if (! $amount) {
            return null;
        }
        if (! $sub_item) {
            $sub_item = $item;
        }

        return DB::transaction(function () use ($user, $amount, $item, $sub_item, $rel_item, $is_paid) {

            // 1. 유저 포인트 갱신 (동시성 문제 방지를 위해 increment 대신 lock을 권장하지만, 일단 간소화)
            // 기존 로직: $user->increment('point', $point);
            // 개선 로직: 모델의 point 값을 직접 수정하여 save (이벤트 트리거 등 활용 가능)
            $currentPoint = $user->point;
            $newPoint = $currentPoint + $amount;

            $user->point = $newPoint;
            $user->save();

            // 2. 로그 기록
            $log = new UserPoint;
            $log->user_id = $user->id;
            $log->point = $amount;
            $log->cur_sum = $newPoint; // 계산된 잔액
            $log->item = $item;
            $log->sub_item = $sub_item;
            $log->rel_item = $rel_item;

            // [신규 컬럼] 마이그레이션이 되어 있다면 저장
            if (\Schema::hasColumn('user_points', 'is_paid')) {
                $log->is_paid = $is_paid;
            }
            if (\Schema::hasColumn('user_points', 'expires_at') && ! $is_paid && $amount > 0) {
                // 무상 포인트는 기본 1년 유효기간 (예시)
                $log->expires_at = now()->addYear();
            }

            $log->save();

            return $log;
        });
    }

    /**
     * 포인트 사용 (차감) - DB 설정에 따른 우선순위 적용
     */
    public function usePoint($user, $amount, $item, $sub_item = null, $rel_item = null)
    {
        // 1. 총 잔액 확인
        if ($user->point < $amount) {
            throw new Exception('보유 복채가 부족합니다.');
        }

        return DB::transaction(function () use ($user, $amount, $item, $sub_item, $rel_item) {

            // 2. DB(json_key_values)에서 설정 가져오기
            $auth_cfg = JsonKeyValue::getAsJson('auth');

            // 설정값이 없으면 기본값 'free_first' (무료 먼저 차감)
            $priority = $auth_cfg->point->deduction_priority ?? 'free_first';

            // 3. 현재 유/무료 잔액 조회
            $balances = $this->getBalances($user->id);
            $freeBal = $balances['free'];
            $paidBal = $balances['paid'];

            $deductFree = 0;
            $deductPaid = 0;

            // 4. 우선순위에 따른 분할 계산 로직
            if ($priority === 'paid_first') {
                // [유료 우선 차감]
                if ($paidBal >= $amount) {
                    $deductPaid = $amount;
                } else {
                    $deductPaid = $paidBal;
                    $deductFree = $amount - $paidBal;
                }
            } else {
                // [무료 우선 차감] (Default)
                if ($freeBal >= $amount) {
                    $deductFree = $amount;
                } else {
                    $deductFree = $freeBal;
                    $deductPaid = $amount - $freeBal;
                }
            }

            // 5. record 메서드 호출하여 실제 저장
            // (A) 무료 포인트 차감 기록
            if ($deductFree > 0) {
                // 차감이니까 음수(-deductFree), is_paid = false
                $this->record($user, -$deductFree, $item, $sub_item, $rel_item, false);
            }

            // (B) 유료 포인트 차감 기록
            if ($deductPaid > 0) {
                // 차감이니까 음수(-deductPaid), is_paid = true
                $this->record($user, -$deductPaid, $item, $sub_item, $rel_item, true);
            }

            return true;
        });
    }

    /**
     *  유저의 유료/무료 포인트 잔액 계산
     */
    public function getBalances($userId)
    {
        $paid = UserPoint::where('user_id', $userId)->where('is_paid', true)->sum('point');
        $free = UserPoint::where('user_id', $userId)->where('is_paid', false)->sum('point');

        return [
            'paid' => max(0, $paid),
            'free' => max(0, $free),
        ];
    }

    /**
     * 회원가입 포인트 지급 (Trait의 _register 대체)
     */
    public function grantRegisterPoint($user)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $point = $auth_cfg->point->register ?? 0;

        if ($point > 0) {
            // 이미 지급받았는지 체크하는 로직을 추가할 수도 있음
            $this->record($user, $point, 'event', 'register', $user->id, false);
        }
    }

    /**
     * 로그인 포인트 지급 (Trait의 _login 대체)
     */
    public function grantLoginPoint($user)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $point = $auth_cfg->point->login ?? 0;

        if ($point > 0) {
            // [고도화] 오늘 이미 로그인을 했는지 체크 (중복 지급 방지)
            $todayLoginPoint = UserPoint::where('user_id', $user->id)
                ->where('item', 'event')
                ->where('sub_item', 'login')
                ->whereDate('created_at', now()->today())
                ->exists();

            if (! $todayLoginPoint) {
                $this->record($user, $point, 'event', 'login', $user->id, false);
            }
        }
    }
}
