<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AccountCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $temporaryPassword;

    public function __construct($user, $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {   
        $password = $this->user->password;
        // $logoUrl = asset('images/logo_admin.png');
        // $logoHtml = '<img src="' . $logoUrl . '" alt="Logo" height="200" width="200">';
        
        return (new MailMessage)
            ->subject('Account Created')
            ->greeting('Hello, '.$this->user->name.'!')
            ->line('Your account has been successfully created.')
            ->line('Here are your account details:')
            ->line('Email: '.$this->user->email)
            ->line('Temporary Password: '.$this->temporaryPassword)
            ->line('Please keep this information secure and do not share it with others.')
            ->line('You can now log in to your account using the provided credentials.')
            ->line('Thank you for joining our platform.')
            ->line('If you have any questions, feel free to contact us.')
            // ->line(new HtmlString($logoHtml))
            ->salutation('Regards,' . PHP_EOL . 'Mindoro Bodega Sale Shop');
    }
}
