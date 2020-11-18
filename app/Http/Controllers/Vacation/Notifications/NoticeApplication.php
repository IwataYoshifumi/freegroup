<?php

namespace App\Http\Controllers\Vacation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Vacation\Application;
use App\Models\Vacation\User;

class NoticeApplication extends Notification
{
    use Queueable;

    public $application;
    public $applicant;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( Application $app )
    {
        $this->application = $app;
        $this->applicant   = $app->user;
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
                    ->subject( "休暇承認のお願い" )
                    ->greeting( "お疲れ様です。" )
                    ->line( $this->applicant->name."より休暇申請がされました" )
                    ->line( "  " )
                    ->line( "申請者　：".$this->applicant->name )
                    ->line( "申請日　：".$this->application->date )
                    ->line( "休暇期間：".$this->application->start_date."～".$this->application->end_date )
                    ->line( "休暇日数：".$this->application->num."日")
                    ->line( "休暇理由：".$this->application->reason )
                    ->line('休暇の承認をお願いします。')
                    ->line( "" )
                    ->salutation( "結び" )
                    ->action('休暇承認業務', route( 'vacation.approval.select' ) ); 
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
