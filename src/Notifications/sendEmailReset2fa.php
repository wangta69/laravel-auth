<?php

namespace Pondol\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendEmailReset2fa extends Notification  implements ShouldQueue

{
  use Queueable;

  /**
   * Create a new notification instance.
   *
   * @return void
   */

  public function __construct()
  {

  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $token=app('auth.password.broker')->createToken($notifiable);


    $actionUrl  = route('2fa.reset', [$token]);
    return (new MailMessage)->subject('['.config('app.name').'] 2FA Reset')->view(
      'auth.templates.mail.'.config('pondol-auth.template.mail').'.reset2fa',
      [
        'actionUrl' => $actionUrl,
        'notifiable' => $notifiable
      ]);
  }
}
