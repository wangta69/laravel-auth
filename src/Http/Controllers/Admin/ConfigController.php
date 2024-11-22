<?php
namespace Pondol\Auth\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Pondol\Auth\Traits\Admin\Config as tConfig;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{

  use tConfig; // getConfig, _update

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
    $result = $this->getConfig($request);
    return view('pondol-auth::admin.configs.config', $result);
  }

  public function update(Request $request) {
    $this->_update($request);
    return response()->json(['error'=>false]);
  }

  public function termsofuse(Request $request)
  {
    $key = 'user.aggrement.term-of-use';
    $msg = $this->getAgreement($key);
    return view('pondol-auth::admin.configs.term-of-use', compact('key', 'msg'));
  }

  public function privacypolicy(Request $request)
  {
    $key = 'user.aggrement.privacy-policy';
    $msg = $this->getAgreement($key);
    return view('pondol-auth::admin.configs.privacy-policy', compact('key', 'msg'));
  }

  public function updateAgreement(Request $request) {
    $this->setAgreement($request->key, $request->value);
    return response()->json(['error'=>false]);
  }

}
