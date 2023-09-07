<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class EmailVerificationCodeNotification extends Notification
{
    private $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
    

        $verificationCodeHtml = '<p style="text-align: center; font-weight: bold; font-size: 25px;">' . $this->verificationCode . '</p>';

        return (new MailMessage)
            ->subject(Lang::get('Email Verification Code'))
            ->greeting('Hello!')
            ->line(Lang::get('Here is your email verification code:'))
            ->line(new HtmlString($verificationCodeHtml))
            ->line(Lang::get('Please use this code to verify your email.'))
            ->line(Lang::get('This verification code will expire in 30 minutes'))
            ->salutation('Regards,' . PHP_EOL . 'Mindoro Bodega Sale Shop');
    }
}