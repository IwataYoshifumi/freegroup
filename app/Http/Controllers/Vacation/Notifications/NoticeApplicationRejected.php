<?php

namespace App\Http\Controllers\Vacation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Vacation\Application;
use App\Models\Vacation\User;

class NoticeApplicationRejected extends Notification
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
                    ->subject( '休暇申請が却下されました')
                    ->greeting( "下記の休暇申請が却下されました" )
                    ->line( "申請者　：".$this->applicant->name )
                    ->line( "申請日　：".$this->application->date )
                    ->line( "休暇期間：".$this->application->start_date."～".$this->application->end_date )
                    ->line( "休暇日数：".$this->application->num."日")
                    ->line( "休暇理由：".$this->application->reason )
                    ->salutation( "結び" )
                    ->action('申請した休暇を見る', route( 'vacation.application.show', ['application' => $this->application ] ) )
                    ->line( "再度休暇申請する場合は、承認者に確認をとって再申請をお願いします。");
                    
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
