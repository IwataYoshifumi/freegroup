<?php

namespace App\Http\Requests\Vacation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;


class ApprovalMasterRequest extends FormRequest
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

        
        switch( Route::currentRouteName() ){
            
            case  "vacation.approvalMaster.store" :
                $rules = [ 'check_approver' => 'required|integer|gte:1',
                           'name'           => [ 'required', "unique:approval_masters" ],
                         ];
                break;

            case "vacation.approvalMaster.update" :
                $rules = [ 'check_approver' => 'required|integer|gte:1',
                           'name'           => [ 'required', Rule::unique('approval_masters')->ignore( Request::input('id')) ],
                        ];
                break;
            case "vacation.approvalMaster.allocate" : 
            case "vacation.approvalMaster.deallocated" : 
                $rules = [ 'check_users' => 'required|integer|gte:1' ,
                           ];
                break;
            case "vacation.approvalMaster.allocated" : 
                $rules = [ 'master' => 'required|gte:1' ,
                           ];
                break;
        }

        return $rules;
    }
    
    public function messages() {
        
        $return = [ 
                'name.required'             => 'マスター名を入力してください',
                'name.unique'               => 'このマスター名は既に登録済みです。別のマスター名にしてください',
                'check_approver.required'   => '承認者を入力してください',
                'check_approver.gte'        => '承認者を入力してください',

            ];

        if( Route::currentRouteName() == "vacation.approvalMaster.store" ) {
             $return['approval_master_id']   = '割り当てる承認マスターを選択してください';
             $return['check_users.required'] = '承認マスターを割り当てる社員を選択してください(0)';
             $return['check_users.gte']      = '承認マスターを割り当てる社員を選択してください(1)';
        } elseif( Route::currentRouteName() == "vacation.approvalMaster.deallocated" ) {
             $return['check_users.required'] = '承認マスターの割当解除する社員を選択してください(0)';
             $return['check_users.gte']      = '承認マスターを割当解除する社員を選択してください(1)';   
        }                                
        // dd( Route::currentRouteName(), $return );
        return $return;
        
        
    }
}
