<?php
 
namespace App\Listeners;
 
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Events\Registered;

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


    public function handleUserRegister($event) {
      
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
      ];
    }
}