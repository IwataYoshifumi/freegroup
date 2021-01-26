<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;


class Schedule2Request extends FormRequest
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
            $this->start = new Carbon( $this->start_date." ". $timezone );
            $this->end   = new Carbon( $this->end_date.  " ".   $timezone   );
        } else {
            $this->start = new Carbon( $this->start_date. " ". $this->start_time." ". $timezone );
            $this->end   = new Carbon( $this->end_date.   " ". $this->end_time." ".   $timezone );
        }
    }

    public function rules() {

        $user     = $this->user();
        $route_name = Route::currentRouteName();
        
        if( $route_name == 'groupware.schedule.update') {
            $schedule = $this->route( 'schedule' ); 
    
            if( $schedule->creator->id == $user->id ) {
                $rules['permission']  = 'required';
            }
        }

        //dd( $this->start, $this->end );
        // dd( $this->route_name );

        $rules['name']        = 'required';
        $rules['start_date']  = 'required|date';
        $rules['end_date']    = 'required|date';
        $rules['calendar_id'] = 'required|integer';
        
        if( empty( $this->all_day ) ) {
            $rules['start_time'] = 'required|regex:/^\d{1,2}:\d{1,2}$/';
            $rules['end_time']   = 'required|regex:/^\d{1,2}:\d{1,2}$/';
        }
        if( $this->start->gt( $this->end )) {
            $rules['start_less_than_end'] = 'required';
        }

        //　有効なカレンダーか確認
        //
        if( $this->calendar_id ) {
            
            $invalid = Calendar::where( 'id', $this->calendar_id )->where( function( $query ) use ( $route_name ) {
                    if( $route_name == "groupware.schedule.create" ) {
                        $query->where( 'not_use', 1 )->orWhere( 'disabled', 1 );
                    } else {
                        $query->orWhere( 'disabled', 1 ); 
                    }
                })->get();
                
            // dd( $num );
            if( count( $invalid ) >= 1 ) {
                $rules['calendar_invalid'] = 'required';
            }
        }

        // dd( $this, $this->all_day, $this->start_date, $rules );
        
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => '件名を入力してください。',
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
                'calendar_id.required' => 'カレンダーを選択してください',
                'calendar_invalid.required' => 'カレンダーが不正です',
            
            ];        




        return $messages;
        
        
    }
    
}
