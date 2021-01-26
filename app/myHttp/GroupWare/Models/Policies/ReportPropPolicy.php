<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;


class ReportPropPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
    }
    public function view(User $user, ReportProp $report_prop) {
        if( $report_prop->user_id != $user->id ) {
            return Response::deny( "ReportPropPolicy::view 1 : This action is unauthorized. You are not ReportProp's Owner" );
        }
        return Response::allow();
    }

    //　自動生成のため create アクションはなし
    //
    public function create(User $user) {
        return Response::deny( "ReportPropPolicy::create 1 : This action is unauthorized." );
    }

    public function update(User $user, ReportProp $report_prop) {
        
        if( $report_prop->report_list->is_disabled() ) {
            return Response::deny( "ReportPropPolicy::update 2 : ReportList has been disabled." );
        }
        
        if( $report_prop->user_id != $user->id ) {
            return Response::deny( "ReportPropPolicy::update 1 : This action is unauthorized. You are not ReportProp's Owner" );
        }
        return Response::allow();
    }

    public function delete(User $user, ReportProp $report_prop) {
        die( __METHOD__ );
    }
    public function restore(User $user, ReportProp $report_prop) {
        die( __METHOD__ );
    }
    public function forceDelete(User $user, ReportProp $report_prop) {
        die( __METHOD__ );
    }
}
