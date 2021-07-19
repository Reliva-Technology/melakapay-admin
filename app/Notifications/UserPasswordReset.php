<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserPasswordReset extends Notification
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
        return (new MailMessage)
            ->line('Your password has been reset by the admin. Please check the details as below:')
            ->line('Username: '.$this->user->username)
            ->line('Password: '.$this->user->new_password)
            ->line('Please contact the user immediately (if needed).');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
