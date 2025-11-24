<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordMail extends ResetPasswordBase
{
    public function toMail($notifiable)
    {
        // Frontend URL where user will reset password
        $url = config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . $notifiable->email;

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we requested a password reset for your account.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
