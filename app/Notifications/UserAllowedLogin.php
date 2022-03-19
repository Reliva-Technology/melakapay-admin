<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Profile;

class UserAllowedLogin extends Notification
{
    use Queueable;

    public function __construct($profile)
    {
        $this->profile = $profile;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account login at MelakaPay')
            ->greeting('Dear '.$this->name)
            ->line('This is to inform that we have allowed your ebayar login at MelakaPay. The account details are as below:')
            ->line('Full Name: '.$this->name)
            ->line('User ID: '.$this->username)
            ->line('Temporary Password: '.$this->username)
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
