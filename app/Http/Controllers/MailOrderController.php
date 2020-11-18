<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\MailOrderRequest;
use App\User;
use App\Mail\MailOrderToAdmin;

class MailOrderController extends Controller
{
    //
    public function create( ) {
        
        
        return view( "mail_order.create" );
        
        
    }
    
    public function store( MailOrderRequest $request ) {
    
        $order = $request->all();
        
        // dump( $order );
        // dump(  config( 'mail_order.notify_ordered.user_ids') );
        
        $mail_to = User::whereIn( 'id', config( 'mail_order.notify_ordered.user_ids'))->get();
        // dd( $mail_to );
        
        $mail = new MailOrderToAdmin( $order );

        //　管理者へメール送信
        // 
        $mail->subject( 'ウェブフォームから注文がありました。');
        foreach( $mail_to as $user ) {
            Mail::to( $user )->send( $mail );
        }
        
        //　顧客へ注文受付メール送信
        //
        $mail->subject( 'ご注文ありがとうございました。');
        $mail->view( 'mail_order.mail_to_customer');
        Mail::to( $order['input']['email'] )->send( $mail );
        
        // session()->regenerateToken();
        return view( "mail_order.done_order" )->with( 'order', $order );
         
        
    }
    
}
