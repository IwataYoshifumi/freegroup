<?php

namespace App\myHttp\GroupWare\Notifications\GoogleCalendar;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

use App\myHttp\GroupWare\Models\CalProp;

class UnSyncGoogleCalendar extends Notification implements ShouldQueue
{
    use Queueable;

    public $calprop;

    public function __construct( CalProp $calprop ) {
        $this->calprop = $calprop;
    }

    public function via($notifiable) {
        return ['mail'];
    }    
    
    public function toMail($notifiable) {

        return (new MailMessage)
                    ->subject( "Googleカレンダー同期が解除されました。" )
                    ->greeting( "お疲れ様です。" )
                    ->line( "部署変更、又はカレンダー管理者によるアクセス権変更によって  " )
                    ->line( $this->calprop->name."のGoogleカレンダー同期が解除されました。" )
                    ->line( "  " )
                    ->line( "" )
                    ->action('カレンダー表示設定', route( 'groupware.calprop.show', [ 'calprop' => $this->calprop->id ] ) )
                    ->salutation( "結び" );
    }
    
    
}