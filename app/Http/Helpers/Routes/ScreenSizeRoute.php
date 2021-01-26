<?php
namespace App\Http\Helpers\Routes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Helpers\Controllers\ScreenSizeController;

class ScreenSizeRoute {
 
    static public function route() {

        Route::prefix( 'screensize' )->namespace( '\App\Http\Helpers\Controllers' )->group( function() {
        
            Route::get(  '/get',  'ScreenSizeController@get' )->name( 'screensize.get' );  
            Route::post( '/set',  'ScreenSizeController@set' )->name( 'screensize.set' );
     
            if( is_debug() ) {
                Route::get(  '/dump',   'ScreenSizeController@dump'      )->name( 'screensize.dump' );
                Route::get(  '/forget', 'ScreenSizeController@forget'    )->name( 'screensize.forget' );
            }
        });        
    }
}