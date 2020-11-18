<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class CalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        $rules = [ 'inputs.start'   => 'required',
                   'inputs.end'     => 'required',
                   'inputs.summary' => 'required' 
                 ];

        return $rules;
    }
    
    
    public function messages() {
        
        return [    'start.required' => '日時を入力してください。',
                    'end_time.required'   => '日時を入力してください。',
                    'subject.required'    => '件名を入力してください',

        ];
        
        
    }
    
}
