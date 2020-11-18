<?php

namespace App\Models\Vacation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vacation\ApprovalMasterList;

class ApprovalMaster extends Model
{
    //
    protected $fillable = [
        'name', 'memo' 
    ];
    
    public function approvalMasterLists() {
        return $this->hasMany("App\Models\Vacation\ApprovalMasterList");
    }

    public function lists() {
        return $this->approvalMasterLists();
    }
    
    
}
