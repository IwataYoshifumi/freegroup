<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use App\myHttp\GroupWare\Models\File as MyFile;


class FileRequest extends FormRequest
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
        if( $route_name == 'groupware.file.delete' or $route_name == 'groupware.file.deleted' ) {
            $rules = [ 'files' => [ 'required', 'array' ],
            
                    ];
        }
        return $rules;
    }
    
    public function messages() {
        $messages = [   'files.required' => '削除ファイルが選択されていません',
                        'files.array'    => '削除ファイルを選択してください',
            
            ];        
        return $messages;
    }
    
}
