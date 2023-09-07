<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OrderReceiptNotification extends Notification
{
    use Queueable;

    protected $receipt;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($receipt)
    {
        $this->receipt = $receipt;
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
            ->subject('Order Receipt')
            ->line('Your order has been delivered. Here is your receipt:')
            ->line(new HtmlString('<table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">Product</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Price (₱)</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Quantity</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Subtotal (₱)</th>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . $this->receipt->title . '</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">₱' . number_format($this->receipt->price, 2) . '</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . $this->receipt->qty . '</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">₱' . number_format($this->receipt->price * $this->receipt->qty, 2) . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;" colspan="3">Shipping Fee (₱)</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">₱' . number_format($this->receipt->shipping_fee, 2) . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;" colspan="3">Total Amount (₱)</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">₱' . number_format($this->receipt->total_amount, 2) . '</td>
                </tr>
            </table>'))
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