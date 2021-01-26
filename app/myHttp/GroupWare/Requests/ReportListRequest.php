<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReportListRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    protected $route_name;
    
    public function authorize() {
        return true;
    }
    
    // public function validationData() {
    // }
    
    // protected function prepareForValidation() {
    // }

    public function rules() {
        
        $route_name = Route::currentRouteName();
        
        if( $route_name != "groupware.report_list.delete" ) {
            
            //　create, update
            //
            $rules = [ 
                'name'       => ['required'],
                'type'      => [ 'required' ],
                'access_list_id' => [ 'required', 'integer' ],
                'default_permission' => [ 'required' ],
                ];
    
            if( $this->disabled ) {
                $rules['not_use'] = [ 'required'];
                $rules['comfirm_disabled'] = [ 'required' ]; 
    
                if( isset( $this->comfirm_disabled )) {
                    $rules['comfirm_disabled.0'] = [ 'required' ]; 
                    $rules['comfirm_disabled.1'] = [ 'required' ]; 
                }
            }
        } else {
            //
            // delete
            $rules[ 'delete_comfirm.0' ] = 'accepted';
            $rules[ 'delete_comfirm.1' ] = 'accepted';
            $rules[ 'delete_comfirm.2' ] = 'accepted';
        }
            
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => '日報リスト名を入力してください。',
                'type.required'         => '公開種別を入力してください。',
                'access_list_id.required'  => 'アクセスリストを選択してください',
                'default_permission.required'  => '日報変更権限（初期値）を入力してください。',
                
                'not_use.required'  => '無効化すると新規に日報追加できなくなります。無効化するのであれば、「新規日報追加不可」もチェックしてください。',
                'comfirm_disabled.required'  => '日報リスト無効化の確認をしてください',
                'comfirm_disabled.*.required'  => '日報リスト無効化について全て確認をお願いします。',
                'delete_comfirm.*.accepted' => '日報リストを削除する場合は、確認事項に全てチェックしてください。',       
                
                
                
            ];        

        return $messages;
        
        
    }
    
}
