<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\User;


class ComfirmDeleteRequest extends FormRequest
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
        
        $rules = [ 'delete_comfirm' => 'required' ];

        return $rules;
    }
    
    public function messages() {
        $messages = [   'delete_comfirm.required' => '削除確認をしてください',
            
            ];        
        return $messages;
    }
    
}
