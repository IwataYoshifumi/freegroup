<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;


class RoleGroupAction extends RoleGroup {
    
    //　ロールを検索
    //
    public static function search( $find ) {
        
        // dump( $find );
        
        $exist_where = false;
        $query = new RoleGroup;
        if( optional( $find )['name'] ) {
            $query = $query->where( 'name', 'like', '%'. $find['name'] .'%' );
            $exist_where = true;
        } 
        if( is_array( optional( $find )['list'] )) {
            $exist_where = true;
            $query = $query->whereHas( 'lists', function( $q ) use( $find ) {
                    $q->whereIn( 'role', $find['list'] );
                });
        }

        if( $exist_where ) {
            $role_groups = $query->get();
        } else {
            $role_groups = $query->all();
        }
        // dump( $exist_where, $role_groups, $query );

        return $role_groups;
        
    
    }

    //　ロール割当画面でユーザを検索
    //
    public static function search_users( $find ) {
        
        $exist_where = false;
        $query = new User;
        if( optional( $find )['name'] ) {
            $query = $query->where( 'name', 'like', '%'. $find['name'] .'%' );
            $exist_where = true;
        }
        if( optional( $find )['dept_id'] ) {
            $query = $query->where( 'dept_id', $find['dept_id'] );
            $exist_where = true;
        }
        if( optional( $find )['role_group_id'] ) {
            if( $find['role_group_id'] >= 1 ) {
                $query = $query->whereHas( 'role_groups', function( $q ) use( $find ) {
                    $q->where( 'id', $find['role_group_id'] );  
                });
            } else {
                $query = $query->doesntHave( 'role_groups' );
            }
            $exist_where = true;
        }
        
        
        $users = $query->with( ['dept' ] )->paginate( $find['pagination'] );
        
        return $users;
    }
    
    //　ユーザにロールを割り当てる
    //
    public static function attach_users( Request $request ) {
        
        $role_group = DB::transaction( function() use( $request ) {
            $users = User::find( $request->users );
            foreach( $users as $user ) {
                $user->role_groups()->detach();
            }
    
            $role_group = RoleGroup::find( $request->role_group );
            $role_group->users()->attach( $request->users );
            
            return $role_group;
        });
        return $role_group;
    }
    

}