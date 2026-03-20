<?php

namespace Pondol\Auth\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Pondol\Auth\Models\User\UserPoint;
use Pondol\Common\Facades\JsonKeyValue;

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

        if (is_null($type)) {
            $type = config('pondol-auth.point.default_type', 0);
        }

        return DB::transaction(function () use ($user, $amount, $item, $sub_item, $rel_item, $type) {
            $user->refresh();
            $newPoint = $user->point + $amount;
            $user->point = $newPoint;
            $user->save();

            $log = new UserPoint;
            $log->user_id = $user->id;
            $log->point = $amount;
            $log->cur_sum = $newPoint;
            $log->item = $item;
            $log->sub_item = $sub_item;
            $log->rel_item = $rel_item;

            if (Schema::hasColumn('user_points', 'point_type')) {
                $log->point_type = $type;
            } elseif (Schema::hasColumn('user_points', 'is_paid')) {
                $log->is_paid = ($type == config('pondol-auth.point.paid_type', 1));
            }

            // 만료일 로직: 적립 시에만 적용
            if (Schema::hasColumn('user_points', 'expires_at') && $amount > 0) {
                if ($type == config('pondol-auth.point.free_type', 0)) {
                    $log->expires_at = now()->addYear();
                }
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
            $query = UserPoint::where('user_id', $userId)->where('point_type', $value);

            // [추가] 만료된 포인트는 잔액 합산에서 제외 (매우 중요)
            if (Schema::hasColumn('user_points', 'expires_at')) {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                });
            }

            $results[$label] = (int) max(0, $query->sum('point'));
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
            $usageReport = []; // 어떤 타입을 얼마나 썼는지 기록

            foreach ($priority as $type) {
                if ($remaining <= 0) {
                    break;
                }

                // 해당 타입의 가용 잔액 확인 (만료된 것 제외)
                $query = UserPoint::where('user_id', $user->id)->where('point_type', $type);
                if (Schema::hasColumn('user_points', 'expires_at')) {
                    $query->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
                }

                $typeBalance = (int) $query->sum('point');
                if ($typeBalance <= 0) {
                    continue;
                }

                $deduct = min($typeBalance, $remaining);
                $this->record($user, -$deduct, $item, $sub_item, null, $type);

                $usageReport[$type] = $deduct;
                $remaining -= $deduct;
            }

            if ($remaining > 0) {
                throw new Exception('가용 잔액이 부족합니다. (만료된 복채 제외)');
            }

            return $usageReport; // 차감 내역 반환
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

        $report = $this->usePointWithPriority($user, $amount, $priority, $item, $sub_item);

        // [하위 호환성] 기존의 ['free' => x, 'paid' => y] 형식으로 반환
        return [
            'free' => $report[$free] ?? 0,
            'paid' => $report[$paid] ?? 0,
        ];
    }

    /**
     * [Legacy] 기존 유/무료 기반 잔액 조회
     */
    public function getBalances($userId)
    {
        $free = config('pondol-auth.point.free_type', 0);
        $paid = config('pondol-auth.point.paid_type', 1);

        $res = $this->getBalancesByMap($userId, ['free' => $free, 'paid' => $paid]);

        return [
            'paid' => $res['paid'],
            'free' => $res['free'],
            'total' => $res['paid'] + $res['free'],
        ];
    }

    /**
     * 회원가입 포인트 지급 (이벤트성)
     */
    public function grantRegisterPoint($user)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $point = $auth_cfg->point->register ?? 0;
        if ($point > 0) {
            $this->record($user, $point, 'event', 'register', $user->id, config('pondol-auth.point.free_type', 0));
        }
    }

    /**
     * 로그인 포인트 지급 (중복 지급 방지 로직 포함)
     */
    public function grantLoginPoint($user)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $point = $auth_cfg->point->login ?? 0;

        if ($point > 0) {
            $exists = UserPoint::where('user_id', $user->id)
                ->where('item', 'event')
                ->where('sub_item', 'login')
                ->whereDate('created_at', now()->today())
                ->exists();

            if (! $exists) {
                $this->record($user, $point, 'event', 'login', $user->id, config('pondol-auth.point.free_type', 0));
            }
        }
    }
}
