<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

use Illuminate\Auth\Notifications\ResetPassword;


class RestPasswordForAdmin extends ResetPassword {

    public function toMail( $notifiable ) {
        
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if (static::$createUrlCallback) {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            $url = url(route('admin.password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        }

        /*
        return (new MailMessage)
            ->subject( '管理者用' . Lang::get('passwords.email.subject'))
            ->line(    Lang::get('passwords.email.line1'))
            ->action(  Lang::get('auth.reset_password'), $url)
            ->line(    Lang::get('passwords.email.line2'))
            ->line(    Lang::get('passwords.email.line3'));
        
        */    
        return (new MailMessage)
            ->subject( '管理者用' . Lang::get('passwords.email.subject'))
            // ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('auth.reset_password'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

}
