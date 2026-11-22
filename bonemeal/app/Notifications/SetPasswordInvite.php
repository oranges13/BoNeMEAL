<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetPasswordInvite extends Notification
{
    public function __construct(public string $url) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Set your '.config('app.name').' password')
            ->line('An account has been set up for you on '.config('app.name').'. Use the link below to choose a password.')
            ->action('Set password', $this->url)
            ->line('This link expires in '.config('auth.passwords.users.expire').' minutes. If you were not expecting this, ignore this email.');
    }
}
