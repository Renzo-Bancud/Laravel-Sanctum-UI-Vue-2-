<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderisPlaced extends Notification
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
        ->subject('Thank you for placing your order!')
        ->line('Your order is currently pending and being processed. To check your order status, please follow these steps:')
        ->line('1. Click on your name in the top left corner of the page.')
        ->line('2. Select the "Checkout" tab from the dropdown menu.')
        ->line("Your order will be prepared for delivery, and we'll notify you once it's ready to be dispatched. Please be ready to receive your delivery when it arrives.")
        ->line('Order ID:'.' '.$this->getUserOrder->order_id)
        ->line('Amount:'.' '.'₱'.number_format($this->getUserOrder->total_amount))
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
