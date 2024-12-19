<?php
namespace Pondol\Auth\Events;


class ResetPasswordToken
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
      $this->user = $user;
    }
}
