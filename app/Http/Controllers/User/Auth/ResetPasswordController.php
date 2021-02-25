<?php

namespace App\Http\Controllers\User\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    
    protected $guard = "user";
    protected $view  = "user.auth.passwords.reset";
    protected $validator = "user.password.validator";

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    //　パスワードリセットフォームの表示( オーバーライド )
    //
    public function showResetForm(Request $request, $token = null) {
        // return view( 'user.auth.passwords.reset' )->with(
        // dd( $view );
        return view( $this->view )->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
    
    //　パスワードリセットの処理（オーバーライド）
    //
    protected function resetPassword($user, $password) {
        $this->setUserPassword($user, $password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
        // $this->guard()->login($user);
    }
    
    //　ガードの設定（オーバーライド）
    //
    protected function guard() {
        return Auth::guard( $this->guard );
    }
    
    // パスワードブロッカー（オーバーライド）
    //
    public function broker() {
        return Password::broker( 'users' );
    }
    
    //　パスワードのバリデーション
    //
    protected function rules() {
        $i = config( $this->validator );
        $validator = config( "password.validator.$i" );
        if( is_null( $validator )) { $validator = "min:8"; } 
       //   dd( $validator );
 
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', $validator ],
        ];
    }
    protected function validationErrorMessages() {
        
        $i = config( $this->validator );
        $error = config( "password.error.".$i ); 
        // dd( $this->validator,$i, $error );

        return $error;
    }

}
