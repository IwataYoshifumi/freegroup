<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ScheduleRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    protected $route_name;
    
    public function __construct() {
        $this->route_name = Route::currentRouteName();
    }
    
    public function authorize() {
        return true;
    }
    
    // public function validationData() {
    // }
    
    protected function prepareForValidation() {
        //  dump( $this->all() );
        // dd( $this );
        

        if( $this->period == "時間") {
            if( is_null( $this->end_time )) {
                // dump( 'aaa', $this->start_time );
                $this->merge( ['end_time' => $this->start_time ]);
                // $this->end_time = $this->start_time;
            }
            
        } elseif( $this->period == "終日" ) {
            $start_time = new Carbon( $this->start_time );
            $end_time   = new Carbon( $this->end_time );
            if( $start_time->format( 'Ymd' ) != $end_time->format( 'Ymd' ) ) { $this->period = "複数日";  } 
        
            // dd( $this->start_time, $this->end_time );
            
        } elseif( $this->period == "複数日" ) {
            $start_time = new Carbon( $this->start_time );
            $end_time   = new Carbon( $this->end_time );
            if( $start_time->format( 'Ymd' ) == $end_time->format( 'Ymd' ) ) { $this->period = "時間";  } 
            
        }
        // dd( $this->all() );
    }

    public function rules() {
        // dd( $this->route_name );

        $rules = [ 
            'name'       => ['required'], 
            'start_time' => ['required', ],
            // 'end_time'   => ['required_if:period,時間', ],
            'end_time'   => ['required', 'after_or_equal:start_time'],
            'period'     => ['required'],
            ];

        // if( preg_match( '/schedule\.store/', $this->route_name ) ) {
        // }
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => '件名を入力してください。',
                'start_time.required'   => '日時を入力してください',
                'end_time.required_if'  => '終了日時を入力してください',
                'end_time.required'     => '終了日時を入力してください',
                'end_time.after_or_equal' => '終了日時は開始日時より後に設定してください',
                
            
            ];        




        return $messages;
        
        
    }
    
}
