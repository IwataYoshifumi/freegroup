<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    
    public function authorize() {
        return true;
    }
    
    //　ロックIDのバリデーション用にＩＤを追加
    //
    public function validationData() {

        $request = $this->request->all();
        switch( Route::currentRouteName() ) {
            case "user.edit" :
            case "user.update" :
            case "user.delete" :
                $request =  array_merge( $request, ['id' => $this->user->id] );
                break;
        }
        return $request;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        switch( Route::currentRouteName() ){
            
            case "groupware.user.store":
                $rules = [  'name'      => ['required', 'string', 'max:255'],
                            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users' ],
                            'dept_id'   => ['required', 'integer' ],
                            'password'  => ['required', 'string', 'min:8', 'confirmed'],
                        ];
                
                //　独自パスワードのバリデーション
                //
                if( config( 'config.password.validation' ) !== null ) {
                    $rules['password'] = array_merge( $rules['password'], config( 'config.password.validation' ) );
                }
                break;
                

            case "groupware.user.update":
                $rules = [  'name'      => ['required', 'string', 'max:255'],
                            'dept_id'   => ['required', 'integer' ],
                            'email'     => [ 'required', 'email', 'max:255', Rule::unique('users')->ignore( Request::input('id') ) ],
                        ];
                
                if( ! empty( $this->input['password'] )) {
                    $rules['password'] = ['string', 'min:8', 'confirmed' ];
                }
                if( ! empty( $this->retired )) {
                    $rules['date_of_retired'] = ['required', 'date' ];
                }
                if( ! empty( $this->password )) {
                    $rules['password'] = [ 'required', 'string', 'min:8', 'confirmed' ];
                    $rules['password'] = array_merge( $rules['password'], config( 'config.password.validation' ) );
                }
                
                
                //　ロックIDのバリデーション
                //
                if( config( 'user.locked_ids')) {
                    $rules['id'] = Rule::notIn( config( 'user.locked_ids' ));
                }

                break;
                
            case "groupware.user.edit":
                //　ロックIDのバリデーション
                //
                if( config( 'user.locked_ids')) {
                     $rules['id'] = Rule::notIn( config( 'user.locked_ids' ));
                }

                break;

            case "user.password.update":
                $rules = [  'password'  => ['required', 'string', 'min:8', 'confirmed', config( 'password.valicator.'.config('user.password_valicator')) ],
                            
                        ];
                break;

        }
        // dd( $rules );
        // dd( $this );
        return $rules;
    }
    
    public function messages() {
        $m = config( 'password.error.'.config('user.password_valicator'));
        // dd( $m, 1);
        return [
                'id.not_in' => 'このIDはロックされています',
                'password.confirmed' => 'パスワードが不一致です',
                'dept_id.required' => '部署を選択してください',
                array_key_first( $m ) => $m[array_key_first( $m )],  
            
            
            ];
    }
    
}
