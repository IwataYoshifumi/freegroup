<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

// class Admin extends Authenticatable {
class Admin extends User {

    // use Notifiable;
    
    protected $model = "Admin";
    protected $config_path = "admin";
    protected static $route_name = [ 'password.email' => "admin.password.reset", 
                                     'password.reset' => "admin.password.reset",
                                     'password.update' => "admin.password.update",
                                    ];
    
    protected $fillable = [
        'name', 'email', 'password', 'retired', 'date_of_retired',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


}
