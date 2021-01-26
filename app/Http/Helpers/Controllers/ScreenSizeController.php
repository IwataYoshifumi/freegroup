<?php

namespace App\Http\Helpers\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

use  App\Http\Helpers\BackButton;
use  App\Http\Helpers\ScreenSize;

class ScreenSizeController extends Controller {
    
    public function get( Request $request ) {
        
        BackButton::stackHere( $request );
        return view( 'helpers.ScreenSize.get_screen_size' );
    }
    
    public function set( Request $request ) {
        
        if_debug_log( __METHOD__, $request->width, $request->height );

        if( ScreenSize::checkRequest( $request ) ) {
            ScreenSize::setScreenSize( $request );
            return response()->json( [ 'status' => 'success' ] );
        } else {
            return response()->json( [ 'status' => 'error' ] );
        }
    }
    
    public function dump() {
        if_debug( __METHOD__, session( 'ScreenSize' ) );
        return view( 'helpers.ScreenSize.test' );
    }
    
    public function forget() {
        
        session()->forget( 'ScreenSize' );
        is_debug( __METHOD__, 'forgeted ScreenSize' );
        if_debug( __METHOD__, session( 'ScreenSize' ) );
        return view( 'helpers.ScreenSize.test' );
    }
    
}
