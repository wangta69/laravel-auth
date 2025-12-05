<?php

namespace Pondol\Auth\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;
use Pondol\Auth\Events\Registered;
use Pondol\Auth\Events\ResetPasswordToken;
use Pondol\Auth\Notifications\sendEmailRegisteredNotification;
use Pondol\Auth\Notifications\sendEmailResetPasswordToken;
use Pondol\Auth\Services\PointService;

class UserEventSubscriber
{
    protected $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event)
    {
        $this->pointService->grantLoginPoint($event->user);
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event) {}

    public function handleUserVerified(Verified $event) {}

    public function handleUserRegister(Registered $event)
    {
        $this->pointService->grantRegisterPoint($event->user);
        $event->user->notify(new sendEmailRegisteredNotification);
    }

    public function handleUserResetPasswordToken(ResetPasswordToken $event)
    {
        $event->user->notify(new sendEmailResetPasswordToken);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array
     */
    public function subscribe(Dispatcher $events)
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
            Registered::class => 'handleUserRegister',
            Verified::class => 'handleUserVerified',
            ResetPasswordToken::class => 'handleUserResetPasswordToken',
        ];
    }
}
