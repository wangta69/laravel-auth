<?php
namespace App\Http\Controllers\Auth\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\Traits\Admin\Config as tConfig;
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
    return view('auth.admin.users.config', $result);
  }

  public function update(Request $request) {
    $this->_update($request);
    return response()->json(['error'=>false]);
  }

}
