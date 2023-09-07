<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\EmailVerificationCodeNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'email_verification_code',
        'isOnline'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateEmailVerificationCode()
    {
        $verificationCode = rand(100000, 999999);
        $this->email_verification_code = $verificationCode;
        $this->save();

        return $verificationCode;
    }


    public function sendEmailVerificationCode($verificationCode)
    {
        $this->notify(new EmailVerificationCodeNotification($verificationCode));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }


    public function user_contact()
    {
        return $this->hasOne(User_contact::class); //n your scenario, the hasOne relationship is used because you want to establish a one-to-one relationship between the User model and the UserContact model. This means that each user should have only one associated contact record in the user_contacts table.
    }

}