<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;


class ReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, Report $report) {

        if( $report->canRead( $user )) { return Response::allow(); }        

        return Response::deny( 'ReportPolicy@view : deny 1 ');
    }

    public function create(User $user) {
        //
        return true;
    }

    public function update(User $user, Report $report) {

        $report_list = $report->report_list;
        
        if( $report_list->is_disabled() ) {
            return Response::deny( 'ReportPolicy@update 2 : The ReportList has been Disabled' );
        }
        
        if( $user->id == $report->user_id ) {
            return Response::allow();
        } 
        if( $report->permission == "creator" and $user->id != $report->user_id ) {
            return Response::deny( 'ReportPolicy@update 1 : you are not creator' );
        }
        if( $report->permission == "attendees" and $report->isAttendee( $user )) {
            return Response::allow();
        }

        if( $report->permission == "writers" ) {
            $access_list = $report->report_list->access_list();
            if( $access_list->canWrite( $user->id ) ) {
                return Response::allow();
            }
            
        }
        return Response::deny( 'ReportPolicy@update : deny at all');
    }

    public function delete(User $user, Report $report) {

        
        // return $user->id == $report->user_id;
        return $this->update( $user, $report );
    }

}
