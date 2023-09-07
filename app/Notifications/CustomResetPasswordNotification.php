<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class CustomResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        $resetUrl = url(config('app.url').route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expiration = Carbon::now()->addMinutes(config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60));

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $resetUrl)
            ->line(Lang::get('This password reset link will expire at :expiration.', ['expiration' => $expiration]))
            ->salutation('Regards,' . PHP_EOL . 'Mindoro Bodega Sale Shop');
    }
}
