<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FacilityRequest extends FormRequest
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
        // dd( $this->route_name );

        $route_name = Route::currentRouteName();
        
        if( $route_name != "groupware.facility.delete" ) {
            
            //　create, update
            //
            $rules = [ 
                'name'      => ['required'],
                'category'  => [ 'required' ],
                'type'      => [ 'required' ],
                'access_list_id' => [ 'required', 'integer' ],
                ];
    
            if( $this->disabled ) {
                $rules['comfirm_disabled'] = [ 'required' ]; 
    
                if( isset( $this->comfirm_disabled )) {
                    $rules['comfirm_disabled.0'] = [ 'required' ]; 
                }
            }
        } else {
            //
            // delete
            //
            $rules[ 'delete_comfirm.0' ] = 'accepted';
            $rules[ 'delete_comfirm.1' ] = 'accepted';
            // $rules[ 'delete_comfirm.2' ] = 'accepted';
        }

        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => '設備名を入力してください。',
                'category.required'     => '大分類を入力してください',
                'type.required'         => '公開種別を入力してください。',
                'access_list_id.required'  => 'アクセスリストを選択してください',
                'default_permission.required'  => 'タスク変更権限（初期値）を入力してください。',
                'not_use.required'  => '無効化すると新規に予定追加もできなくなります。無効化するのであれば、「新規予定追加不可」もチェックしてください。',
                'comfirm_disabled.required'  => '設備無効化の確認をしてください',
                'comfirm_disabled.*.required'  => '設備無効化について全て確認をお願いします。',
                'delete_comfirm.*.accepted' => '設備を削除する場合は、確認事項に全てチェックしてください。',                
            ];        

        return $messages;
    }
    
}
