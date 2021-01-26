<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReportRequest extends FormRequest
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
    
    // protected function prepareForValidation() {
    // }

    public function rules() {
        // dd( $this->route_name );

        $rules = [ 
            'user_id'    => ['required'],
            'name'       => ['required'], 
            'start_time' => ['required', ],
            'end_time'   => ['required', 'after_or_equal:start_time'],
            ];

        // if( preg_match( '/report\.store/', $this->route_name ) ) {
        // }
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => '件名を入力してください。',
                'start_time.required'   => '日時を入力してください',
                'end_time.required_if'  => '終了日時を入力してください',
                'end_time.after_or_equal' => '終了日時は開始日時より後に設定してください',
                
            
            ];        




        return $messages;
        
        
    }
    
}
