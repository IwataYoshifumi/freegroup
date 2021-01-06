<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Groups;
use App\myHttp\GroupWare\Models\AccessList;

class ACL extends Model {
    
    protected $table = "acls";
    
    protected $fillable = [ 'order', 'role', 'access_list_id', 'aclable_id', 'aclable_type' ];
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////

    public function access_list() {
        return $this->belongsTo( AccessList::class );
    }
    
    public function aclable() {
        // morphTo User, Dept, Group
        // 
        return $this->morphTo();
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　DB クエリーメソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public static function whereUser( $id ) {
        return ACL::where( 'aclable_type', User::class )->where( 'aclable_id', $id );
    }

    public static function whereDept( $id ) {
        return ACL::where( 'aclable_type', Dept::class )->where( 'aclable_id', $id );
    }

    public static function whereGroup( $id ) {
        return ACL::where( 'aclable_type', Group::class )->where( 'aclable_id', $id );
    }

    //////////////////////////////////////////////////////////////////////////
    //
    //　値取得用メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public static function getRoleTypes() {
        return config( 'groupware.access_list.roles' );
    }


    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム利用用　メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public static function get_array_roles_for_select() {
        $array = [ '' => '' ];
        foreach( config( 'groupware.access_list.roles' ) as $role => $memo ) {
            $array[ $role ] = $memo;
        }
        return $array;        
    }
    
    public function aclable_url() {
        $aclable = $this->aclable;
        if( $this->aclable_type == User::class ) {
            return route( 'groupware.user.show', [ 'user' => $aclable->id ] );
        } elseif( $this->aclable_type == Dept::class ) {
            return route( 'dept.show', [ 'dept' => $aclable->id ] );
        } elseif( $this->aclable_type == Group::class ) {
            return route( 'groupware.group.show', [ 'group' => $aclable->id ] );
        } else {
            throw new Exception( "ACL get_aclable_show_link: No Aclable" );
        }
    }
    

    
    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム表示用メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public function p_type() {
        if( $this->aclable_type == User::class ) {
            return "ユーザ";
        } elseif( $this->aclable_type == Dept::class ) {
            return "部署";
        } elseif( $this->aclable_type == Group::class ) {
            return "グループ";
        } else {
            return "unknown";
        }
    }
    
    public function p_aclable_name() {
        if( $this->aclable_type == User::class ) {
            $user = $this->aclable;
            $dept = ( $user->dept ) ? " 【". $user->dept->name. "】" : "";
            $print =  $user->name. $dept;
            return $print;
        } else {
            return op( $this->aclable )->name;
        }
    }
    
}