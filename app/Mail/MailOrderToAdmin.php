<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailOrderToAdmin extends Mailable
{
    use Queueable, SerializesModels;

     public $order;
     public $view = "mail_order.mail_to_admin";

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $order )
    {
        //
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view( $this->view )->with( 'order', $this->order );
    }
    
    public function set_view( $view ) {
        $this->view = $view;
        return $this;
    }
}
