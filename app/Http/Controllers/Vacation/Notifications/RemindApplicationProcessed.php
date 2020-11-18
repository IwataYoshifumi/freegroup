<?php

namespace App\Http\Controllers\Vacation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Vacation\Application;
use App\Models\Vacation\User;

class RemindApplicationProcessed extends Notification
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
                    ->subject( "休暇完了処理のお願い" )
                    ->greeting( "休暇完了処理のお願い" )
                    ->line( "下記休暇申請は承認されていますが、休暇の取得完了処理がされていません。" )
                    ->line( "休暇の取得完了処理をしないと総務部で確認が取れず、欠勤扱いになることがあります。")
                    ->line( "下記より処理をお願いします。なお休暇申請したが、結局休暇を取得しなかった場合は「取り下げ」を行ってください。")
                    ->line( "申請者　：".$this->applicant->name )
                    ->line( "申請日　：".$this->application->date )
                    ->line( "休暇期間：".$this->application->start_date."～".$this->application->end_date )
                    ->line( "休暇日数：".$this->application->num."日")
                    ->line( "休暇理由：".$this->application->reason )
                    ->line( "" )
                    ->salutation( "休暇申請DB　総務部" )
                    ->action('休暇完了処理', route( 'vacation.application.show', ['application' => $this->application ] )); 
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
