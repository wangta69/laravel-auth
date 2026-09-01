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
                    // 무상(보너스/이벤트) 복채: 지급일로부터 1년
                    $log->expires_at = now()->addYear();
                } elseif ($type == config('pondol-auth.point.paid_type', 1)) {
                    // [정책 반영] 유료 복채: 유효기간 제한 없음
                    $log->expires_at = null;
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

    /**
     * 특정 타입을 지정하여 FIFO 순서로 잔액을 차감 (환불 신청 시 사용)
     */
    public function deductByType($user, $amount, $type, $item, $sub_item, $rel_item = null)
    {
        return DB::transaction(function () use ($user, $amount, $type, $item, $sub_item, $rel_item) {
            $remaining = $amount;
            $sources = UserPoint::where('user_id', $user->id)
                ->where('point_type', $type)
                ->where('remaining_point', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($sources as $source) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min($source->remaining_point, $remaining);

                $source->remaining_point -= $take;
                $source->save();

                // 마이너스 로그 기록 (record 내부에서 remaining_point = 0으로 저장됨)
                $this->record($user, -$take, $item, $sub_item, $rel_item, $type);
                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new Exception('환불 가능한 유료 잔액이 부족합니다.');
            }

            return true;
        });
    }

    /**
     * 특정 결제건(payment_id)에 연결된 포인트 잔액을 직접 0으로 만듦 (외부 취소/웹훅 시 사용)
     */
    public function invalidatePaymentPoints($user, $paymentId)
    {
        return DB::transaction(function () use ($user, $paymentId) {
            // 해당 결제건으로 적립된 모든 로그를 찾음 (유료+보너스 모두)
            $points = UserPoint::where('user_id', $user->id)
                ->where('rel_item', $paymentId)
                ->where('point', '>', 0)
                ->where('remaining_point', '>', 0)
                ->get();

            foreach ($points as $p) {
                $amountToVoid = $p->remaining_point;

                // 1. 해당 행의 잔액을 0으로 만듦
                $p->remaining_point = 0;
                $p->save();

                // 2. 마이너스 로그 기록하여 전체 잔액 동기화
                $this->record($user, -$amountToVoid, 'refund', '결제 취소에 따른 회수', $paymentId, $p->point_type);
            }
        });
    }

    /**
     * 특정 결제건에 연결된 포인트 잔액을 직접 회수 (관리자 취소/외부 웹훅 시 사용)
     */
    public function cancelPaymentPoints($user, $paymentId, $cancelAmount, $isFullRefund)
    {
        // 1. 유료 포인트(원금) 회수
        if ($cancelAmount > 0) {
            $paidLog = UserPoint::where('user_id', $user->id) // [보완] user_id 조건 추가
                ->where('rel_item', $paymentId)
                ->where('point_type', config('pondol-auth.point.paid_type', 1)) // [보완] 하드코딩 대신 config 사용
                ->where('point', '>', 0)
                ->first();

            if ($paidLog && $paidLog->remaining_point > 0) {
                $take = min($paidLog->remaining_point, $cancelAmount);
                $paidLog->remaining_point -= $take;
                $paidLog->save();
                $this->record($user, -$take, 'refund', '결제 취소(원금회수)', $paymentId, config('pondol-auth.point.paid_type', 1));
            }
        }

        // 2. 보너스 포인트 회수 (사용자님의 핵심 정책 반영 구역)
        if ($isFullRefund) {
            $bonusLog = UserPoint::where('user_id', $user->id) // [보완] user_id 조건 추가
                ->where('rel_item', $paymentId)
                ->where('point_type', config('pondol-auth.point.free_type', 0)) // [보완] 하드코딩 대신 config 사용
                ->where('point', '>', 0)
                ->first();

            if ($bonusLog && $bonusLog->remaining_point > 0) {
                $recoverableBonus = $bonusLog->remaining_point;
                $bonusLog->remaining_point = 0;
                $bonusLog->save();

                $this->record($user, -$recoverableBonus, 'refund', '결제 취소(보너스회수)', $paymentId, config('pondol-auth.point.free_type', 0));
            }
        }
    }

    /**
     * 환불을 위한 유료 포인트 차감 (최신순 차감 - LIFO)
     *
     * @param  int|null  $daysLimit  제한 기간 (사용자 신청 시 7, 관리자 임의 취소 시 null)
     */
    public function deductForRefund($user, $amount, $rel_item, $daysLimit = null)
    {
        return DB::transaction(function () use ($user, $amount, $rel_item, $daysLimit) {
            $remaining = $amount;

            // 1. 차감 대상 유료 적립 로그 조회
            $query = UserPoint::where('user_id', $user->id)
                ->where('point_type', config('pondol-auth.point.paid_type', 1))
                ->where('point', '>', 0)
                ->where('remaining_point', '>', 0);

            // [정책 반영] 기간 제한이 있는 경우(사용자 신청) 필터 적용
            if ($daysLimit) {
                $query->where('created_at', '>=', now()->subDays($daysLimit));
            }

            // [정책 반영] 가장 최근에 충전한 것부터 차감 (최신순 정렬)
            $sources = $query->orderBy('created_at', 'desc')->get();

            foreach ($sources as $source) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($source->remaining_point, $remaining);

                // 원본 행의 잔액 직접 차감
                $source->remaining_point -= $take;
                $source->save();

                // 차감 마이너스 로그 기록
                $this->record(
                    $user,
                    -$take,
                    'refund',
                    $daysLimit ? '결제 취소 신청(선차감)' : '관리자 직권 환불 차감',
                    $rel_item,
                    config('pondol-auth.point.paid_type', 1)
                );

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new Exception($daysLimit ? '환불 가능한 최근 7일 이내 유료 잔액이 부족합니다.' : '환불 가능한 유료 잔액이 부족합니다.');
            }

            return true;
        });
    }
}
