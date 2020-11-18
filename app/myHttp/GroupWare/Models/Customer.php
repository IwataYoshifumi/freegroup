<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

use App\Models\Customer as OriginalCustomer;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

class Customer extends OriginalCustomer {

    //　リレーションの定義
    //
    
    //　関連顧客として紐づけられているスケジュール・日報
    //
    public function schedules() {
        // return $this->belongsToMany( Schedule::class, 'relations', 'customer_id', 'schedule_id' );
        return $this->morphToMany( Schedule::class, 'scheduleable' );
    }
    
    //　関連顧客として紐づけられている日報
    //
    public function reports() {
        // return $this->belongsToMany( Report::class, 'relations', 'customer_id', 'schedule_id' );
        // return $this->belongsToMany( Report::class, 'r_reports_customers', 'customer_id', 'report_id' );
        return $this->morphToMany( Report::class, 'reportable' );
    }
    

}
