<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;


class ReservationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, Reservation $reservation) {
        //
        if( $reservation->canRead( $user )) { return Response::allow(); }        

        return Response::deny( 'ReservationPolicy@view : deny 1 ');
    }
    
    public function create(User $user) {
        
        //　予約可能な設備があるか確認
        //
        // $facilitys = Facility::whereCanWrite( $user )->where( 'not_use', false )->get();
        $facilitys = Facility::whereCanWrite( $user )->get();
        return count( $facilitys ) >= 1;
    }

    public function update(User $user, Reservation $reservation) {

        if( $user->id == $reservation->user_id ) {
            return Response::allow();
        } 
        
        return Response::deny( 'ReservationPolicy@update : deny at all');
    }

    public function delete(User $user, Reservation $reservation) {

        return $this->update( $user, $reservation );
    }

    public function cancel( User $user, Reservation $reservation ) {
        
        return $this->delete( $user, $reservation );
    }

}
