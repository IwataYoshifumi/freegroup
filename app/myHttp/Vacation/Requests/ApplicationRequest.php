<?php

namespace App\Http\Requests\Vacation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Vacation\Vacation;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;

class ApplicationRequest extends FormRequest
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
     
    // バリデーションデータの修正
    //
    protected function prepareForValidation() {
        if( preg_match( '/application\.store_hourly$/', Route::currentRouteName() )){  
            $this->merge( ['num' => Application::calc_hourly_paidleave( $this->start_time, $this->end_time ) ]);
        }
    }
     
    public function rules()
    {
        //　通常有給、特別休暇のバリデーション( vacation.application.store )
        //
        if( preg_match( '/application\.store$/', Route::currentRouteName() )) {
            // dd( 'aaa');
            $start_date = new Carbon( $this->start_date );
            $end_date   = new Carbon( $this->end_date   );
            $diff = $start_date->diffInDays($end_date)+1;
    
            $rules =  [
                'user_id'        => 'required',
                'date'           => 'required|date',
                'type'           => 'required',
                'reason'         => 'required',
                'start_date'     => 'required|date|before_or_equal:'.$this->end_date,
                'end_date'       => 'required|date|after_or_equal:'.$this->start_date,
                'num'            => 'required|integer|in:'.$diff,
                'check_approver' => 'required|integer|gte:1',
            ];
            
            // 有給休暇　申請内容のバリデーション
            //
            if( $this->input('type') == '有給休暇' ) {
                if( empty( $this->input('vacation_id'))) {
                    $rules['vacation_id'] = 'required';
                } else {
                    //  有給休暇の残日数、休暇期間のバリデーション
                    //
                    $paidleave = Vacation::where( 'id', $this->input('vacation_id') )->get()->first();
                    // dd( $paidleave );
                    $rules['start_date'] = $rules['start_date']."|after_or_equal:".$paidleave->allocate_date;
                    $rules['end_date']   = $rules['end_date']."|before_or_equal:".$paidleave->expire_date;
                    $rules['num']        = $rules['num']."|lte:".$paidleave->remains_num;
                    //
                    //  割当年度の古いものから有給を消化するようにバリデーション
                    //
                    // dd( $this );
                    $p1 = Vacation::where( 'id', $this->vacation_id )->get()->first();
                    $p2 = Vacation::where( 'user_id', $this->user_id )
                                  ->where( 'id', '!=', $this->vacation_id )
                                  ->where( 'expire_date', '>=', $this->end_date )
                                  ->where( 'expire_date', '<=', $p1->expire_date  )
                                  ->where( 'remains_num', '>=', 1 )
                                  ->get()->first();
                    // dump( $p1->all(), optional($p2)->all() );
                    if( ! empty( $p2->id )) {
                        $rules['vacation_id'] = 'same:'.$p2->id;
                    }
                }
            }

        }

        //　時間有給のバリデーション( vacation.application.store_hourly )
        //
        if( preg_match( '/application\.store_hourly$/', Route::currentRouteName() )){     

            $start_date = new Carbon( $this->start_date );
            $this->merge( ['end_date' =>  $this->start_date ] );
            $this->merge( ['num' => Application::calc_hourly_paidleave( $this->start_time, $this->end_time ) ]);
            // dd( $this->all() );
            // $diff = $start_date->diffInDays($end_date)+1;
            $time = 'regex:/^\d+:00$/';
            $rules =  [
                'user_id'        => 'required',
                'date'           => 'required|date',
                'type'           => 'required',
                'reason'         => 'required',
                'start_date'     => "required|date",
                'start_time'     => 'required',
                'end_time'       => 'required',
                'check_approver' => 'required|integer|gte:1',
                'num'            => 'regex:/^-{0,1}0\.\d+$/',
            ];

            
            //  有給休暇の残日数、休暇期間のバリデーション
            //
            $paidleave = Vacation::where( 'id', $this->input('vacation_id') )->get()->first();
            // // dd( $paidleave );
            // $rules['start_date'] = $rules['start_date']."|after_or_equal:".$paidleave->allocate_date;
            // $rules['start_date'] = $rules['start_date']."|before_or_equal:".$paidleave->expire_date;

            $rules['num']        = $rules['num']."|gt:0|lte:".$paidleave->remains_num;
            //
            //  割当年度の古いものから有給を消化するようにバリデーション
            //

            $p1 = Vacation::where( 'id', $this->vacation_id )->get()->first();
            // dd( $p1 );
            $p2 = Vacation::where( 'user_id', $this->user_id )
                          ->where( 'id', '!=', $this->vacation_id )
                          ->where( 'expire_date', '>=', $this->end_date )
                          ->where( 'expire_date', '<=', $p1->expire_date  )
                          ->where( 'remains_num', '>', 0 )
                          ->get()->first();
            // dd( $p1, $p2 );
            if( ! empty( $p2->id )) {
                $rules['vacation_id'] = 'same:'.$p2->id;
                
            }
            // dd( $rules, $this, $this->all() );
        }
        
        if( is_null($rules)) { abort( 403, 'ApplicationRequest:未定義ルート'); }
        // dd( $rules );
        return $rules;
    }
    
    //　エラーメッセージ
    //
    public function messages() {

        $messages = [
            'end_date.after_or_equal'   => "休暇期間の開始日より終了日が前になっています。",
            'end_date.before_or_equal'  => "有給の有効期限を超えています。",
            'start_date.after_or_equal' => "有給割当日より前に休暇申請できません",
            'start_date.before_or_equal'=> "休暇期間の開始日が終了日より後になっています。",
            'num.integer'               => "休暇日数は整数を入力してください",
            'num.in'                    => "休暇日数が間違っています",

            'check_approver.required'   => "申請先を入力してください(0)",
            'check_approver.gte'        => "申請先を入力してください(1)",
            'vacation_id.required'      => "「有給割当」を選んでください",
            'vacation_id.same'          => "有効期限の短い「有給割当」を選択してください",
            ];
        
        if( preg_match( '/application\.store_hourly$/', Route::currentRouteName() )) {
            $messages = array_merge( $messages, ['start_time.required' => '休暇時間を入力してください',
                                                 'num.lte'             => "申請休暇日数が、有給残日数を超えています。一旦残日数分で申請を行って、残りを再度申請してください",
                                                 'num.gt'              => "休暇時間が前後しています",
                                                 'num.regex'           => '時間有給を８時間以上設定する場合は、通常の休暇申請で申請をしてください。',
                                                 'end_time.required'   => '休暇時間を入力してください',
                                                 'end_time.gt'         => '休暇時間を正しく入力してください',
                    ]);
            
        }
    // dd( $messages );

        return $messages;
    }
    
}
