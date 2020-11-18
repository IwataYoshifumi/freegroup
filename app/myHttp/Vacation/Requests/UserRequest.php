<?php

namespace App\Http\Requests\Vacation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use App\Models\Vacation\User;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules( ) {

        switch( Route::currentRouteName() ){
            
            case "vacation.user.store":
                $rules = [  'name'      => ['required', 'string',  'max:255'],
                            'email'     => ['sometimes','nullable','max:255', 'unique:users', 'email' ],
                            'code'      => ['required', 'string'  ,'max:255', 'unique:users' ],
                            'password'  => ['required', 'string', 'min:8', 'confirmed'],
                            'join_date' => ['required', 'date' ],
                            'carrier'   => ['required'],
                            'dept_id'   => ['required'],
                        ];
                break;
            case "vacation.user.update":
                $rules = [  'name'      => ['required', 'string', 'max:255'],
                            'code'      => ['required', 'string',   'max:255', Rule::unique('users')->ignore( Request::input('id') ),],
                            'join_date' => ['required', 'date' ],
                            'carrier'   => ['required'],
                            'dept_id'   => ['required'],
                        'date_of_retired'  => [ Rule::requiredIf( ! empty( Request::input('retired'))) ],
                        ];
                if( ! empty( $this->email )) {
                	$rules['email'] = ['string', 'email', 'max:255', Rule::unique('users')->ignore( Request::input('id') ) ];
                }
                
                break;
            case "vacation.user.password.update":
                $rules = [  'password'  => ['required', 'string', 'min:8', 'confirmed' ],
                            
                        ];
                break;

        }
        //
        //  管理者が最低１名残るようにバリデーション
        //
        // if( Route::currentRouteName() == 'user.update' or  Route::currentRouteName() == 'user.droped' ) {
        //     if( ! $this->admin or $this->required ) {
        //         $admin_num = User::where( 'admin', true )->whereNotIn( 'id', [ $this->id ] )->get()->count();                        
        //         // dd( $admin->count() );
        //         if( $admin_num <= 0 ) {
        //             $rules['admin'] = 'same:1';
        //         }
                
        //     }
        // }
        
        return $rules;
    }

    public function messages() {
        
        return [    'email.unique' =>'このメールアドレスは他ユーザで既に登録済みです。',
                    'code.unique'  => 'この社員番号は登録済みです',
                    'date_of_retired.required' => '退職日を入力してください',
                    'admin.same'    => '管理者は最低1名必要です。他の管理者を管理者に設定してから、管理者権限を外してください。'     ,
                    'join_date.required' => '入社日年月日を入力してください',
                    'password.min' => 'パスワードは8文字以上です',
        ];
        
        
    }
    
    
    
    
}
