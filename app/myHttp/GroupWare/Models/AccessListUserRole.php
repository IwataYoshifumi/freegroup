<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class AccessListUserRole extends Model {
    
    const     TABLE  = "access_list_user_role";
    protected $table = self::TABLE;
    protected $fillable = [ 'access_list_id', 'user_id', 'role' ];
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////
    public function access_list() {
        return $this->belongsTo( AccessList::class );
    }
    
    public function user() {
        return $this->belongsTo( User::class );
    }
    

    //////////////////////////////////////////////////////////////////////////
    //
    //　関連関数
    //
    //////////////////////////////////////////////////////////////////////////
    public static function get_table_name() {
        return self::TABLE;
    }
    public static function table_name() {
        return self::get_table_name();
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　DBクエリー関数
    //
    //////////////////////////////////////////////////////////////////////////
    
    public static function whereOwner( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        return self::where( 'role', 'owner' )->where( 'user_id', $user_id );
    }
    public static function whereWriter( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        
        return self::where( 'role', 'writer' )->where( 'user_id', $user_id );
    }
    public static function whereReader( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }    
    
        return self::where( 'role', 'reader' )->where( 'user_id', $user_id );
    }
    public static function whereCanWrite( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        return self::whereIn( 'role', [ 'owner', 'writer'] )->where( 'user_id', $user_id );
    }
    public static function whereCanRead( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        return self::whereIn( 'role', [ 'owner', 'writer', 'reader' ] )->where( 'user_id', $user_id );
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　DB　更新メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public static function updateWithAccessList( $access_list ) {
        if( $access_list instanceof AccessList ) {
            $access_list_id = $access_list->id;
        } elseif( is_numeric( $access_list )) {
            $access_list_id = $access_list;
            $access_list    = AccessList::find( $access_list_id );
        } else {
            die( __METHOD__ );
        }
        return AccessListUserRoleUpdate::AccessList( $access_list );        
    }

}