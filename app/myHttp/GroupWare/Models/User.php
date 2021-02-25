<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\User as OriginalUser;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\ScheduleType;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;


use App\myHttp\GroupWare\Models\Initialization\InitUser;


class User extends OriginalUser {

    public function dept() {
        return $this->belongsTo( Dept::class );
    }
    public function depertmant() {
        return $this->dept();
    }

    //自分が作成したスケジュール・日報
    //
    public function schedules() {
        return $this->hasMany( Schedule::class );
    }
    public function reports() {
        return $this->hasMany( Report::class );
    }
    public function files() {
        return $this->hasMany( MyFile::class );
    }
    // public function schedule_types() {
    //     return $this->hasMany( ScheduleType::class );
    // }
    //　関連社員として紐づけられたスケジュール・日報
    //
    public function allocated_schedules() {
        return $this->morphToMany( Schedule::class, 'scheduleable' );
    }
    public function allocated_reports() {
        return $this->morphToMany( Report::class, 'reportable' );
    }
    public function groups() {
         return $this->morphToMany( Group::class, 'groupable' );
    }
    
    //  ユーザロール・ロールグループ関連
    //
    public function role_groups() {
        return $this->morphToMany( RoleGroup::class, 'rolegroupable' );
    }
    
    public function acls() {
        return $this->morphMany( ACL::class, 'aclable' );
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　ロールグループ関連メソッド
    //  アクセスリスト関連メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    
    //　ロールグループ関連
    //
    public function role_group() {
        return $this->morphToMany( RoleGroup::class, 'rolegroupable' )->first();
    }    
    public function hasRoleGroup() {
        return $this->role_groups()->count() == 1;
    }
    public function setRoleGroup( RoleGroup $role_group ) {
        $role_group->users()->attach( [ $this->id ] );
    }
    public function hasRole( string $role ) {
        $subquery1 = RoleList::where( 'role', $role )->select( 'role_group_id' );
        $subquery2 = RoleGroup::whereIn( 'id', $subquery1 )->select('id');
        return $this->role_groups()->whereIn( 'id', $subquery2 )->count() === 1;
    }

    //　アクセスリスト関連
    //
    public function hasAccessListsWhoCanRead() {
        $access_lists = AccessList::whereCanRead( $this )->count();
        return $access_lists >= 1;
    }
    
    public function hasAccessListsWhoCanWrite() {
        $access_lists = AccessList::whereCanWrite( $this )->count();
        return $access_lists >= 1;
    }
    
    public function hasAccessListsWhoIsOwner() {
        $access_lists = AccessList::whereOwner( $this )->count();
        return $access_lists >= 1;
    }


    //////////////////////////////////////////////////////////////////////////
    //
    //　ユーザの状態取得メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    
    
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム用メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public static function get_array_for_select( $find ) {
        
        if( ! is_array( $find )) { throw new Exception( "User::get_array_for_select : Error 1"); }
        $users = self::search( $find );
        $array = [ "" => "" ];
        foreach( $users as $user ) {
            $array[ $user->id ] = $user->name;
        }
        return $array;
    }
}
