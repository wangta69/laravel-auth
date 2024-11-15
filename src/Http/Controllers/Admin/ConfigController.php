<?php
namespace Pondol\Auth\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Pondol\Auth\Traits\Auth\Admin\Config as tConfig;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{

  use tConfig;

  public function __construct()
  {

  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {

    $result = $this->_index($request);
    return view('pondol-auth::admin.users.config', $result);
  }

  public function update(Request $request) {
    $this->_update($request);
    return response()->json(['error'=>false]);
  }

}
