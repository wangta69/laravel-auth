<?php

namespace Pondol\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class sendEmailVerificationNotification extends Notification  implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct()
  {
      //
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

    $actionUrl  = $this->verificationUrl($notifiable);


    // return (new MailMessage)
    //   ->greeting('Hello!')
    //   ->line('One of your invoices has been paid!')
    //   ->action('View Invoice', $actionUrl)
    //   ->line('Thank you for using our application!');

    return (new MailMessage)->subject('Verify your account')->view(
      auth_theme('mail').'.verify',
      [
        'notifiable' => $notifiable,
        'actionUrl' => $actionUrl,
      ]);

  }

  protected function verificationUrl($notifiable)
  {
    // if (static::$createUrlCallback) {
    //   return call_user_func(static::$createUrlCallback, $notifiable);
    // }

    return URL::temporarySignedRoute(
      'verification.verify',
      Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
      [
        'id' => $notifiable->getKey(),
        'hash' => sha1($notifiable->getEmailForVerification()),
      ]
    );
  }
}
