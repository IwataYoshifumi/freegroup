<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\User as OriginalUser;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\ScheduleType;

class User extends OriginalUser {

    //　リレーションの定義
    //

    //　自分が作成したスケジュール・日報
    //
    public function schedules() {
        return $this->hasMany( Schedule::class );
    }

    public function reports() {
        return $this->hasMany( Report::class );
    }
    
    public function files() {
        return $this->hasMany( MyFile::class );
    }
    
    public function schedule_types() {
        return $this->hasMany( ScheduleType::class );
    }

    //　関連社員として紐づけられたスケジュール・日報
    //
    public function allocated_schedules() {
        // return $this->hasMany( 'App\myHttp\GroupWare\Models\Schedule' );
        // return $this->belongsToMany( Schedule::class, 'relations', 'user_id', 'schedule_id' );
        return $this->morphToMany( Schedule::class, 'scheduleable' );
    }

    public function allocated_reports() {
        return $this->morphToMany( Report::class, 'reportable' );
    }


    

}
