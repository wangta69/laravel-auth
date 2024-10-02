<?php
namespace Pondol\Auth\Https\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Validator;
use Response;

use Pondol\Auth\Traits\Tracking;

class AuthController  { //  extends Controller

  use Tracking;

  public function __construct() {
  }


  public function search() {

    return view('tracking::search', [
      'couriers' => $this->_couriers()
    ]);
  }

/*
    * Tracking delivery
    *
    * @param String $courier 택배사 코드
    * @param  String invoicenumber  송장번호
    */
  public function tracking(Request $request, $courier, $invoicenumber, $type='json')
  {
    $_courier = $this->_courier($courier);
    if($_courier === false) {
      $result = (object)['error'=>'courierNotFoundError'];
      switch($type) {
        case 'html':
          return view('tracking::show', ['result'=>$result]);
        default:
          return response()->json($result, 203);//500, 203
      }
    }

    $result = (object)$this->_tracking($courier, $invoicenumber);
    $result->invoicenumber = $invoicenumber;
    $result->courier = $_courier;

    switch($type) {
      case 'html':

        return view('tracking::show', [
          'result'=>$result,
        ]);
      default:
        return response()->json($result, 200);//500, 203
    }
  }

  public function couriers() {
    return $this->_couriers();
  }
}
