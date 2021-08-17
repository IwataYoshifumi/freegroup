<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Reservation;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Actions\FileAction;

class ReservationAction  {
    
    // Reservationの新規作成
    //
    // 複数の設備に対する予約を同時に行う
    //
    public static function creates( Request $request ) {

        $reservations = DB::transaction( function() use ( $request ) {
    
            $reservations = [];

            foreach( $request->facilities as $facility_id ) {            

                $reservation = new Reservation;

                $reservation->user_id  = user_id();
                $reservation->purpose  = $request->purpose;
                $reservation->memo  = $request->memo;
                $reservation->start = $request->start;
                $reservation->end   = $request->end;
                $reservation->all_day    = (   $request->all_day ) ? 1 : 0;
                $reservation->facility_id = $facility_id;
                $reservation->save();

                $reservations[$reservation->id] = $reservation;
            }
            
            // 関連顧客・社員情報の同期        
            //
            // $reservation->customers()->sync( $request->customers );
            // $reservation->users()->sync( $request->users );
            
            return $reservations;

        });    
        return $reservations;
    }
    
    // Reservationの修正（目的、備考のみ修正可能）
    //
    public static function updates( Reservation $reservation, Request $request ) {

        $reservation = DB::transaction( function() use ( $reservation, $request ) {

            $user = auth( 'user' )->user();

            $reservation->purpose  = $request->purpose;
            $reservation->memo     = $request->memo;
            $reservation->save();
            return $reservation;
        });    
        return $reservation;
    }
    
    // Reservationの削除
    //
    public static function deletes( $reservation ) { 

        $return = DB::transaction( function() use ( $reservation ) {
            
            // $reservation->users()->detach();
            $reservation->schedules()->detach();
            $reservation->delete();

        });

        return $return;
    }
    
}

