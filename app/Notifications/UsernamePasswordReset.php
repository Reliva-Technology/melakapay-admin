<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UsernamePasswordReset extends Notification
{
    use Queueable;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $password = $this->user->new_password;
        return (new MailMessage)
            ->subject('Details Changes at MelakaPay')
            ->markdown('email.password-reset', [
                'password' => $password,
                'name' => $this->user->name,
                'username' => $this->user->username
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
