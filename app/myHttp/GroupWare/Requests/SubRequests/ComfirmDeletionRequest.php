<?php

namespace App\myHttp\GroupWare\Requests\SubRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ComfirmDeletionRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
     
    public const COMFIRM_INPUT_NAME = 'comfirm_deletion';
     
    public function rules() {

        return [ 'comfirm_deletion' => 'accepted' ];
    }
    
    public function messages() {
        $messages = [
                'comfirm_deletion.accepted'         => '削除確認をチェックしてください',
            ];        
        return $messages;
    }
    
    public static function getInputName() {
        return self::COMFIRM_INPUT_NAME;
    }
    
}
