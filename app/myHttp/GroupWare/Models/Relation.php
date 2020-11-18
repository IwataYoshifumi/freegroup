<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Customer;
use App\Models\User;


class Template extends Model {

    // use Notifiable;
    // use SoftDeletes;
    
    protected $fillable = [
        
 
    ];

    // protected $hidden = [];

    // protected $casts = [];


    //　検索する
    //
    static public function search( $find, $sort = null, $asc_desc = null ) {
        
    }

}
