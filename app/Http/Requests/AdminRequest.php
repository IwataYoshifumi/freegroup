<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    public function validationData() {
        // dd( $this->admin->id );
        //　ロックIDのバリデーション用
        //
        $request = $this->request->all();
        switch( Route::currentRouteName() ) {
            case "admin.edit" :
            case "admin.update" :
            case "admin.delete" :
                $request =  array_merge( $request, ['id' => $this->admin->id] );
                // dd( $return );
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
            
            case "admin.store":
                $rules = [  'name'      => ['required', 'string', 'max:255'],
                            'email'     => ['required', 'string', 'email', 'max:255', 'unique:admins' ],
                            'password'  => ['required', 'string', 'min:8', 'confirmed'],
                        ];
                
                //　独自パスワードのバリデーション
                //
                if( config( 'config.password.validation' ) !== null ) {
                    $rules['password'] = array_merge( $rules['password'], config( 'config.password.validation' ) );
                }
                break;
                

            case "admin.update":
                $rules = [  'name'      => ['required', 'string', 'max:255'],
                            'email'     => [ 'required', 'email', 'max:255', Rule::unique('admins')->ignore( Request::input('id') ) ],
                        ];
                
                if( ! empty( $this->input['password'] )) {
                    $rules['password'] = ['string', 'min:8', 'confirmed' ];
                }
                if( ! empty( $this->retired )) {
                    $rules['date_of_retired'] = ['required', 'date' ];
                }
                //　ロックIDのバリデーション
                //
                if( config( 'admin.locked_ids')) {
                    $rules['id'] = Rule::notIn( config( 'admin.locked_ids' ));
                }

                break;
                
            case "admin.edit":
                //　ロックIDのバリデーション
                //
                if( config( 'admin.locked_ids')) {
                     $rules['id'] = Rule::notIn( config( 'admin.locked_ids' ));
                }

                break;

            case "admin.password.update":
                $rules = [  'password'  => ['required', 'string', 'min:8', 'confirmed', config( 'password.valicator.'.config('admin.password_valicator')) ],
                            
                        ];
                break;

        }
        // dd( $this );
        // dd(config( 'password.error.'.config('admin.password_valicator')) );
        return $rules;
    }
    
    public function messages() {
        
        $m = config( 'password.error.'.config('admin.password_valicator'));
        // dd( $m, 1);
        return [
                'id.not_in' => 'このIDはロックされています',
                'password.confirmed' => 'パスワードが不一致です',
                array_key_first( $m ) => $m[array_key_first( $m )],          
            
            
            
            ];
        
    }
    
}
