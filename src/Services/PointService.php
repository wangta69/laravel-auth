<?php

namespace Pondol\Auth\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Pondol\Auth\Models\User\UserPoint;
use Pondol\Common\Facades\JsonKeyValue;
use Schema;

class PointService
{
    /**
     * 포인트 적립/차감 (핵심 메커니즘)
     */
    public function record($user, $amount, $item, $sub_item = null, $rel_item = null, $type = null)
    {
        if (! $amount) {
            return null;
        }
        if (! $sub_item) {
            $sub_item = $item;
        }

        // 1. 타입 결정 (전달값이 없으면 설정파일의 기본값 사용)
        if (is_null($type)) {
            $type = config('pondol-auth.point.default_type', 0);
        }

        return DB::transaction(function () use ($user, $amount, $item, $sub_item, $rel_item, $type) {

            // 2. 유저 잔액 갱신
            $user->refresh();
            $newPoint = $user->point + $amount;
            $user->point = $newPoint;
            $user->save();

            // 3. 로그 생성
            $log = new UserPoint;
            $log->user_id = $user->id;
            $log->point = $amount;
            $log->cur_sum = $newPoint;
            $log->item = $item;
            $log->sub_item = $sub_item;
            $log->rel_item = $rel_item;

            // [범용] point_type 컬럼이 있으면 저장
            if (Schema::hasColumn('user_points', 'point_type')) {
                $log->point_type = $type;
            }
            // [레거시] is_paid 컬럼만 있으면 설정의 paid_type과 비교하여 저장
            elseif (Schema::hasColumn('user_points', 'is_paid')) {
                $log->is_paid = ($type == config('pondol-auth.point.paid_type', 1));
            }

            // [자동 유효기간] 무료 포인트 적립 시에만 적용
            if (Schema::hasColumn('user_points', 'expires_at') &&
                $type == config('pondol-auth.point.free_type', 0) && $amount > 0) {
                $log->expires_at = now()->addYear();
            }

            $log->save();

            return $log;
        });
    }

    /**
     * 프로젝트 정의 맵에 따른 잔액 동적 계산 (유연한 확장성)
     */
    public function getBalancesByMap($userId, array $typeMap)
    {
        $results = [];
        foreach ($typeMap as $label => $value) {
            $sum = UserPoint::where('user_id', $userId)->where('point_type', $value)->sum('point');
            $results[$label] = (int) max(0, $sum);
        }

        return $results;
    }

    /**
     * 특정 우선순위에 따른 순차 차감 (전략적 포인트 소진)
     */
    public function usePointWithPriority($user, $amount, array $priority, $item, $sub_item = null)
    {
        if ($user->point < $amount) {
            throw new Exception('보유 포인트가 부족합니다.');
        }

        return DB::transaction(function () use ($user, $amount, $priority, $item, $sub_item) {
            $remaining = $amount;

            foreach ($priority as $type) {
                if ($remaining <= 0) {
                    break;
                }

                $typeBalance = UserPoint::where('user_id', $user->id)->where('point_type', $type)->sum('point');
                if ($typeBalance <= 0) {
                    continue;
                }

                $deduct = min($typeBalance, $remaining);
                $this->record($user, -$deduct, $item, $sub_item, null, $type);
                $remaining -= $deduct;
            }

            if ($remaining > 0) {
                throw new Exception('포인트 타입별 잔액 부족으로 차감에 실패했습니다.');
            }

            return true;
        });
    }

    /**
     * [Legacy] 기존 설정 기반 사용 (is_paid 방식과 point_type 방식 모두 지원)
     */
    public function usePoint($user, $amount, $item, $sub_item = null, $rel_item = null)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $priorityCfg = $auth_cfg->point->deduction_priority ?? 'free_first';

        $free = config('pondol-auth.point.free_type', 0);
        $paid = config('pondol-auth.point.paid_type', 1);

        $priority = ($priorityCfg === 'paid_first') ? [$paid, $free] : [$free, $paid];

        return $this->usePointWithPriority($user, $amount, $priority, $item, $sub_item);
    }

    /**
     * [Legacy] 기존 유/무료 기반 잔액 조회
     */
    public function getBalances($userId)
    {
        if (Schema::hasColumn('user_points', 'point_type')) {
            return $this->getBalancesByMap($userId, [
                'free' => config('pondol-auth.point.free_type', 0),
                'paid' => config('pondol-auth.point.paid_type', 1),
            ]);
        }

        $paid = UserPoint::where('user_id', $userId)->where('is_paid', true)->sum('point');
        $free = UserPoint::where('user_id', $userId)->where('is_paid', false)->sum('point');

        return ['paid' => (int) $paid, 'free' => (int) $free];
    }
}
