<?php

namespace Pondol\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendEmailResetPasswordToken extends Notification  implements ShouldQueue

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

    // return (new MailMessage)
    //   ->greeting('Hello!')
    //   ->line('One of your invoices has been paid!')
    //   ->action('View Invoice', $actionUrl)
    //   ->line('Thank you for using our application!');
    $actionUrl  = route('password.reset', [$token]);
    return (new MailMessage)->subject('['.config('app.name').'] 비밀번호 초기화')->view(
      auth_theme('mail').'.resetpassword',
      [
        'actionUrl' => $actionUrl,
        'token' => $token,
        'notifiable' => $notifiable
      ]);
  }
}
