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

            // [핵심 추가] 적립(양수)일 때만 남은 포인트(remaining_point)를 설정
            if ($amount > 0) {
                $log->remaining_point = $amount;
            } else {
                $log->remaining_point = 0;
            }

            if (Schema::hasColumn('user_points', 'point_type')) {
                $log->point_type = $type;
            }

            // 만료일 로직: 적립 시에만 적용
            if (Schema::hasColumn('user_points', 'expires_at') && $amount > 0) {
                // 무상 포인트 등에 대해 만료일 설정 (프로젝트 정책에 따름)
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
            // point 대신 remaining_point를 조회하여 만료 처리를 정확하게 함
            $query = UserPoint::where('user_id', $userId)
                ->where('point_type', $value)
                ->where('remaining_point', '>', 0);

            if (Schema::hasColumn('user_points', 'expires_at')) {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                });
            }

            $results[$label] = (int) max(0, $query->sum('remaining_point'));
        }

        return $results;
    }

    /**
     * 특정 우선순위에 따른 순차 차감 (전략적 포인트 소진)
     */
    public function usePointWithPriority($user, $amount, array $priority, $item, $sub_item = null)
    {
        // 1단계: 가용 잔액 먼저 체크 (배신감 방지)
        $balances = $this->getBalances($user->id);
        if ($balances['total'] < $amount) {
            throw new Exception('가용 잔액이 부족합니다. (만료된 복채 제외)');
        }

        return DB::transaction(function () use ($user, $amount, $priority, $item, $sub_item) {
            $remaining = $amount;
            $usageReport = [];

            foreach ($priority as $type) {
                if ($remaining <= 0) {
                    break;
                }

                // [FIFO] 해당 타입의 적립 로그 중 잔액이 있는 것을 오래된 순서대로 가져옴
                $sources = UserPoint::where('user_id', $user->id)
                    ->where('point_type', $type)
                    ->where('remaining_point', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->orderBy('created_at', 'asc') // 오래된 것부터
                    ->get();

                foreach ($sources as $source) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $take = min($source->remaining_point, $remaining);

                    // 원본 적립 행의 남은 금액 깎기 (Update)
                    $source->remaining_point -= $take;
                    $source->save();

                    // 차감 내역 기록 (Insert & user.point 차감)
                    $this->record($user, -$take, $item, $sub_item, null, $type);

                    $usageReport[$type] = ($usageReport[$type] ?? 0) + $take;
                    $remaining -= $take;
                }
            }

            if ($remaining > 0) {
                throw new Exception('결제 처리 중 가용 잔액이 부족해졌습니다.');
            }

            return $usageReport;
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
        $earning = config('pondol-auth.point.earning_type', 2);

        if ($priorityCfg === 'paid_first') {
            $priority = [$paid, $free, $earning];
        } else {
            $priority = [$free, $paid, $earning];
        }

        $report = $this->usePointWithPriority($user, $amount, $priority, $item, $sub_item);

        return [
            'free' => $report[$free] ?? 0,
            'paid' => $report[$paid] ?? 0,
            'earning' => $report[$earning] ?? 0,
        ];
    }

    /**
     * [Legacy] 기존 유/무료 기반 잔액 조회
     */
    public function getBalances($userId)
    {
        $free = config('pondol-auth.point.free_type', 0);
        $paid = config('pondol-auth.point.paid_type', 1);
        $earning = config('pondol-auth.point.earning_type', 2);

        $res = $this->getBalancesByMap($userId, [
            'free' => $free,
            'paid' => $paid,
            'earning' => $earning,
        ]);

        return [
            'paid' => $res['paid'],
            'free' => $res['free'],
            'earning' => $res['earning'],
            'total' => $res['paid'] + $res['free'] + $res['earning'],
        ];
    }

    /**
     * users.point 컬럼과 실제 로그 합계 강제 동기화 (만료 클리닝)
     * 이 메서드는 로그인 시나 결제 직전에 호출하면 좋습니다.
     */
    public function syncBalance($user)
    {
        $balances = $this->getBalances($user->id);
        $realTotal = $balances['total'];

        // 컬럼 잔액이 실제 계산된 잔액보다 많다면 (즉, 만료된 것이 반영 안 되었다면)
        if ($user->point > $realTotal) {
            $diff = $user->point - $realTotal;
            // 실제 차감 로그를 남겨서 users.point를 일치시킴
            $this->record($user, -$diff, 'system', 'expired', null, config('pondol-auth.point.free_type', 0));

            return $diff;
        }

        return 0;
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
