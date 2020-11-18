<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailOrderRequest extends FormRequest
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
    public function rules()
    {
                // dd( $this );
        
        return [ 
                 "input.name"     => [ 'required', 'string' ],
                //  'input.address'  => [ 'required', 'string' ],
                 'input.person'   => [ 'required', 'string' ],
                 'input.tel'      => [ 'nullable', 'regex:/^0\d+-\d+-\d+$/' ],
                 'input.fax'      => [ 'nullable', 'regex:/^0\d+-\d+-\d+$/' ],
                 'input.delivery_date' => [ 'nullable', 'date' ],
                 'input.email'    => [ 'required', 'email', 'confirmed' ],
                 'input.delivery_postcode' => ['required', 'regex:/^\d{3}-\d{4}$/'],
                 'input.delivery_prefecture' => ['required'],
                 'input.delivery_city' => ['required'],
                 'input.delivery_address' => ['required'],
                 'input.delivery_tel' => ['required', 'regex:/^0\d+-\d+-\d+$/'],
                 'input.delivery_person' => ['nullable'],
                 'num.*' => [ 'nullable', 'gte:0', 'integer' ],
                 'total' => [ 'gte:1' ],
                 
                 

        
            //
        ];

        
    }
    
    public function messages() {
        
        return [
                'input.name.required'   => '名前を入力してください',
                'input.address.required'=> '住所を入力してください。',
                'input.email.confirmed' => 'メールが不一致です。',
                'input.email.required'  => 'メールアドレスを入力してください',
                'input.person.required' => '担当者名を入力してください',
                'input.tel.regex'      => '電話番号は「－（半角）」ハイフン付きで入力してください。',
                'input.fax.regex'      => 'FAX番号は「－（半角）」ハイフン付きで入力してください。',
                'input.delivery_name.required'   => '納品先名を入力してください。',
                'input.delivery_postcode.required' => '納品先の郵便番号を入力してください',
                'input.delivery_postcode.regex' => '納品先の郵便番号は半角で「＊＊＊－＊＊＊＊」形式で入力してください',
                'input.delivery_prefecture.required' => '納品先の都道府県を入力してください',
                'input.delivery_city.required' => '納品先の市区町村を入力してください',
                'input.delivery_address.required' => '納品先の住所を入力してください',
                'input.delivery_tel.required' => '納品先の電話番号を入力してください',
                'input.delivery_tel.regex'    => '納品先電話番号は「－（半角）」ハイフン付きで入力してください。',
                'num.*.gte'                 => '注文数量はゼロ以上の数字を入力してください',
                'total.gte'                 => '何も注文が入力されていません',
                
            
            
            
            ];
        
    }
}
