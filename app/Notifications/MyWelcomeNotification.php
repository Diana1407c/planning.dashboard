<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Spatie\WelcomeNotification\WelcomeNotification;

class MyWelcomeNotification extends WelcomeNotification
{
    protected function initializeNotificationProperties(User $user)
    {
        $this->user = $user;

        $this->user->welcome_valid_until = $this->validUntil;
        $this->user->save();

        $this->showWelcomeFormUrl = URL::temporarySignedRoute(
            'welcome',
            $this->validUntil,
            ['user' => $user->welcomeNotificationKeyValue()]
        );
    }

    public function buildWelcomeNotificationMessage(): MailMessage
    {
        return (new MailMessage())
            ->subject(Lang::get('Welcome'))
            ->line(Lang::get('You are receiving this email because an account was created for you.'))
            ->action(Lang::get('Set initial password'), $this->showWelcomeFormUrl)
            ->line(Lang::get('This welcome link will expire in :count minutes.', ['count' => $this->validUntil->diffInRealMinutes()]));
    }
    public function rules()
    {
        return [
            'password' => 'required|confirmed|min:6',
        ];
    }
}
