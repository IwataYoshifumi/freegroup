<?php

namespace App\Models\Vacation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

use DB;
use Carbon\Carbon;

use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;

use App\Http\Controllers\Vacation\Notifications\RemindApplicationProcessed;
use App\Http\Controllers\Vacation\Notifications\RemindApproval;

class Application extends Model
{
    //
    protected $fillable = [
        'user_id','date','status','approval_date','type','reason','num','start_date','end_date','memo', 'paidleave_id',
        'start_time', 'end_time',
    ];
    
    protected static $types = ['有給休暇' => '有給休暇', '特別休暇' => '特別休暇' ];
    
    public static function getDayOffTypes() {
        
        $array = array("" => "") ;
        $array = array_merge( $array, self::$types );
        return $array;
    }

    //　ＤＢリレーション
    //

    //  申請先DBのリスト
    //
    public function approvals() {
        return $this->hasMany( 'App\Models\Vacation\Approval', 'application_id' );
    }
    
    //　承認者のUsers リストを返す
    //
    public function approvers() {
        return $this->belongsToMany( 'App\Models\Vacation\User', 'approvals', 'application_id', 'approver_id');

        // $approvals = $this->approvals;
        // $users = array();
        // foreach( $approvals as $app ) {
        //     array_push( $users, $app->approver );
        // }
        // return( $users );
    }
    
    //　承認処理を行っていない承認者のリストを返す
    //
    public function unTreatedApprovers() {
        
        $approvals = $this->approvals;
        $users = array();
        foreach( $approvals as $app ) {
            if( $app->status == "承認待ち" ) {
                array_push( $users, $app->approver );
            }
        }
        return( $users );
    }
    
    //　申請者
    //
    public function user() {
        return $this->belongsTo( 'App\Models\Vacation\User', 'user_id' );
    }
    public function applicant() {
        return $this->belongsTo( 'App\Models\Vacation\User', 'user_id' );
    }
    
    //　有給休暇の申請の場合、対象の有給休暇割当レコードを返す
    //
    public function paidleave() {
        return $this->belongsTo( 'App\Models\Vacation\Vacation', 'allocated_vacation_id' );
    }
    public function allocated_paidleave() {
        return $this->paidleave();
    }

    //　休暇期間が過ぎたにも関わらず、完了処理されていない申請を返す
    //
    public static function getIncomplited( $find ) {
        
        $applications = Application::where( 'end_date', '<=', $find['end_date'] )
                                    // ->whereIn( 'status', ['承認'] )
                                    // ->whereIn( 'status', [ '承認待ち'] )
                                    ->whereIn( 'status', ['承認', '承認待ち'] )
                                    ->get();
        return $applications;
    }
    
    //　催促通知をする
    //
    //　申請者に対しては、承認済み休暇申請の休暇取得完了処理を催促
    //　承認者に対しては、休暇申請の承認業務を催促
    //　催促するタイミングは、休暇終了後
    //　タスクスケジュールで毎日呼び出され催促メールを送る
    //
    public function notifyIncompleted() {
        
        if( $this->status == "承認" ) {
            $user = $this->user;
            // dump( $user->name );
            $user->notify( new RemindApplicationProcessed( $this ));
            
        } elseif( $this->status == "承認待ち" ) {
            $approvers = $this->unTreatedApprovers();
            // dd( "notifyIncompleted", $approvers );
            foreach( $approvers as $user ) {
                $user->notify( new RemindApproval( $this ));
            }
        }
    }
    public static function DoMentionIncompleted() {
        
        $find['end_date'] = new Carbon( 'today' );
        $applications = Application::getIncomplited( $find );
        
        foreach( $applications as $app ) {
            $app->notifyIncompleted();
        }

    }

    ///////////////////////////////////////////////////////////////////////////
    //
    // 休暇申請データに対する処理
    //
    ///////////////////////////////////////////////////////////////////////////

    //　休暇申請（申請データ作成）
    //
    static public function applicate( $request, User $user ) {
        
        //　有給取得日数（時間有給）の処理
        //
        if( isset( $request->start_time) and isset( $request->end_time )) {
            $num = Application::calc_hourly_paidleave( $request->start_time, $request->end_time );
        } else {
            $num = $request->num;
        }
        
        $application = new Application();
        $application->user_id       = $user->id;
        $application->date          = $request->date;
        $application->start_date    = $request->start_date;
        $application->end_date      = $request->end_date;
        $application->num           = $num;
        $application->type          = $request->type;
        $application->reason        = $request->reason;
        $application->status        = "承認待ち";
        //  時間有給
        //
        if( $num < 1 ) {
            $application->start_time = $request->start_time;
            $application->end_time   = $request->end_time;
        }
        
        
        if( $request->type == "有給休暇") {
            $application->allocated_vacation_id = $request->vacation_id;
        } else {
            $application->allocated_vacation_id = null;
        }
        $application->save();
        
        return $application;
    }
    static public function create( $request, User $user ) {
        return Application::applicate( $request, $user );
    }

    //　申請の却下処理
    //
    public function reject( Approval $approval ) {

        $date = new Carbon('today', 'Asia/Tokyo');
        $today = $date->toDateString();

        $approvals = $this->approvals;
    
        $this->status = "却下";
        $this->approval_date = $today;
        $this->save();
        
        //　休暇データの更新
        //
        Vacation::create_rejection( $this );        
        
        //　申請データを却下処理
        //
        foreach( $approvals as $app ) {
            if( $approval->id == $app->id ) {
                $app->status = "却下";
            } else {
                if( $app->status = "承認待ち" ) {
                    $app->status = "取り下げ（却下）";
                }
            }
            $app->date = $today;
            $app->save();
        }
        
        // if( $this->type == "有給休暇" ) {
        //     $paidleave = $this->paidleave->recalculate();
        //     // dd($this, $paidleave );
        //     // $paidleave->recalculate();
        // }
        return $this;
    }
    
    //　申請が承認済み処理をする
    //
    public function approve() {
        if( $this->is_all_approvals_approved()) {
            $today = new Carbon('today', 'Asia/Tokyo');
            $this->status = "承認";
            $this->approval_date = $today->toDateString();
            $this->save();
            
            //　休暇データの更新
            //
            Vacation::create_approval( $this ); 
            // $this->allocated_paidleave->paidleave_recalculate();            
            
        } 
        return $this;            
    }
    
    //　申請が承認済みか確認する
    //
    public function is_approved() {
        if( $this->status == "承認" or $this->status == "休暇取得完了") {
            return true;
        } else {
            return false;            
        }
    }

    //　承認データが全て承認済みなら TRUE
    // 
    public function is_all_approvals_approved() {
        foreach( $this->approvals as $approval ) {
            if( $approval->status != "承認" ) { return false; }
        }
        return true;            
    }

    //　時間有給の時間数を計算( return 1以下の少数 )
    //
    public static function calc_hourly_paidleave( $t1, $t2 ) {
        $time_1 = new Carbon( "2020-1-1 ". $t1 );
        $time_2 = new Carbon( "2020-1-1 ". $t2 );
        $diff = $time_1->diffInMinutes( $time_2 );
        $num = $diff / 60 / 8;
        if( $time_1->gt( $time_2 )) { $num = $num * -1; }
        
        // dd( "Application::calc_hourly_paidleave", $diff, $num, $time_1, $time_2 );
        return $num;

    }

    //  有給期間の表示
    //
    public function print_period() {
        // dump( $this );
        if( $this->num == 1 ) {
            return $this->start_date;
        } elseif( $this->num > 1 ) {
            return $this->start_date ."～".$this->end_date;
        } elseif( $this->num < 1 ) {
            $start_time = preg_replace( '/\:\d+$/', "", $this->start_time );
            $end_time   = preg_replace( '/\:\d+$/', "", $this->end_time );
            // dump( $start_time );
            return $this->start_date." ( ".$start_time." - ".$end_time." )";
        } else {
            abort( 500, 'Application.print_peroid エラー');
        }
    }
    public function print_period_for_index() {
        if( $this->num <= 1 ) {
            return $this->start_date;
        } elseif( $this->num >= 2 ) {
            $period ="<div data-toggle='tooltip' data-placement='top' title='".$this->start_date."～".$this->end_date."'>";
            $period .= $this->start_date."～</div>";
            return new HtmlString( $period );
            // return $period;
        } else {
            abort( 500, 'Application.print_peroid_for_index エラー');
        }
    }
    public function print_num() {
        
        if( $this->num > 0 ) {
            return Vacation::pnum( $this->num );
        } else {
            abort( 500, 'Application.print_num エラー');
        }
    }
}
