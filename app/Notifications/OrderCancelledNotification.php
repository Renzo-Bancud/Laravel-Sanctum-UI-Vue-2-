<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OrderCancelledNotification extends Notification
{
    use Queueable;

    protected $getUserOrder;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
   

    public function __construct($getUserOrder)
    {
        $this->getUserOrder = $getUserOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {   
    
        
        return (new MailMessage)
            ->subject('Order Cancelled')
            ->line('Your order has been cancelled.')
            ->line('Order ID: ' . $this->getUserOrder->order_id)
            ->line('Cancellation Notes: ' . $this->getUserOrder->notes)
            ->line('Thank you for using our service!')
            ->salutation('Regards,' . PHP_EOL . 'Mindoro Bodega Sale Shop');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}