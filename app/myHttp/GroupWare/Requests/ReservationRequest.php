<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Reservation;


class ReservationRequest extends FormRequest
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

        $user     = $this->user();
        $route_name = Route::currentRouteName();
        
        if( $route_name == 'groupware.reservation.update') {
            $rules['purpose']        = 'required';
        }
        
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'purpose.required'         => '設備の利用目的を入力してください。',
            ];        

        return $messages;
    }
    
}
