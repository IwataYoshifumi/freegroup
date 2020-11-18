<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::USER_HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user')->except('logout');
    }
    
    // Guardの認証方法を指定
    protected function guard() {
        return Auth::guard('user');
    }

    // ログイン画面
    public function showLoginForm() {
        return view('user.auth.login');
    }

    // ログアウト処理
    public function logout(Request $request) {
        Auth::guard('user')->logout();

        return $this->loggedOut($request);
    }

    // ログアウトした時のリダイレクト先
    public function loggedOut(Request $request) {
        return redirect(route('user.login'));
    }
    
    // ログイン、認証の設定（退社社員はログイン不可）
    //
    public function credentials( Request $request ) {
        $credentials = $request->only( $this->username(), 'password');
        $credentials['retired'] = 'false';
        return $credentials;
    }
    
    //　ログイン通過後の処理（他のガードは自動ログアウト）
    //
    protected function authenticated(Request $request, $user) {
        
        if( Auth::guard('admin')->check() ) { Auth::guard('admin')->logout(); }
        
        return redirect()->intended($this->redirectPath());
    }
}
