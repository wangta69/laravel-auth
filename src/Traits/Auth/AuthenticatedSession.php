<?php
namespace App\Traits\Auth;

trait AuthenticatedSession {

  private function storeToLog($user) {
    $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']:"";
    $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN']:"";
    $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']:"";
    $remote_addr = $this->getRealIpAddr();

    $log = new \App\Models\Auth\User\UserLog;
    $log->user_id = $user->id;
    $log->http_referer = $http_referer;
    $log->http_origin = $http_origin;
    $log->http_user_agent = $http_user_agent;
    $log->remote_addr = $remote_addr;

    $log->save();
    return array("result"=>true);//, "inserted_id"=>$betting->id
  }


  private function getRealIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP']) && getenv('HTTP_CLIENT_IP')){
      return $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && getenv('HTTP_X_FORWARDED_FOR')){
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else if(!empty($_SERVER['REMOTE_HOST']) && getenv('REMOTE_HOST')){
      return $_SERVER['REMOTE_HOST'];
    }else if(!empty($_SERVER['REMOTE_ADDR']) && getenv('REMOTE_ADDR')){
      return $_SERVER['REMOTE_ADDR'];
    }
    return false;
  }
  
}