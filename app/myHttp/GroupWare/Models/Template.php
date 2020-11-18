<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;


class Relation extends Model {

    // use Notifiable;
    // use SoftDeletes;
    
    protected $fillable = [
        'user_id', 'customer_id', 'schedule_id', 'report_id',
 
    ];
}
