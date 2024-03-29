<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Reservation;


class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public $start; // dateTime
    public $end;   // dateTime 
    
    public function authorize() {
        return true;
    }
    
    // public function validationData() {
    // }
    
    protected function prepareForValidation() {

        $timezone = config( 'app.timezone' );
        $pattern = '/^(\d{1,2])[\.\-:,](\d{1,2})/';
        $replace = '\1:\2';
        if( $this->start_time ) {
            $this->start_time = preg_replace( $pattern, $replace, $this->start_time );
        } else {
            $this->start_time = "00:00";
        }
        
        if( $this->end_time ) {
            $this->end_time   = preg_replace( $pattern, $replace, $this->end_time   );
        } else {
            $this->end_time = "00:00";
        }

        if( $this->all_day ) {
            $this->start = new Carbon( $this->start_date." 00:00:00". $timezone );
            $this->end   = new Carbon( $this->end_date.  " 23:59:59". $timezone   );
        } else {
            $this->start = new Carbon( $this->start_date. " ". $this->start_time." ". $timezone );
            $this->end   = new Carbon( $this->end_date.   " ". $this->end_time." ".   $timezone );
        }
    }

    public function rules() {

        $user     = $this->user();
        $route_name = Route::currentRouteName();
        
        if( $route_name == 'groupware.reservation.update') {
            $reservation = $this->route( 'reservation' ); 
        }

        //dd( $this->start, $this->end );
        // dd( $this->route_name );

        $rules['purpose']        = 'required';
        $rules['start_date']  = 'required|date';
        $rules['end_date']    = 'required|date';
        $rules['facilities'] = 'required|array';
        
        if( empty( $this->all_day ) ) {
            $rules['start_time'] = 'required|regex:/^\d{1,2}:\d{1,2}$/';
            $rules['end_time']   = 'required|regex:/^\d{1,2}:\d{1,2}$/';
        }
        if( $this->start->gt( $this->end )) {
            $rules['start_less_than_end'] = 'required';
        }

        //　設備予約可能な設備か確認
        //
        if( is_array( $this->facilities ) and count( $this->facilities )) {
            $faciliies = Facility::whereIn( 'id', $this->facilities )->where( 'disabled', false )->get();

            if( count( $this->facilities ) != count( $faciliies )) { die( 'StoreReservationRequest : illegal operaton 1'); }
            
            foreach( $faciliies as $facility ) {
                if( ! $facility->canWrite( $user )) { die( 'StoreReservationRequest : illegal operaton 2'); }
            }
        }
        // 設備に空き時間があるか確認
        //
        $request = clone $this;
        foreach( $faciliies as $facility ) {
            $reservations = Reservation::where( function( $sub_query ) use ( $request ) {
            $sub_query->where( function( $query ) use ( $request ) {
                        $query->where( 'start', '>', $request->start->format( 'Y-m-d H:i' ) )
                              ->where( 'start', '<',  $request->end->format( 'Y-m-d H:i' )   );
                        });
            $sub_query->orWhere( function( $query ) use( $request ) {
                        $query->where( 'end', '>',  $request->start->format( 'Y-m-d H:i' ) )
                              ->where( 'end', '<', $request->end->format( 'Y-m-d H:i' )   );
                        });
            $sub_query->orWhere( function( $query ) use( $request ) {
                        $query->where( 'start', '<=', $request->start->format( 'Y-m-d H:i' ) )
                              ->where( 'end',   '>=', $request->end->format( 'Y-m-d H:i' )  );
                        });
            });
            $reservations = $reservations->where( 'facility_id', $facility->id );
            $reservations = $reservations->get();
            
            if_debug( 'search reservation', $request, $reservations, count( $reservations ), $request->start->format( 'Y-m-d H:i') );
            if( count( $reservations )) {
                $rules["facilities." . $facility->id . ".time_invalid"] = "required";
            }
            
            
        }
        

        if_debug( $this, $this->all_day, $this->start_date, $rules );
        
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'purpose.required'         => '設備の利用目的を入力してください。',
                'start_date.required'   => '開始日を入力してください',
                'start_date.date'       => '開始日が不正です',
                'start_time.required'   => '開始日時を入力してください',
                'start_time.regex'      => '開始時刻が不正です。',

                'end_date.required'     => '終了日を入力してください',
                'end_date.date'         => '終了日が不正です',
                'end_time.required'     => '終了日時を入力してください',
                'end_time.regex'        => '終了時刻が不正です。',

                'end_time.required_if'  => '終了日時を入力してください',
                'end_time.required'     => '終了日時を入力してください',

                'start_less_than_end.required' => '開始日時より終了日時の方が前になっています。',                
                'facilities.required' => '予約設備が入力されていません。',
                'facilities.array'     => '予約設備の入力が不正です',
            
            ];        

        $faciliies = Facility::whereIn( 'id', $this->facilities )->get();

        foreach( $faciliies as $facility ) {
            $messages['facilities.' . $facility->id . '.time_invalid.required'] = '設備「' . $facility->name . '」はこの時間帯に予約が入っています。';
        }

        return $messages;
        
        
    }
    
}
