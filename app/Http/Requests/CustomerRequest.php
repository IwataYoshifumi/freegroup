<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    
    public function authorize() {
        return true;
    }
    
    //　ロックIDのバリデーション用にＩＤを追加
    //
    public function validationData() {

        $request = $this->request->all();
        switch( Route::currentRouteName() ) {
            case "customer.edit" :
            case "customer.update" :
            case "customer.delete" :
                $request =  array_merge( $request, ['id' => $this->customer->id] );
                break;
        }
        return $request;
    }
    
    //　バリデーションデータの整形（バリデーション前の準備）
    //
    protected function prepareForValidation() {

        $route_name = Route::currentRouteName();
        
        if( $route_name == "customer.store" or $route_name = "customer.update" ) {
            $trim = [ '-', ' ','－', '―', '‐' ]; // スペース・ハイフンを除去
            $hyphen = [ '-', '－', '―', '－', 'ー' ];  // 全角ハイフン
            $values = $this->all();
            // if_debug( $route_name,$values );
            // $values = self::adjust_input( $this->all() );
            $values = array_merge( $values, [
                'kana'     => str_replace( $hyphen, "ー", mb_convert_kana( $this->kana, 'KCVS' )),
                'zip_code' => str_replace( $trim, "",     mb_convert_kana( $this->zip_code, 'n' )),
                'tel'      => str_replace( $trim, "",     mb_convert_kana( $this->tel, 'n' )),
                'fax'      => str_replace( $trim, "",     mb_convert_kana( $this->fax, 'n' )),
                'mobile'   => str_replace( $trim, "",     mb_convert_kana( $this->mobile, 'n' )),
                'street'  => str_replace( $hyphen, '-',  mb_convert_kana( $this->street,  'as' )),
                'building' => str_replace( $hyphen, '－',  mb_convert_kana( $this->building, 'as' )),
            ] );
            $this->merge( $values );
            
            // $this->flash( '_old_input', $values );
            // if_debug( $values, $this );
        }
    }
    
    //　入力値の整形
    //
    protected function adjust_input( $inputs ) {
                    $trim = [ '-', ' ','－', '―', '‐' ]; // スペース・ハイフンを除去
            $hyphen = [ '-', '―', '－', 'ー' ];  // ハイフン
            $values = $this->all();
        $values =  [
                'kana'     => str_replace( $hyphen, "ー", mb_convert_kana( $inputs->kana, 'KCVS' )),
                'zip_code' => str_replace( $trim, "",     mb_convert_kana( $inputs->zip_code, 'n' )),
                'tel'      => str_replace( $trim, "",     mb_convert_kana( $inputs->tel, 'n' )),
                'fax'      => str_replace( $trim, "",     mb_convert_kana( $inputs->fax, 'n' )),
                'mobile'   => str_replace( $trim, "",     mb_convert_kana( $inputs->mobile, 'n' )),
                'street'   => str_replace( $hyphen, '-',  mb_convert_kana( $inputs->street,  'as' )),
                'building' => str_replace( $hyphen, '－',  mb_convert_kana( $inputs->building, 'as' )),
            ];
        return $values;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $route_name = Route::currentRouteName();
        $rules = [];
        
        if( $route_name == "customer.store" or $route_name == "customer.update" ) {
            $rules = [  'name'      => ['required', 'string', 'max:255'],
                        'kana'      => [ 'nullable', 'regex:/^[ア-ヶー　]+$/u' ],
                        'zip_code'  => [ 'nullable', 'regex:/^\d{7}$/' ],
                        'tel'       => [ 'nullable', 'regex:/^0\d+$/' ],
                        'fax'       => [ 'nullable', 'regex:/^0\d+$/' ],
                        'mobile'    => [ 'nullable', 'regex:/^0\d+$/' ],
                        'birth_day' => [ 'nullable', 'date' ],
                    ];

            if( config( 'customer.validation.email.required' )) {
                $rules['email'] = ['required', 'email', 'max:255' ];
            } else {
                $rules['email'] = ['nullable', 'email', 'max:255' ];
            }
                    
            
        }

        //　ロックIDのバリデーション
        //
        if( $route_name == "customer.edit" or $route_name == "customer.update" ) {
            if( config( 'customer.locked_ids')) {
                $rules['id'] = Rule::notIn( config( 'customer.locked_ids' ));
            }
        }

        // dd( $rules );
        // dd( $this );
        return $rules;
    }
    
    public function messages() {
        // $m = config( 'password.error.'.config('customer.password_valicator'));
        // dd( $m, 1);
        return [
                'id.not_in' => 'このIDはロックされています',
                'password.confirmed' => 'パスワードが不一致です',
                // array_key_first( $m ) => $m[array_key_first( $m )],    
            
                'kana.regex'     => '「ヨミカナ」はカタカナ・スペースのみ入力可能です。',
                'zip_code.regex' => '郵便番号の入力が不正です。ハイフンは不要です。',
                'tel.regex'      => '電話番号の入力が不正です。ハイフンは不要です。',
                'mobile.regex'   => '携帯電話の入力が不正です。ハイフンは不要です。',
                'fax.regex'      => 'FAX番号の入力が不正です。ハイフンは不要です。',
                
            
            
            ];
        
    }
    
}
