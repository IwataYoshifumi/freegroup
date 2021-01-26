<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Calendar;


class CalendarPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
        return true;
    }

    public function view(User $user, Calendar $calendar) {
        if( $calendar->canRead( $user->id ) ) {
            return Response::allow();
        }
        return Response::deny( "CalendarPolicy::view 1 : This action is unauthorized. You can not read the calendar" );
    }

    public function create(User $user) {
        if( $user->is_retired() ) { return Response::deny( "CalendarPolicy::create 1 : You are retired." ); }
        return true;
    }

    public function update(User $user, Calendar $calendar) {
        //　オーナーでなければ変更できない
        //
        // if( ! $calendar->access_list()->isOwner( $user->id )) {
        if( ! $calendar->isOwner( $user->id )) {
            return Response::deny( "CalendarPolicy::update 1 : This action is unauthorized. You are not Calendar's Owner" );
        }
        return Response::allow();
    }

    public function delete(User $user, Calendar $calendar) {
        
        //　無効状態でないと削除できない
        //
        if( $calendar->isNotDisabled() ) { return  Response::deny( "CalendarPolicy::delete 2 : The Calendar is available. You Can not delete it." );  }
        
        //　オーナーでなければ削除できない
        //
        if( ! $calendar->isOwner( $user->id )) {
            return Response::deny( "CalendarPolicy::delete 1 : This action is unauthorized. You are not Calendar's Owner" );
        }
        return Response::allow();
    }

}
