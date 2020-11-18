<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\Vacation\Dept as OriginalDept;
use User;

class Dept extends OriginalDept {

    //　リレーションの定義
    //
    public function users() {
        return $this->hasMany( User::class );
    }
    
    
    

}
