<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CalendarRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    
    public function authorize() {
        return true;
    }
    
    public function rules() {
        // dd( $this->route_name );

        $route_name = Route::currentRouteName();
        $rules = [ 
            'name'       => ['required'],
            'type'      => [ 'required' ],
            'access_list_id' => [ 'required', 'integer' ],
            'default_permission' => [ 'required' ],
            ];


        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => 'カレンダー名を入力してください。',
                'type.required'         => '公開種別を入力してください。',
                'access_list_id.required'  => 'アクセスリストを選択してください',
                'default_permission.required'  => 'スケジュール変更権限（初期値）を入力してください。',
            ];        

        return $messages;
    }
    
}
