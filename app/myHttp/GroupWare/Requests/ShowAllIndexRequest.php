<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class ShowAllIndexRequest extends FormRequest
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

        $rules = [];
        if( empty( $this->keyword ) and empty( $this->start_date ) and empty( $this->end_date )) {
            $rules['keyword_or_date'] = 'required';
        }
        if( empty( $this->calendars ) and empty( $this->tasklists ) and empty( $this->report_lists )) {
            $rules['calendars_or_tasklists'] = 'required';
        }
        
        return $rules;
    }
    
    public function messages() {
        $messages = [   'keyword_or_date.required' => 'キーワード又は検索期間のどちらかの入力は必須です。',
                        'calendars_or_tasklists.required' => '検索対象のカレンダー又はタスクリスト、日報リストを選択してください',
            
            ];        
        return $messages;
    }
    
}
