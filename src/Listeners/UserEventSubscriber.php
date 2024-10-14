<?php
 
namespace App\Listeners;
 
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Verified;
// use App\Events\Registered;
use Illuminate\Auth\Events\Registered;
// use Illuminate\Auth\Events\Registered;

use Illuminate\Events\Dispatcher;
use App\Notifications\sendEmailRegisteredNotification;
use App\Traits\Point;
class UserEventSubscriber
{

  use Point;
  public function __construct()
  {
  }
  
    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event) {
      \Log::info('============== handleUserLogin');
      \Log::info(json_encode($event));
      $this->_login($event->user); // 로그인 포인트
    }
 
    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event) {
      \Log::info('============== handleUserLogout');
      \Log::info(json_encode($event));
    }
    public function handleUserVerified(Verified $event) {
      \Log::info('============== handleUserVerified');
      \Log::info(json_encode($event));
    }

    public function handleUserRegister(Registered $event) {
      \Log::info('============== handleUserRegister');
      \Log::info(json_encode($event));
      $this->_register($event->user); // 회원가입 포인트
      $event->user->notify(new sendEmailRegisteredNotification);
    }
 
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe(Dispatcher $events)
    {

      \Log::info('UserEventSubscriber subscribe');
      \Log::info(json_encode($events));

      // $events->listen(
      //   Login::class,
      //   [UserEventSubscriber::class, 'handleUserLogin']
      // );

      // $events->listen(
      //   Logout::class,
      //   [UserEventSubscriber::class, 'handleUserLogout']
      // );

      return [
        Login::class => 'handleUserLogin',
        Logout::class => 'handleUserLogout',
        Registered::class => 'handleUserRegister',
        Verified::class => 'handleUserVerified',
      ];
    }
}