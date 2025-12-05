<?php

namespace Pondol\Auth\Traits;

use Pondol\Auth\Services\PointService;
use Pondol\Common\Facades\JsonKeyValue;

trait Point
{
    /**
     * 입금및 게임시 처리 (별도의 hold_point를 처리하지 않는다.)
     *
     * @param  object  $user
     * @param  string  $item  : 포인트 지불 flag(admino, order, event)
     * @param  string  $sub_item  : $item 보다 디테일하게 처리, 가령 event 일경우 어떤 이벤트인지
     * @param  int  $rel_item:  참조테이블의 ID
     */
    public function _insertPoint($user, $point, $item = null, $sub_item = null, $rel_item = null)
    {
        $service = new PointService;

        // 기존 코드는 is_paid가 없었으므로 false(무상)로 처리
        return $service->record($user, $point, $item, $sub_item, $rel_item, false);
    }

    // 회원가입 포인트
    public function _register($user)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $point = $auth_cfg->point->register;
        if ($point) {
            $this->_insertPoint($user, $point, 'register', null, $user->id);
        }
    }

    // 회원 로그인 포인트
    public function _login($user)
    {
        $auth_cfg = JsonKeyValue::getAsJson('auth');
        $point = $auth_cfg->point->login;
        if ($point) {
            $this->_insertPoint($user, $point, 'login', null, $user->id);
        }
    }
}
