<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    
    //　リセットパスワードフォーム（オーバーライド）
    //
    public function showLinkRequestForm() {
        return view( 'user.auth.passwords.email' );
    }

    //　パスワードブロッカー（オーバーライド）
    //
    public function broker() {
        return Password::broker( 'users' );
    }
    
    
    // public function sendResetLinkEmail( Request $request ) {
    //      dd( $request, Route::currentRouteName() );
    //  }

}
