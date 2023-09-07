<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    public function generateOrderID()
    {
        $timestamp = time(); // Get current timestamp
        $randomNum = rand(1000, 9999); // Generate a random number between 1000 and 9999
        $orderID = $timestamp . '-' . $randomNum; // Combine timestamp and random number
        return $orderID;
    }

    public function sendEmailCOD()
    {
        Mail::send([], [], function (Message $message) {
            $message->subject('COD Payment Confirmation');
            $message->setBody('
            <html>
            <head>
                <style>
                    body {
                        text-align: center;
                        font-family: Arial, sans-serif;
                    }
                    h1 {
                        color: #333333;
                        font-size: 24px;
                        margin-bottom: 20px;
                    }
                    p {
                        color: #666666;
                        font-size: 16px;
                        margin-bottom: 10px;
                    }
                    img {
                        display: block;
                        margin: 0 auto;
                        max-width: 100%;
                        height: auto;
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <img src="' . asset('images/logo_admin.png') . '" alt="Logo" />
                <h1>COD Payment Confirmation</h1>
                <p>Thank you for choosing Cash on Delivery (COD) as your payment method.</p>
                <p>Your order will be processed and prepared for delivery.</p>
                <p>Please be ready to receive your delivery when it arrives.</p>
                <p>For any inquiries, please contact our customer support.</p>
            </body>
            </html>
        ', 'text/html');
            $message->to(Auth::user()->email);
        });
    }

}