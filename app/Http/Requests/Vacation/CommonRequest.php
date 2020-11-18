<?php

namespace App\Http\Requests\Vacation;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class CommonRequest extends FormRequest
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
         $rules = array();
         
         
         switch( Route::currentRouteName() ){
            
            case "vacation.common.vindex":
            case "vacation.common.csv"   : 
                // $rules = [  'find.start_date'   => ['required', 'date' ],
                //             'find.end_date'     => ['required', 'date' ],

                //         ];
                break;
            case "vacation.common.vacation":
                // $rules = [  'find.start_date'   => ['required', 'date' ],
                //             'find.end_date'     => ['required', 'date' ],

                //         ];

            

                break;

        }
        return $rules;
    }
}
