<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;


class SetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable, $email = null)
    {
        $email = $email ?? $notifiable->getEmailForPasswordReset();

        return (new MailMessage())
            ->subject(Lang::get('Set Password Notification'))
            ->line(Lang::get('You are receiving this email because an account was created for you.Click the button below to set your password:'))
            ->action(Lang::get('Set initial password'), route('backpack.auth.password.reset.token', $this->token).'?email='.urlencode($email));
    }
}
