<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Facility;


class FacilityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
        return true;
    }

    public function view(User $user, Facility $facility) {
        if( $facility->canRead( $user->id ) ) {
            return Response::allow();
        }
        return Response::deny( "FacilityPolicy::view 1 : This action is unauthorized. You can not read the facility" );
    }

    public function create(User $user) {
        if(   $user->is_retired()               ) { return Response::deny( "FacilityPolicy::create 1 : You are retired." ); }
        if( ! $user->hasAccessListsWhoIsOwner() ) { return Response::deny( "FacilityPolicy::create : You do not have AccessLists that you own" ); }
        return true;
    }

    public function update(User $user, Facility $facility) {
        
        //　オーナーでなければ変更できない
        //
        if( $facility->isOwner( $user->id )) { return Response::allow(); }
        
        return Response::deny( "FacilityPolicy::update 1 : This action is unauthorized. You are not Facility's Owner" );
    }

    public function delete(User $user, Facility $facility ) {
        
        //　無効状態でないと削除できない
        //
        if( $facility->isNotDisabled() ) { return  Response::deny( "FacilityPolicy::delete 2 : The Facility is available. You Can not delete it." );  }
        
        //　オーナーでなければ削除できない
        //
        if( ! $facility->isOwner( $user->id )) {
            return Response::deny( "FacilityPolicy::delete 1 : This action is unauthorized. You are not Facility's Owner" );
        }
        return Response::allow();
    }
    
    //　予約可能な設備かチェック
    //
    public function reserve( User $user, Facility $facility ) {
        
        // 無効設備でないこと
        //
        if( $facility->isDisabled() ) { return Response::deny( "FacilityPolicy::reserve : The Facility is disabled." ); }
        
        //　アクセスリストの編集権限があること
        //
        if( $facility->canWrite( $user )) { 
            return Response::allow(); 
        } else { 
            return Response::deny( "FacilityPolicy::reserve : You don't have right to reserve it.");
        }
    }

}
