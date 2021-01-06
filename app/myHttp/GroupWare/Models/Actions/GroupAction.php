<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class GroupAction {
    
    // protected $table = 'groups';
    
    //　検索
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

    public static function creates( Request $request ) {

        $group = DB::transaction( function() use ( $request ) {
                // dump( $request->input() );
                $group = new Group;
                $group->name = $request->name;
                $group->memo = $request->memo;
                $group->save();
                
                $group->users()->sync( $request->users );
                $group->access_lists()->sync( [$request->access_list_id] );
                return $group;
            });
        
        return $group;
    }
    
    public static function updates( Group $group, Request $request ) {

        $group = DB::transaction( function() use ( $group, $request ) {
            
            $group->name = $request->name;
            $group->memo = $request->memo;
            $group->save();
            
            $group->users()->sync( $request->users );
            $group->access_lists()->sync( [$request->access_list_id] );
            
            // このグループを使用しているAccessListのAccessListUserRole DBを更新
            AccessListUserRoleUpdate::Group( $group );
            
            return $group;
        });
        
        return $group;
    }
    
    //　アクセスリストでグループを使用していたら削除不可
    //
    public static function deletes( Group $group ) {

        $group = DB::transaction( function() use ( $group ) {
                $group->users()->detach();
                return $group->delete();
            });
        
        return $group;
    }
    

}

