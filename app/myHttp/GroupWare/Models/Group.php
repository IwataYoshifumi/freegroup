<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Models\Traits\AccessListTrait;

class Group extends Model {
    
    use AccessListTrait;
    
    // use ModelRouteTrait;
    // public function __construct( array $attributes = [] ) {
    //     parent::__construct($attributes);
    //     // ModelRouteTraitで利用する変数
    //     // 
    //     $route_names = [ 'index'  => "groupware.group.index",
    //                     'show'   => "groupware.group.show",
    //                     'create' => "groupware.group.create",
    //                     'store'  => "groupware.group.store",
    //                     'edit'   => "groupware.group.edit",
    //                     'update' => "groupware.group.update",
    //                     'delete' => "groupware.group.delete",
    //     ];
    //     $this->initModelRouteTrait( 'group', $route_names );
    // }
    
    protected $fillable = [ 'name', 'memo', ];
    
    private $access_list_id;
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////
    
    //　グループに登録されているユーザを返す
    //
    public function users() {
        return $this->morphedByMany( User::class, 'groupable' );
    }

    public function acls() {
        return $this->morphMany( ACL::class, 'aclable' );
    }
    
    public function access_lists() {
        return $this->morphToMany( AccessList::class, 'accesslistable' );
    }

    //////////////////////////////////////////////////////////////////////////
    //
    //　権限関連（ return Users )
    //
    //////////////////////////////////////////////////////////////////////////
    public function owners() {
    }
    
    public function writers() {
    }
    
    public function readers() {
    }
    
    public function access_list() {
        return $this->access_lists->first();
    }

    public function access_list_id() {
        return optional( $this->access_lists()->first() )->id;
    }
    

    
    //////////////////////////////////////////////////////////////////////////
    //
    //  アクセスリストの依存性チェック
    //
    //////////////////////////////////////////////////////////////////////////

    

    //　ユーザがそのグループの管理者権限を持っているか確認する（管理者権限があれば true ）
    //
    public static function check_if_the_user_in_the_group_is_owner( $group_id, $user_id ) {
        
        // ユーザがそのグループに含まれているかチェック
        //
        $result = User::find( $user_id )->groups()->where( 'id', $group_id )->first();
        $group_id_if_user_belongs = ( optional( $result )->id ) ? $result->id : 0; 
        
        // グループがオーナー設定で使われているアクセスリストを検索                                    
        //
        $acls = ACL::where( 'aclable_type', Group::class )
                   ->where( 'aclable_id',   $group_id_if_user_belongs )
                   ->where( 'role', 'owner')
                //   ->get();
                   ->count();     
        
        // return ( ! empty( $acls ) ) ? 1 : 0 ; get_array_for_input_formget_array_for_input_form
        return ! empty( $acls ); 
    }
    
    public function check_if_the_user_in_this_is_owner( $user_id ) {
        return self::check_if_the_user_in_the_group_is_owner( $this->id, $user_id );
    }


    

}