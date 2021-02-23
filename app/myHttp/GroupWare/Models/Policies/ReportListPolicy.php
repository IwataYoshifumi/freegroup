<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\ReportList;


class ReportListPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
        return true;
    }

    public function view(User $user, ReportList $report_list) {
        if( $report_list->canRead( $user->id ) ) {
            return Response::allow();
        }
        return Response::deny( "ReportListPolicy::view 1 : This action is unauthorized. You can not read the report_list" );
    }

    public function create(User $user) {
        if(   $user->is_retired()               ) { return Response::deny( "ReportListPolicy::create 1 : You are retired." ); }
        if( ! $user->hasAccessListsWhoIsOwner() ) { return Response::deny( "ReportListPolicy::create : You do not have AccessLists that you own" ); }
        return true;
    }

    public function update(User $user, ReportList $report_list) {
        //　オーナーでなければ変更できない
        //
        if( $report_list->isOwner( $user->id )) { return Response::allow(); }
        
        return Response::deny( "ReportListPolicy::update 1 : This action is unauthorized. You are not ReportList's Owner" );
    }

    public function delete(User $user, ReportList $report_list ) {
        
        //　無効状態でないと削除できない
        //
        if( $report_list->isNotDisabled() ) { return  Response::deny( "ReportListPolicy::delete 2 : The ReportList is available. You Can not delete it." );  }
        
        //　オーナーでなければ削除できない
        //
        if( ! $report_list->isOwner( $user->id )) {
            return Response::deny( "ReportListPolicy::delete 1 : This action is unauthorized. You are not ReportList's Owner" );
        }
        return Response::allow();
    }

}
