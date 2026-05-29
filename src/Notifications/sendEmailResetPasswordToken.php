<?php

namespace Pondol\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendEmailResetPasswordToken extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct() {}

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
        $token = app('auth.password.broker')->createToken($notifiable);

        // 이메일을 쿼리 스트링으로 포함시킵니다.
        $actionUrl = route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('['.config('app.name').'] 비밀번호 재설정 요청')
            ->view(auth_theme('mail').'.resetpassword', [
                'actionUrl' => $actionUrl,
                'token' => $token,
                'notifiable' => $notifiable,
            ]);
    }
}
