<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('إعادة تعيين كلمة المرور — MindFitBro')
            ->view('emails.reset_password', [
                'url'  => $url,
                'name' => $notifiable->name,
                'expireMinutes' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
            ]);
    }
}
