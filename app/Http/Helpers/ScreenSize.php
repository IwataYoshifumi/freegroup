<?php 

namespace App\Http\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ScreenSize {
    
    public const xs = 575;
    public const sm = 767;
    public const md = 991;
    public const lg = 1199;
    public const xl = 1200;

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  画面サイズ取得関連メソッド
    //
    ////////////////////////////////////////////////////////////////////////////////

    public static function checkRequest( Request $request ) {
        
        if( ! is_int( $request->height ) and $request->height < 1 ) { return false; } 
        if( ! is_int( $request->width  ) and $request->width  < 1 ) { return false; } 
        return true;
    }
    
    public static function setScreenSize( Request $request ) {
        
        $array = [  'width'  => ( int ) $request->width, 
                    'height' => ( int ) $request->height,
                    'updated_at' => now()->toAtomString(),
        ];
        if_debug_log( __METHOD__, $request->width, $request->height );
        
        return session( [ 'ScreenSize' => $array ] );
    }

    public static function getScreenSize() {
        if_debug_log( __METHOD__ );
        return redirect()->route( 'screensize.get' );
    }
    
    //　Bladeファイル内で呼び出して、ScreenSizeを取得するJavaスクリプトを実行する
    //
    public static function rendarScriptToGetScreenSize() {
        
        $route_to_send_screen_size = route( 'screensize.set' );
        $csrf_token = csrf_token();

        $script = "
            <script>
                var url_to_send_screen_size = '$route_to_send_screen_size';
                
                var fd = new FormData();
                fd.append( 'width',  $(window).width()   );
                fd.append( 'height', $(window).height()   );
                fd.append( '_token', '$csrf_token' );
                console.log( fd );
                
                $.ajax({ 
                    url: url_to_send_screen_size,
                    method: 'POST',
                    dataType: 'json',
                    contentType:false,
                    processData: false,
                    cache: false,
                    data: fd,
                    
                }).done( function( data, status, xhr ) {
                    console.log( data, status, xhr ); 
                    
                });
            </script>
            ";
        return new HtmlString( $script );
        
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    //  画面サイズ確認メソッド
    //
    ////////////////////////////////////////////////////////////////////////////////
    
    public static function isSet() {
        $screen_size = session( 'ScreenSize' );
        return ( empty( $screen_size )) ? false : true;
    }
    
    public static function getWidth() {
        return ( self::isSet() ) ? session( 'ScreenSize.width' ) : null;
    }

    public static function getHeight() {
        return ( self::isSet() ) ? session( 'ScreenSize.height' ) : null;
    }
    
    public static function isMobile() {
        if( ! self::isSet() ) { return null; }
        
        $width = session( 'ScreenSize.width' );
        return ( $width < self::md ) ? true : false;
    }
    
    public static function isPCTablet() {
        if( ! self::isSet() ) { return null; } 
        return ! self::isMobile();
    }
    
    public static function isPC() {
        if( ! self::isSet() ) { return false; }
        
        $width = session( 'ScreenSize.width' );
        return ( $width > self::md ) ? true : false;
    }
    
    public static function isLarge() {
        return self::isPC();
    }
}
