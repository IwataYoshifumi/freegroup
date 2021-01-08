<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;


class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, Schedule $schedule) {
        //

        if( $schedule->canRead( $user )) { return Response::allow(); }        
        // if( $user->id == $schedule->user_id or
        //     $schedule->isAttendee( $user ) or 
        //     $schedule->calendar->canRead( $user->id ) ) {
        //     return Response::allow();
        // }
        return Response::deny( 'SchedulePolicy@view : deny 1 ');
    }

    public function create(User $user) {
        //
        return true;
    }

    public function update(User $user, Schedule $schedule) {

        // dump( $schedule->isAttendee( $user ));
        
        if( $user->id == $schedule->user_id ) {
            return Response::allow();
        } 
        if( $schedule->permission == "creator" and $user->id != $schedule->user_id ) {
            return Response::deny( 'SchedulePolicy@update 1 : you are not creator' );
        }
        if( $schedule->permission == "attendees" and $schedule->isAttendee( $user )) {
            return Response::allow();
        }

        if( $schedule->permission == "writers" ) {
            $access_list = $schedule->calendar->access_list();
            if( $access_list->canWrite( $user->id ) ) {
                return Response::allow();
            }
            
        }
        return Response::deny( 'SchedulePolicy@update : deny at all');
    }

    public function delete(User $user, Schedule $schedule) {

        // return $user->id == $schedule->user_id;
        return $this->update( $user, $schedule );
    }

    public function restore(User $user, Schedule $schedule) {
        return false;
    }

    public function forceDelete(User $user, Schedule $schedule) {
        return false;
    }
}
