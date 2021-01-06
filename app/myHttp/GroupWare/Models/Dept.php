<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\Dept as OriginalDept;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

class Dept extends OriginalDept {

    //　リレーションの定義
    //
    public function users() {
        return $this->hasMany( User::class );
    }
    
    public function acls() {
        return $this->morphMany( ACL::class, 'aclable' );
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム用　配列取得関数
    //
    //////////////////////////////////////////////////////////////////////////
    
    //　セレクトフォーム用配列
    //
    public static function get_array_for_select() {
        
        $depts = Dept::all();
        $array = [ '' => '' ];
        foreach( $depts as $d ) {
            $array[ $d->id ] = $d->name;
        }
        return $array;        
    }
    
    public function getUserIDs() {
        
        $array = [];
        foreach( $this->users as $i => $user ) {
            $array[$i] = $user->id;
        }
        return $array;
        
        
    }

}
