<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

// class Admin extends Authenticatable {
class Customer extends User {

    // use Notifiable;
    use SoftDeletes;
    
    protected $model = "Customer";
    protected $config_path = "customer";
    protected static $route_name = [ 'password.email' => "customer.password.reset", 
                                     'password.reset' => "customer.password.reset",
                                     'password.update' => "customer.password.update",
                                    ];
    
    protected $fillable = [
        'name','kana', 'email', 'password', 'zip_code', 'prefecture', 'city', 'address', 'tel', 'fax', 'mobile', 'birth_day', 'sex', 'memo',
        #'login_id',
        'salse_force_id'
        
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'birth_day' => 'date',
    ];


    //　検索する
    //
    public static function search( $find, $sort = null, $asc_desc = null ) {
        
        if( isset( $this )) { 
            $customers = $this;
        } else {
            $select   = "id, name, kana, email, zip_code, prefecture, city, street, building, tel, fax, mobile, birth_day, sex, memo,";
            $select  .= "CONCAT( IFNULL(prefecture, '') , IFNULL(city,''), IFNULL(street,'') ,' ',IFNULL(building,'') ) as address";


            if( config( 'customer.salseforce.enable')) {
                $select .=  ", salseforce_id";
            }
            // $customers = self::select( "*" );
            // $customers = self::selectRaw( "id,name,tel" );
            $customers = self::selectRaw( $select );

        }
        
        if( isset( $find['name'])) {
            $customers = $customers->where( 'name', 'like', '%'.$find['name'].'%' );
        }
        if( isset( $find['kana'])) {
            $customers = $customers->where( 'kana', 'like', '%'.$find['kana'].'%' );
        }
        
        if( isset( $find['email'] )) {
            $customers = $customers->where( 'email', 'like', '%'.$find['email'].'%' ); 
        }
        if( isset( $find['prefecture'] )) {
            $customers = $customers->where( 'prefecture', 'like', '%'.$find['prefecture'].'%' ); 
        }
        if( isset( $find['city'] )) {
            $customers = $customers->where( 'city', 'like', '%'.$find['city'].'%' );
        }
        if( isset( $find['street'] )) {
            $customers = $customers->where( 'street', 'like', '%'.$find['street'].'%' );
        }
        if( isset( $find['telephone'])) {
            $customers = $customers->where( function( $query ) use( $find ) {
                    $query->where(   'tel',    $find['telephone'] )
                          ->orWhere( 'fax',    $find['telephone'] )
                          ->orWhere( 'mobile', $find['telephone'] );
                        });
        }

        //  ソート
        //
        if( isset( $sort ) && is_array( $sort )) {
            foreach( $sort as $i => $s ) {
                if( empty( $s )) { continue; }
                if( ! empty( $asc_desc[$i]) ) {
                    $customers = $customers->orderBy( $s, $asc_desc[$i] );
                } else {
                    $customers = $customers->orderBy( $s );
                }
            }
        }
                
        if( isset( $find['paginate'] )) {
           return $customers->paginate( $find['paginate'] );
        } else {
            return $customers->get();
        }
    }
    

    
    //　リレーション・プロパティ関数
    //
    public function age() {
        if( is_null( $this->birth_day )) { return null; }
        
        $today = Carbon::today();
        return $today->diffInYears( $this->birth_day );
        
    }
    
    //　表示用関数
    //
    public function p_address() {
        $address = "〒".$this->p_zip_code()."<br>".$this->preg_replace.$this->city.$this->street;
        if( ! empty( $this->building )) { $address .= "<br>".$this->building; }
        return new HtmlString( $address );
    }
    
    public function p_zip_code() {
        $zip_code = preg_replace( '/^(\d{3})(\d{4})$/', '$1-$2', $this->zip_code );
        return ( ! $zip_code ) ? $zip_code : $this->zip_code;
    }

    public function p_age() {        // 年齢表示

        if( is_null( $this->birth_day )) { return null; }
        return "( ".$this->age()." 才)";
    }
}
