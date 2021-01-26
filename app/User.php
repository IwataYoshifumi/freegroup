<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'retired', 'date_of_retired',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //　ユーザを検索する
    //
    //
    public static function search( $find, $sort = null, $asc_desc = null ) {
        
        // if_debug( $find );
        if( isset( $find['retired'] )) {
            if( $find['retired'] != 'all' ) {
                $users = User::where( 'retired', $find['retired'] );
            } else {
                $users = User::select( "*" );
            }
        } else {
            $users = User::select( "*" );
        }

        if( isset( $find['name'])) {
            $users = $users->where( 'name', 'like', '%'.$find['name'].'%' );
        }
        
        if( isset( $find['email'] )) {
            $users = $users->where( 'email', 'like', '%'.$find['email'].'%' ); 
        }
        if( isset( $find['user_id'])) {
            $users = $users->where( 'id', $find['user_id']);
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
        // if_debug( $users );
                
        if( isset( $find['paginate'] )) {
            return $users->paginate( $find['paginate'] );
        } else {
            return $users->get();
        }
    }
    
}
