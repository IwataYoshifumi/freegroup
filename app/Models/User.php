<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\RequestPassword;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $model = "User";
    protected $config_path = "user";
    protected static $route_name = [ 'password.email' => "user.password.reset", 
                                     'password.reset' => "user.password.reset",
                                     'password.update' => "user.password.update",
                                    ];
    
    protected $fillable = [
        'name', 'email', 'dept_id', 'grade', 'password', 'retired', 'date_of_retired',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //　リレーションの定義
    //
    public function dept() {
        return $this->belongsTo( 'App\Models\Dept' );
    }
    public function department() {
        return $this->dept();
    }

    //　検索する
    //
    public static function search( $find, $sort = null, $asc_desc = null ) {
        
        if( isset( $find['retired'] )) {
            if( $find['retired'] != 'all' ) {
                $users = self::where( 'retired', $find['retired'] );
            } else {
                $users = self::select( "*" );
            }
        } else {
            $users = self::select( "*" );
        }

        if( isset( $find['name'])) {
            $users = $users->where( 'name', 'like', '%'.$find['name'].'%' );
        }
        
        if( isset( $find['email'] )) {
            $users = $users->where( 'email', 'like', '%'.$find['email'].'%' ); 
        }
        if( isset( $find['dept_id'])) {
            $users = $users->where( 'dept_id', $find['dept_id'] );
        }

        //  ソート
        //
        if( isset( $sort ) && is_array( $sort )) {
            foreach( $sort as $i => $s ) {
                if( empty( $s )) { continue; }
                if( ! empty( $asc_desc[$i]) ) {
                    $users = $users->orderBy( $s, $asc_desc[$i] );
                } else {
                    $users = $users->orderBy( $s );
                }
            }
        }
        // dump( $users );
                
        if( isset( $find['paginate'] )) {
            return $users->with('department')->paginate( $find['paginate'] );
        } else {
            return $users->with('department')->get();
        }
    }
    
    //  管理者かユーザか顧客か
    //
    public function is_admin() {
        if( $this->model == "Admin" ) { return true; } else { return false; }
    } 
    public function is_user() {
        if( $this->model == "User" ) { return true; } else { return false; }
    }
    public function is_customer() {
        if( $this->model == "Customer" ) { return true; } else { return false; }
    }
    
    // 退職済みか、在任中か
    //
    public function is_retired() {
        if( $this->retired ) { return true; } else { return fales; }
    }
    
    public function is_in_office() {
        return ! $this->is_retired();
    }
    
    // ロックＩＤか( update, delete ができないＩＤ) middleware ( is_lockedで使用)
    //
    public function is_locked() {
        // dd( config( $this->config_path  ));
        if( in_array( $this->id, config( $this->config_path.".locked_ids"  ))) { 
            // dump( "locked");
            return true; 
        } else { 
            // dump( "no locked");
            return false; 
        }
    }
    
    //　パスワードリセットメール設定(ルート名、password.email, GET password.reset )
    //
    public function sendPasswordResetNotification( $token ) {
        // dump( $token );
        $notice = new ResetPasswordNotification( $token );
        $callback = get_class( $this )."::getResetPasswordURL";
        $callback = "App\Models\User::getResetPasswordURL";
        // dd( $callback );
        $notice->createUrlUsing( $callback );
        $this->notify( $notice );
    }
    static public function getResetPasswordURL( $notifiable, $token ) {
        // $url = url( route( $this->route_name_for_reset_password, [ 'token' => $token, 'email' => $notifiable->getEmailForPasswordReset(), ] ));
        $url = url( route( self::$route_name['password.email'], [ 'token' => $token, 'email' => $notifiable->getEmailForPasswordReset(), ] ));
        return $url;
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function p_dept_name() {
        if( ! $this->dept ) { return ""; }
        
        return "【". $this->dept->name. "】";

    }
}

