<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\User;


class GroupRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    // public function validationData() {
    // }
    
    // protected function prepareForValidation() {
    // }

    public function rules() {
        $route_name = Route::currentRouteName();
        
        switch( $route_name ) {
            case "groupware.group.create" :
            case "groupware.group.update" : 
                $rules = [ 
                    'name' => [ 'required' ],
                    'users'=> [ 'required', 'array', ],
                    'access_list_id' => ['required'],
                    ];
                break;
            case "groupware.group.delete" : 
                $rules = [ 'delete_comfirm' => 'required' ];
                break;
        }
        return $rules;
    }
    
    public function messages() {
        $messages = [   'name.required'  => 'グループ名を入力してください。',
                        'users.required' => '所属ユーザを入力してください(1)。',
                        'users.array'    => '所属ユーザを入力してください(2)。',
                        'access_list_id.required' => 'アクセスリストを設定してください。',
                        'delete_comfirm.required' => '削除確認をしてください',
            ];        
        return $messages;
    }
    
}
