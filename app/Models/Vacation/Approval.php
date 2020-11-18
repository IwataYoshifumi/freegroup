<?php

namespace App\Models\Vacation;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use DB;

use App\Models\Vacation\User;
use App\Models\Vacation\Application;
use App\Models\Vacation\VacationList;

class Approval extends Model
{
    //
    protected $fillable = [
         'application_id', 'status', 'date', 'comment', 'approver_id', 'applicant_id',
    ];
    
    //　休暇申請
    //
    public function application() {
        return $this->belongsTo( 'App\Models\Vacation\Application', 'application_id' );
    }

    //　承認者
    //
    public function approver() {
        return $this->belongsTo( 'App\Models\Vacation\User', 'approver_id' );
    }
    
    //　申請者
    //
    public function applicant() {
        return $this->belongsTo( 'App\Models\Vacation\User', 'applicant_id' );
    }
    
    //　承認者を登録する
    //
    static function create( Application $application, $approvers ) {
        // $approvers = $request->approvers;
        // dd( $approvers );

        $user_id = $application->applicant->id;            
        foreach( $approvers as $i => $approver_id ) {
            if( ! is_null( $approver_id )) {
                $application->approvals()->create( 
                                [ 'applicant_id'   => $user_id,
                                  'approver_id'    => $approver_id, 
                                  'date'           => $application->date,
                                  'status'         => '承認待ち'
                                ]);
            }
        }
        return true;
    }
    
    // 
    //

    
}
