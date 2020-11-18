<?php

namespace App\Http\Requests\Vacation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use App\Models\Vacation\Vacation;

class VacationRequest extends FormRequest
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
        $rules = [ 'allocate_date'    => ['required', 'date' ],
                   'expire_date'      => ['required', 'date', 'after_or_equal:'.$this->allocate_date ],
                   'num'              => ['required', 'integer' ],
                   'year'             => ['required', 'integer', 'gt:2000' ],

                    ];


        //
        // year と user_id の組み合わせはユニーク
        // 従業員への有給割当は年１回まで
        //
        // dd( $this );
        if( Route::currentRouteName() == "vacation.allocate.store" ) {
            $rules['users.*'] = Rule::unique( 'vacations', 'user_id' )->where( 'year', $this->input('year') )->where( 'action', '割当' );
            array_push( $rules['num'], 'gte:1' ); 
        }
        if( Route::currentRouteName() == "vacation.vacation.update" ) {
            $rules['user_id'] = Rule::unique( 'vacations', 'user_id' )->ignore( $this->id )->where( 'year', $this->input('year') )->where( 'action', '割当');
            $p = Vacation::where( 'id', $this->id )->get()->first();
            // dd( $p );
            $digested_num = $p->application_num + $p->approval_num + $p->completed_num;
            
            array_push( $rules['num'], 'gte:'.$digested_num ); 
            
        }
        // dd( Route::currentRouteName(), $rules );

        return( $rules );
    }
    
    public function messages() {
        
        $messages = [
                        'expire_date.after_or_equal'    => '有効期限は割当日より後の日付にしてください。',
                      'paid_leave.gte'                 => '有給日数は１以上の数を入力してください。',
                      'users.*'                => '既に同年度で有給休暇を割当済みの社員に有給を割当ようとしています',
                      'user_id.unique'                => '既に同年度で有給休暇を割当済みの社員に有給を割当ようとしています',
                        ];
                        
        if( Route::currentRouteName() == "paidleave.update" ) {
            $messages['paid_leave.gte'] = "休暇申請日数より割当休暇日数を多くしてください。";
        }
        
        return $messages;

    }
}
