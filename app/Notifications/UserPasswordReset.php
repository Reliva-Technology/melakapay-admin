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
            ->subject('Details Changes at MelakaPay')
            ->greeting('Dear '.$this->user->name)
            ->line('This is to inform that we have made some changes in your user profile at MelakaPay, upon your request. The account details are as below:')
            ->line('Full Name: '.$this->user->name.'<br>User ID: '.$this->user->username)
            ->line('The changes made can be found below:')
            ->line('New Password: '.$this->user->new_password)
            ->line('If this is not your account / request, kindly contact our administrator using the contact details below the soonest:')
            ->line('Telephone No: +606-3333333 ext 7656')
            ->line('Email Address: melakapay_admin@melaka.gov.my')
            ->line('Address: Bahagian Teknologi Maklumat Dan Komunikasi, Aras 1, Blok Temenggong, Seri Negeri, Hang Tuah Jaya, 75450 Ayer Keroh, Melaka MALAYSIA');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
