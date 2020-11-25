<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleList;

class RoleGroup extends Model {
    
    protected $fillable = [ 'name', 'memo', ];
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////

    public function users() {
        return $this->morphedByMany( User::class, 'rolegroupable' );
    }
    
    public function lists() {
        return $this->role_lists();
    }
    
    public function role_lists() {
        return $this->hasMany( RoleList::class );
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーションに対するアクション
    //
    //////////////////////////////////////////////////////////////////////////
    
    public function clear_lists() {
        return RoleList::where( 'role_group_id', $this->id )->delete();
    }
    
    // $array はroleの配列 [ 'CanCreate・・・', 'CantCreate・・・'　]
    //
    public function update_lists( $array ) {

        $this->clear_lists();
        
        if( is_array( $array ) and count( $array )) {
            $lists = [];
            foreach( $array as $r ) {
                array_push( $lists, [ 'role' => $r ] );    
            }
            $this->role_lists()->createMany( $lists );
        }
        return $this;
    }
    
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　データベースのアクセス
    //
    //////////////////////////////////////////////////////////////////////////
    
    public static function creates( Request $request ) {
        
        $role_group = DB::transaction( function() use( $request ) {

            if( $request->default ) {
                RoleGroup::where( 'default', true )->update( ['default'=> false] );
            }
            $role_group = new RoleGroup;
            $role_group->name    = $request->name;
            $role_group->memo    = $request->memo;
            $role_group->default = ( $request->default ) ? 1 : 0 ;
            $role_group->save();
            $role_group->update_lists( $request->lists );
            return $role_group;
        });
        return $role_group;
    }

    public function updates( Request $request ) {
        $role_group = DB::transaction( function() use( $request ) {
            
            if( $request->default ) {
                RoleGroup::where( 'default', true )->where( 'id', '!=', $this->id )->update( ['default' => false] );
            }
            $this->name    = $request->name;
            $this->memo    = $request->memo;
            $this->default = ( $request->default ) ? 1 : 0 ;
            $this->save();
            // dump( $request->input(), $this );
            
            $this->update_lists( $request->lists );
            return $this;
        });
        return $role_group;
    }
    
    public function deletes( ) {

        $role_group = DB::transaction( function() {
            
            if( $this->default ) { throw new Exception( 'デフォルトのロールグループは削除できません'); return false; }

            RoleList::where( 'role_group_id', $this->id )->delete();
            $this->delete();

            return $this;
        });
        return $role_group;
        
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム用　配列取得関数
    //
    //////////////////////////////////////////////////////////////////////////
    
    //　グループ内のロールを配列を返す
    //
    public function get_array_role_lists() {
        $role_lists = $this->lists;
        
        $lists = [];
        foreach( $role_group->role_lists as $list ) {
            array_push( $lists, $list->role );
        } 
        return $lists;
        
    }
    
    //　ロールグループ選択用セレクトフォーム用配列出力
    //
    public static function get_array_for_select() {
        
        $role_groups = RoleGroup::all();
        
        $array = [ '' ];
        foreach( $role_groups as $role_group ) {
            $array[ $role_group->id ] = $role_group->name;
        }
        return $array;        
    }
    
    public static function get_default_id() {
        $role_group = RoleGroup::where( 'default', 1 )->first();

        dump( $role_group );        
        
        return $role_group->id;
        
    }

}