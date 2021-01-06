<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\RoleGroup;

class RoleList extends Model {
    
    protected $fillable = [ 'role', 'memo', 'rolegroup_id' ];
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////

    public function group() {
        return $this->role_group();
    }

    public function role_group() {
        return $this->belongsTo( RoleGroup::class );
    }

    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム利用用　メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    
    static public function getRoles() {
        return config( 'groupware.rolelist' );
    }
    

}