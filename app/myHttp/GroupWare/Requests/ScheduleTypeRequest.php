<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ScheduleTypeRequest extends FormRequest
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
            'input.name'       => ['required'], 
            ];


        return $rules;
    }
    
    public function messages() {
        $messages = [
                'input.name.required'         => 'スケジュール種別名を入力してください。',
                'name.required'         => 'スケジュール種別名を入力してください。',
            ];        

        return $messages;
    }
    
}
