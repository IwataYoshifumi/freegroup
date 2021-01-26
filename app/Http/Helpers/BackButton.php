<?php 

namespace App\Http\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class BackButton {
    
    public const route_back_all = "back_all";
    public const route_back_one = "back_one";
    public const route_name = "back.to.there";
    public const path       = "/back_to_there";
    public const name       = "戻る";

    static public function setHere( Request $request ) {

        $session = self::get_session_values( $request );
        
        // if_debug( session()->all() );
        session()->forget( 'back_button' );
        session()->push( 'back_button', $session );
        return true;
    }
    
    static public function stackHere( Request $request ) {
        $privious = self::get_previous_session_values();
        $session  = self::get_session_values( $request );
        // if_debug( $privious['full_url'], $session['full_url'] );
        // if_debug( $privious['url'], $session['url'] );
        
        // if( $privious['full_url'] !== $session['full_url'] ) {
        if( $privious['url'] !== $session['url'] ) {
            session()->push( 'back_button', $session );
        } 
        return true;
    }

    //　１つ前のバックボタンセッションを削除する
    //
    static public function removePreviousSession() {
        $sessions = session('back_button');
        array_pop( $sessions );
        return session()->put( 'back_button', $sessions );
    }
    
    //　プライベート関数
    //
    static private function get_session_values( Request $request ) {

        $session = ['route_name'    => Route::currentRouteName() ,
                    'url'           => url()->current(),
                    'full_url'      => url()->full(),
                    'method'        => $request->method(),
                    ];
        $para = [];
        foreach( $request->all() as $key => $value ) {
            // if_debug( $key, $value );
            if( $key == "_token" ) { continue; }
            $para = array_merge( $para, [ $key => $value ]);
        }
        
        if( count( $para )) {
            $session = array_merge( $session, [ 'para' => $para ]);
        }
        return $session;
    }
    
    static private function get_previous_session_values() {

        $sessions = session( 'back_button' );
        if( is_array( $sessions )) {
            return $sessions[ array_key_last( $sessions )];
        } else {
            return null;
        }
    }
    
    // バックボタンを表示する。blade内で使用する
    //
    static public function form( $class = null ) {
        
        // $now = self::get_session_values( request() );
        // $privious = self::get_previous_session_values();
        
        $sessions = session()->get( 'back_button' );
        // if_debug( $sessions );
        
        if( count( $sessions ) >= 3 ) {
            $form  = "<a class='btn btn-secondary ".$class."' href='".route( self::route_back_all )."'>最初に戻る</a>&nbsp;";
            $form .= "<a class='btn btn-secondary ".$class."' href='".route( self::route_back_one )."'>１つ戻る</a>";
        } elseif( count( $sessions ) == 1 ) {
            $form = "";            
        } else {
            $form = "<a class='btn btn-secondary ".$class."' href='".route( self::route_back_one )."'>戻る</a>";
        }
        return new HtmlString( $form );
    }
    
    // コントローラー（１つ戻る）
    //
    static public function backOne() {
        
        $sessions = session()->get( 'back_button' );
        
        if( count( $sessions ) >= 2 ) {
            $session = array_pop( $sessions );
            $session = array_pop( $sessions );
            session()->put( 'back_button', $sessions );
        
        } elseif( count( $sessions ) == 1 ) {
            $session = array_pop( $sessions );
            session()->put( 'back_button', $sessions );
            
        } else {
            if( auth( 'user' )->check() ) {
                return redirect()->route( 'user.home' );
            } elseif( auth( 'admin' )->check() ) {
                return redirect()->route( 'admin.home' );
            } elseif( auth( 'customer' )->check() ) {
                return redirect()->route( 'customer.home' );
            } else {
                return redirect()->route( 'home' );
            }
            
        }
        return view( 'helpers.back_button.redirect_form' )->with( 'session', $session );

    }
    
    // コントローラー（最初に戻る）
    static public function backAll() {
        
        $sessions = session()->get( 'back_button' );
        $session = array_shift( $sessions );
    
        session()->forget( 'back_button' );
        
        return view( 'helpers.back_button.redirect_form' )->with( 'session', $session );
    }

    //　戻るボタンのルート
    //
    static public function route() {
        Route::prefix( 'back_button/' )->group( function() {
            
            // １つ戻るボタンのルート
            //
            Route::get( 'back_one', function() { return BackButton::backOne(); })->name( 'back_one' );
            
            //　全て戻るボタンのルート
            //
            Route::get( 'back_all', function() { return BackButton::backAll(); })->name( 'back_all' );
        });
    }
    

}
