<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use App\myHttp\GroupWare\Models\File as MyFile;


class TestRequest extends FormRequest
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
            $rules = [ 
                     'id' => 'required', 
                    
                ];

        return $rules;
    }
    
    public function messages() {
        $messages = [
            
            
            
            ];        
        return $messages;
    }
    
}
