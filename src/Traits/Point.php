<?php
namespace Pondol\Auth\Traits;

use Pondol\Auth\Models\User\UserPoint;

trait Point
{
  /**
   * 입금및 게임시 처리 (별도의 hold_point를 처리하지 않는다.)
   * @param Object $user
   * @param String $item : 포인트 지불 flag(admino, order, event)
   * @param String $sub_item : $item 보다 디테일하게 처리, 가령 event 일경우 어떤 이벤트인지
   * @param  Integer $rel_item: 참조테이블의 ID
   */
  public function _insertPoint($user, $point, $item = null, $sub_item = null, $rel_item = null) {

    if(!$point) {
      return;
    }
    if(!$sub_item) {$sub_item = $item;}
    
    $cur_sum = $user->curPoint();
    $userPoint = new UserPoint;
    $userPoint->user_id = $user->id;
    $userPoint->point = $point;
    $userPoint->cur_sum = $cur_sum + $point;
    $userPoint->item = $item;
    $userPoint->sub_item = $sub_item;
    $userPoint->rel_item = $rel_item;

    $userPoint->save();

    $user->increment('point', $point);
    return;
  }

  // 회원가입 포인트
  public function _register($user) {
    $point = config('pondol-auth.point.register');
    if($point) {
      $this->_insertPoint($user, $point, 'register', null, $user->id);
    }
  }

  // 회원 로그인 포인트
  public function _login($user) {
    $point = config('pondol-auth.point.login');
    if($point) {
      $this->_insertPoint($user, $point, 'login', null, $user->id);
    }
  }
}
