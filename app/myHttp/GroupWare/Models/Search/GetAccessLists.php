<?php
namespace App\myHttp\GroupWare\Models\Search;

use DB;
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;

// access_list_user_role データベースを検索して、AccessLIstクラスのインスタンスと role のリストを返す の検索をする
//
class GetAccessLists {

    public static function isOwner( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->where( 'role', 'owner' )
                                    ->get()->pluck('access_list');
        return $result;
    }   

    public static function isWriter( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->where( 'role', 'writer' )
                                    ->get()->pluck('access_list');
        return $result;
    }
    
    public static function isReader( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->where( 'role', 'reader' )
                                    ->get()->pluck('access_list');
        return $result;
    }
    
    public static function isFreeBusyReader( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->where( 'role', 'freeBusyReader' )
                                    ->get()->pluck('access_list');
        return $result;
    }

    public static function canWrite( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->whereIn( 'role', ['owner', 'writer'] )
                                    ->get()->pluck('access_list');
        return $result;
        
    }   

    public static function canRead( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->whereIn( 'role', ['owner', 'writer', 'reader'] )
                                    ->get()->pluck('access_list');
        return $result;
    }

    public static function user( $user_id ) {
        self::check_input( $user_id );
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->get()->pluck('access_list');
        return $result;
    }
    
    public static function find( $user_id, $roles ) {
        self::check_input( $user_id );
        
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->whereIn( 'role', $roles )
                                    ->get();
        return $result;
    }
    

    private static function check_input( $user_id ) {
        if( empty( $user_id        )) { throw new Exception( "checkAccessList : Error 1"); }
        return true;
    }
    
    
}