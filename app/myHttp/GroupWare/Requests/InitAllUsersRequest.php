<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use Arr;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\ACL;


class InitAllUsersRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    public function rules() {
        
        for( $i = 0; $i <= 4; $i++ ) {
            $rules["confirms.$i.init"] = 'required';
        }
        
        //　初期ロールグループが設定されているかチェック
        //
        if( RoleGroup::where( 'default', 1 )->count() !== 1 ) {
            $rules['default_role_group'] = 'required';
        }
        // dd( $rules );
        return $rules;

    }
    
    public function messages() {

        $messages = [   'confirms.*.init.required' => '各種確認の上、チェックをしてください',
                        'default_role_group.required' => '初期値ロールグループの設定がありません。ロール設定画面からロールの設定をしてください。',
            ];        
        return $messages;
    }
    
}
