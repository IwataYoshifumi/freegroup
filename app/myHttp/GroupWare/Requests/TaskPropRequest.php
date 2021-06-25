<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\TaskProp;

class TaskPropRequest extends FormRequest
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

        $taskprop  = $this->route( 'taskprop' );
        $calendar = $taskprop->calendar;
        $user     = $taskprop->user;

        $route_name = Route::currentRouteName();
        $rules = [ 
            'name'                  => ['required'],
            'background_color'      => ['required'],
            'text_color'            => ['required'],
            ];

        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => 'タスク表示名を入力してください。',
                'background_color.required' => '背景色を選択してください。',
                'text_color.required'      => '文字色を選択してください。',
            ];        

        return $messages;
    }
    
}
