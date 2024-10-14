<?php
namespace App\Traits;

use App\Models\Auth\User\UserPoint;

trait Point
{
  /**
   * 입금및 게임시 처리 (별도의 hold_point를 처리하지 않는다.)
   * @param Object $user
   * @param String $item : admin |
   * \App\Services\PointService::insertPoint($user, $point, 'gameCrypto', 'win', $player->id);
   * \App\Services\PointService::insertPoint($user, $point, 'gameFx', 'win', $player->id);
   * \App\Services\PointService::insertPoint($user, $amount, 'deposit', $symbol, $deposit->id);
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

  public function _register($user) {
    $point = config('auth-pondol.point.register');
    if($point) {
      $this->_insertPoint($user, $point, 'register', null, $user->id);
    }
  }

  public function _login($user) {
    $point = config('auth-pondol.point.login');
    if($point) {
      $this->_insertPoint($user, $point, 'login', null, $user->id);
    }
  }


}
