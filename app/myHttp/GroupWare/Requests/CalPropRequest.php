<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\CalProp;

class CalPropRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    
    public function authorize() {
        return true;
    }
    
    // protected function prepareForValidation() {

    // if( $this->has( 'google_sync_bidirectional' ))
    //     $this->merge([
    //         'google_sync_bidirectional' => intval($this->google_sync_bidirectional)
    //     ]);
    // }
    
    
    public function rules() {

        $calprop  = $this->route( 'calprop' );
        $calendar = $calprop->calendar;
        $user     = $calprop->user;

        $route_name = Route::currentRouteName();
        $rules = [ 
            'name'                  => ['required'],
            'background_color'      => ['required'],
            'text_color'            => ['required'],
            'google_id'             => ['required_with:google_calendar_id'],
            ];
            
        if( $this->google_calendar_id ) {
           $rules['google_id'] = [ 'required' ];
        }
        if( $this->google_id ) {

            if( $route_name == "groupware.calprop.create" ) {
                $unique = 'unique:App\myHttp\GroupWare\Models\CalProp,google_calendar_id';
            } else {
                $unique = Rule::unique( CalProp::class )->ignore( $this->calprop_id );
            }
            $rules['google_calendar_id'] = [ 'required', $unique ];
        }

        //　双方向同期はカレンダーに対してWriteできなければならない
        //
        if( $this->google_sync_bidirectional ) {
            if( $calendar->canNotWrite( $user )) {
                $rules['google_sync_bidirectional'] = [ 'max:0' ];                
            }
        }


        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => 'カレンダー表示名を入力してください。',
                'background_color.required' => '背景色を選択してください。',
                'text_color.required'      => '文字色を選択してください。',
                'google_id.required'        => 'Google サービスアカウントＩＤを入力してください',
                'google_calendar_id.required'        => 'Google カレンダーＩＤを入力してください',
                'google_calendar_id.unique'        => '他のカレンダーで同じGoogleカレンダーＩＤを指定しています。',
                'google_sync_bidirectional.max'  => 'カレンダーへの書き込みが出来ないため、双方向同期はできません',            
            ];        

        return $messages;
    }
    
}
