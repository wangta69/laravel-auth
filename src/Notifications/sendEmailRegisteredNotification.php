<?php

namespace Pondol\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendEmailRegisteredNotification extends Notification  implements ShouldQueue
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
    // return (new MailMessage)
    //   ->greeting('Hello!')
    //   ->line('One of your invoices has been paid!')
    //   ->action('View Invoice', $actionUrl)
    //   ->line('Thank you for using our application!');

    return (new MailMessage)->subject('['.config('app.name').'] 회원가입안내 메일')->view(
      'auth.templates.mail.'.config('pondol-auth.template.mail').'.register',
      [
        'notifiable' => $notifiable
      ]);

  }


}
