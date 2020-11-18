<?php

namespace App\Http\Requests\Vacation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class DeptRequest extends FormRequest
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
    public function rules()
    {
        switch( Route::currentRouteName() ){
            
            case "vacation.dept.store":
                $rules = [  'name' => ['required', 'string', 'max:255', Rule::unique( 'depts', 'name' ), ],

                        ];
                break;
            case "vacation.dept.update":
                $rules = [  'name' => ['required', 'string', 'max:255',Rule::unique('depts')->ignore( Request::input('id') ) ],

                        ];
                break;
                

        }
        return $rules;
    }
    
    
    public function messages() {
        
        return [    'name.required' => '部署名を入力してください',
                    'name.unique'   => 'この部署名は登録済みです',

        ];
        
        
    }
    
}
