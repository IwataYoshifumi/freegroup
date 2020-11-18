<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SansanRequest extends FormRequest
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
                
                
        $return = [
            'SecretKey'             => ['required'], 
            'CompanyName'           => ['string', 'max:250'],
            'CompanyNameReading'    => ['string', 'max:250'],
            'DepartmentName'        => ['string', 'max:250'],
            'Title'                 => ['string', 'max:250'],
            'LastName'              => ['string', 'max:250', 'required' ],
            'FirstName'             => ['string', 'max:250'],
            'LastNameReading'       => ['string', 'max:250'],
            'FirstNameReading'      => ['string', 'max:250'],
            'PostalCode1'           => ['numeric', 'size:7'],
            'Prefecture'            => ['string', 'max:250'],
            'City'                  => ['string', 'max:250'],
            'Address'               => ['string', 'max:250'],
            'Building'              => ['string', 'max:250'],
            'Tel1'                  => ['string', 'max:250'],
            'Fax1'                  => ['string', 'max:250'],
            'Mobile1'               => ['string', 'max:250'],
            'Email1'                => ['email', 'max:250', 'required'],
            'Url1'                  => ['string', 'max:250'],
            'Memo'                  => ['string', 'max:2000'],
            'TagNames'              => ['string', 'max:50'], 
           ];
        return $return;
        
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
