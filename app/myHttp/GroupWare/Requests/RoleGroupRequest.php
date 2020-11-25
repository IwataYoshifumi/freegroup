<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class RoleGroupRequest extends FormRequest
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
        // dd( $this );
        $route_name = Route::currentRouteName();
        
        switch( $route_name ) {
            case "groupware.role_group.create" :
            case "groupware.role_group.update" : 
                $rules = [ 'name' => 'required' ];
                break;
            case "groupware.role_group.delete" : 
                $rules = [ 'comfirm' => 'required' ];
                break;
            case "groupware.role_group.attach_role" : 
                $rules = [ 'role_group' => 'required' ];
                break;
                
        }
        
        return $rules;
    }
    
    public function messages() {
        $messages = [ 
            'role_group.required' => '割当ロールグループを選択してください。',
            'name.required' => 'ロールグループ名は必須です。',
            'comfirm.required' => '削除確認をチェックしてください',
            ];        
        return $messages;
    }
    
}
