<?php

namespace App\Http\Controllers\Admin\Auth;

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
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest:admin')->except('logout');
    }
    
     protected function guard() {
        return Auth::guard('admin');
    }

    public function showLoginForm() {
        return view('admin.auth.login');
    }

    public function logout(Request $request) {
        Auth::guard('admin')->logout();
        return $this->loggedOut($request);
    }

    public function loggedOut(Request $request) {
        return redirect(route('admin.login'));
    }
    
    // ログイン、認証の設定（退社済み管理者はログイン不可）
    //
    public function credentials( Request $request ) {
        $credentials = $request->only( $this->username(), 'password');
        $credentials['retired'] = 'false';
        return $credentials;
    }
    
    //　ログイン通過後の処理(他のガードは自動ログアウト)
    //
    protected function authenticated(Request $request, $user) {
        if( Auth::guard('user')->check() ) { Auth::guard('user')->logout(); }
        // if( Auth::guard('admin')->check() ) { Auth::guard('admin')->logout(); }
        // if( Auth::guard('customer')->check() ) { Auth::guard('customer')->logout(); }
        return redirect()->intended($this->redirectPath());
    }
}
