<?php
 
namespace App\Listeners;
 
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Verified;
// use App\Events\Registered;
use Illuminate\Auth\Events\Registered;

use App\Jobs\JobRegisteredMail;

class UserEventSubscriber
{

  public function __construct()
  {
  }
  
    /**
     * Handle user login events.
     */
    public function handleUserLogin($event) {}
 
    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event) {}
    public function handleUserVerified($event) {}

    public function handleUserRegister($event) {
      // $this->mailSvc->registerMail($event->user);
      // $data = new \stdClass;
      // $data->user = $event->user;
      // $data->subject = $event->user->name.'님의 회원가입정보입니다.';
      // $data->message = null;
      // dispatch(new JobRegisteredMail($data));
    }
 
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
      return [
        Login::class => 'handleUserLogin',
        Logout::class => 'handleUserLogout',
        Registered::class => 'handleUserRegister',
        Verified::class => 'handleUserVerified',
      ];
    }
}