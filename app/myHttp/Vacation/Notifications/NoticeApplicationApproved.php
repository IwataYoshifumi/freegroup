<?php

namespace App\Http\Controllers\Vacation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Vacation\Application;
use App\Models\Vacation\User;

class NoticeApplicationApproved extends Notification
{
    use Queueable;

    private $application;
    private $applicant;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $application )
    {
        //
        $this->application = $application;    
        $this->applicant   = $application->user;
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
                    ->subject( '休暇申請が承認されました')
                    ->greeting( "下記の休暇申請が承認されました" )
                    ->line( "申請者　：".$this->applicant->name )
                    ->line( "申請日　：".$this->application->date )
                    ->line( "休暇期間：".$this->application->start_date."～".$this->application->end_date )
                    ->line( "休暇日数：".$this->application->num."日")
                    ->line( "休暇理由：".$this->application->reason )
                    ->salutation( "結び" )
                    ->action('承認された休暇を見る', route( 'vacation.application.show', ['application' => $this->application ] ) )
                    ->line( "休暇取得後に必ず「休暇取得の完了処理」を本システムで行ってください。
                    　　　　「休暇取得完了処理」をしないと総務で休暇を取った旨確認できず、欠勤扱いになることがあります");
                    
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
