<?php

namespace App\Models\Vacation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vacation\ApprovalMaster;
use App\Models\Vacation\User;

class ApprovalMasterList extends Model
{
    //
    public function approvalMaster() {
        return $this->belongsTo( "App\Models\Vacation\ApprovalMaster" );
    }
    
    public function approver() {
        return $this->belongsTo( "App\Models\Vacation\User", "user_id");
    }

    public function user() {
        return $this->approver();
    }

    
}
