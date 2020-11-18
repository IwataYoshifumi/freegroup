<?php

namespace App\Models\Vacation;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

use App\Models\Vacation\User;
use App\Models\Vacation\Application;
use App\Models\Vacation\VacationList;

define( 'HOURLY_PAID_LEAVE_UNIT', ( config( 'vacation.config.is_valid_hourly_paidleave' )) ? config( 'vacation.config.hourly_paidleave.unit') : 1 );
// dd(  config( 'vacation.config.hourly_paidleave.unit'), config('vacation.config'), HOURLY_PAID_LEAVE_UNIT);

class Vacation extends Model {
    //
    const HOURLY_PAID_LEAVE_UNIT = HOURLY_PAID_LEAVE_UNIT;

    protected $table = 'vacations';
    
    protected $fillable = [
        'user_id', 'application_id', 'type', 'year','allocate_date', 'expire_date', 'num', 'action', 'done_expire',
        'allocated_num', 'application_num', 'approval_num', 'completed_num', 'expired_num', 'remains_num',
    ];

    public function user() {
        return $this->belongsTo( 'App\Models\Vacation\User'  );
        // return $this->hasOne( 'App\Models\Vacation\User' );
    }
    
    public function applications() {
        return $this->hasMany( 'App\Models\Vacation\Application', 'allocated_vacation_id' );
    }

    public function application() {
        return $this->belongsTo( 'App\Models\Vacation\Application', 'application_id' );
    }
    
    public function vacation_lists() {
        return $this->hasMany( 'App\Models\Vacation\VacationList', 'vacation_id' );
    }
    
    public function lists() {
        return $this->hasMany( 'App\Models\Vacation\VacationList', 'vacation_id' );
    }
    
    // 有給休暇データを返す
    //
    public function paidleave() {
        if( $this->type != "有給休暇") { return null; }

        $paidleave = Vacation::where( 'year', $this->year )
                             ->where( 'user_id', $this->user_id )
                             ->where( 'action', '割当')->first();
        return $paidleave;
    }

    //　ある年度の有給休暇割りて済みのUser_idを取得する
    //
    public static function getArrayAllocatedUser( $year ) {
        $results = Vacation::select( 'user_id' )
                             ->where( 'year', $year )
                             ->get();
        $array = array();
        foreach( $results as $result ) {
            $array[$result->user_id] = $result->user_id;
        }
    
        return $array;
        
    }
    
    
    // 一覧用DB申請
    //
    public static function DBselect( $find ) {

        $query = DB::table( 'vacations as v')
                    ->join( 'users as u', 'v.user_id', 'u.id' )
                    ->select( 'v.id as id', 'v.year', 'v.expire_date', 'v.allocate_date','v.action',
                              'u.id as user_id', 'u.name as user_name', 'u.grade as grade',
                              'v.allocated_num', 'v.remains_num', 'v.application_num', 'v.approval_num', 'v.completed_num', 'v.expired_num',
                              'u.dept_id'
                              )
                    ->where( 'v.action', '割当' );
        
    //    $query2 = Vacation::where( 'action', '割当' );
    

        if( !empty( $find['user_id'] ) ) {
            $query = $query->where( 'v.user_id', 'in', $find['user_id'] );
            // $query2= $query->user->find[ $find['user_id']];
        }
        
        
        if( !empty( $find['year'] )) {
            $query = $query->where( 'v.year', $find['year'] );
            // $query2= $query2->where( 'year', $find['year']);
        }
        if( !empty( $find['user_name'] )) {
            $query = $query->where( 'u.name', 'like', '%'.$find['user_name'].'%' );   
            // $query2= $query2->user()->where( 'name', 'like', '%'.$find['user_name'].'%');
            
        }
        
        if( !empty( $find['dept_id'] )) {
            $query = $query->where( 'u.dept_id', $find['dept_id'] );
            // $query2= $query2->user()->where( 'dept_id', $find['dept_id']);
            
        }

        if( !empty( $find['pagination'] )) {
            $result = $query->paginate( $find['pagination'] );    
        } else {
            $result = $query->paginate( 20 );
        }
        // dd( $query2->get() );
        //dd( $result );
        return $result;
  
    }

    //　割当年度　選択フォーム用　配列取得関数
    //    
    public static function getArrayForYearSelcetForm() {

        $array = array();
        $results = DB::table('vacations')->select('year')
                                         ->groupBy( 'year' )
                                         ->get();

        $array = [ "" => "-" ];
        foreach( $results as $result ) {
            $array[$result->year] = $result->year;
        }
        return $array;
    }
    
    //  有給休暇申請フォームで使用
    //
    public static function getArrayForSelectOfApplicationForm( $user ) {
        $array = [ "" => "-" ];
        $vacations = $user->valid_paidleave;
        // dd($vacations);
        foreach( $vacations as $v ) {
            #$value = $vacation->year."年度（残".$vacation->remains_num."／".$vacation->allocated_num."）".$vacation->expire_date;
            $value = $v->year."年度分　期限：".$v->expire_date."　（残 ".$v->print_remains_num()."／".$v->print_allocated_num()." ）";
            
            $array[$v->id] = $value;
        }
        return $array;        
    }

    public static function getArrayForSelectOfHoulyPaidleaveApplicationForm( $user ) {
        $array = [ "" => "-" ];
        $vacations = $user->valid_houly_paidleave;
        // dd($vacations);
        foreach( $vacations as $v ) {
            #$value = $vacation->year."年度（残".$vacation->remains_num."／".$vacation->allocated_num."）".$vacation->expire_date;
            $value = $v->year."年度分　期限：".$v->expire_date."　（残 ".$v->print_remains_num()."／".$v->print_allocated_num()." ）";
            
            $array[$v->id] = $value;
        }
        return $array;        
    }


    //////////////////////////////////////////////////////////////////////////
    //
    //  時間有給関連　
    //
    //　時間有給がＯＮかどうか。
    //
    public static function is_valid_hourly_paidleave() {
        // dd( config( 'vacation.config.hourly_paidleave'));
        if( config( 'vacation.config.is_valid_hourly_paidleave' )) { return true; } else { return false; }
    }
    
    //　時間有給のフォームで使う配列を返す
    //
    public static function getArrayForSelectOfHoulyPaidleaveForm() {
        return config( 'vacation.constant.paidleave.hourly');
    }


    //   時間有給関連、ここまで
    //
    //////////////////////////////////////////////////////////////////////////

    //　有給残日数の再計算
    //
    public function recalculate() {
        $this->paidleave_recalculate();
    }

    public function paidleave_recalculate() {
        
        //
        if( $this->type != "有給休暇" and $action == "割当" ) { abort( 'Vacation.paidleave_recalculate:1'); }
        
        $year    = $this->year;
        $user_id = $this->user_id;

        $lists = DB::table( 'vacation_lists as l' )->join( 'vacations as v', 'v.id', '=', 'l.vacation_id' )
                                                   ->selectRaw( 'l.action, sum( l.num ) as num')
                                                   ->where( 'v.user_id', $user_id )
                                                  ->where( 'v.year', $year )
                                                //   ->where( 'v.id', $this->id )
                                                   ->groupBy( 'l.action' )
                                                   ->get()->toArray();
        // dd( $this, $lists);
        $this->remains_num     = 0;
        $this->application_num = 0;
        $this->approval_num  = 0;
        $this->completed_num = 0;
        $this->expired_num   = 0;
        $this->allocated_num = 0;
        foreach( $lists as $row ) {
            $action = $row->action;
            $num    = $row->num;
            // dd( $row );
            // dump(  $action, $num );
            if( $action == "残日数" ) {
                $this->remains_num = $num;
            } elseif( $action == "申請" ) {
                $this->application_num = $num;
            } elseif( $action == "承認" ) {
                $this->approval_num = $num;
            } elseif( $action == "休暇取得完了" ) {
                $this->completed_num = $num;
            } elseif( $action == "期限切れ" ) {
                $this->expired_num = $num;
            } elseif( $action == "割当" ) {
                $this->allocated_num = $num * -1;
            }
        }
        $this->save();
        return $this;
    }
        
    // 休暇申請の処理
    //
    public static function create_application( Application $application ) {
        
        $vacation = new Vacation();

        $type = $application->type;
        if( $type == "有給休暇" ) {
            $paidleave = $application->paidleave;
            $year = $application->allocated_paidleave->year;
        } else {
            $year = null;
        }
        $vacation->user_id          = $application->user_id;
        $vacation->application_id   = $application->id;
        $vacation->action           = "申請";
        $vacation->type             = $type;
        $vacation->year             = $year;
        $vacation->num              = $application->num;
        $vacation->save();
                //  dd( $vacation );
                
        $vacation->create_lists();
        
        if( $type == "有給休暇") {
            $paidleave->paidleave_recalculate();
        }

        return $vacation;
    }

    //　休暇承認の処理
    //
    static public function create_approval( Application $application ) {
        $paidleave = $application->allocated_paidleave;
        $user      = $application->applicant;
        $type      = $application->type;
        $num       = $application->num;
        $year      = optional( $paidleave )->year;

        $vacation  = new Vacation();
        $vacation->user_id = $user->id;
        $vacation->application_id = $application->id;
        $vacation->action = "承認";
        $vacation->type   = $type;
        $vacation->year   = $year;
        $vacation->num    = $application->num;
        $vacation->save();
        // dd( 'aaa', $vacation, $paidleave);
        
        $lists = [  [ 'action' => "承認",  'num' => $num ],
                    [ 'action' => "申請",  'num' => $num * -1 ],
                    ];
        $vacation->lists()->createMany( $lists );
        
        if( $type == "有給休暇" ) { $paidleave->paidleave_recalculate(); }

        return $vacation;
        
    }
    
    //　休暇却下の処理
    //
    static public function create_rejection( Application $application ) {
        $paidleave = $application->allocated_paidleave;
        // dd( $application, $paidleave );
        $user      = $application->applicant;
        $type      = $application->type;
        $num       = $application->num;
        $year      = optional( $paidleave )->year;

        $vacation  = new Vacation();
        $vacation->user_id = $user->id;
        $vacation->application_id = $application->id;
        $vacation->action = "却下";
        $vacation->type   = $type;
        $vacation->year   = $year;
        $vacation->num    = $application->num;
        $vacation->save();
        
        $lists = [  [ 'action' => "残日数",  'num' => $num ],
                    [ 'action' => "申請",    'num' => $num * -1 ],
                    ];
        $vacation->lists()->createMany( $lists );
        
        if( $type == "有給休暇" ) { $paidleave->paidleave_recalculate(); }

        return $vacation;
    }
    
    //　休暇取得完了の処理
    //
    static public function create_completion( Application $application ) {
        $paidleave = $application->allocated_paidleave;
        $user      = $application->applicant;
        $type      = $application->type;
        $num       = $application->num;
        $year      = optional( $paidleave )->year;

        $vacation  = new Vacation();
        $vacation->user_id = $user->id;
        $vacation->application_id = $application->id;
        $vacation->action = "休暇取得完了";
        $vacation->type   = $type;
        $vacation->year   = $year;
        $vacation->num    = $application->num;
        $vacation->save();
        
        $lists = [  [ 'action' => "休暇取得完了",  'num' => $num ],
                    [ 'action' => "承認",  'num' => $num * -1 ],
                    ];
        $vacation->lists()->createMany( $lists );
        
        if( $type == "有給休暇" ) { $paidleave->paidleave_recalculate(); }

        return $vacation;
    }
    
    //　休暇取得完了済み休暇の削除（管理者のみ）の処理
    //
    public static function create_delete_complete( Application $app ) {

        $paidleave = $app->allocated_paidleave;
        $user_id   = $app->user_id;
        $status    = $app->status;
        $type      = $app->type;
        $num       = $app->num;

        $action = "残日数";
        $sub = "休暇取得完了";

        $vacation = new Vacation;
        $vacation->user_id = $user_id;
        $vacation->application_id = $app->id;
        $vacation->action = "取り下げ";
        $vacation->type   = $type;
        $vacation->year   = optional( $paidleave )->year;
        $vacation->num    = $num;
        $vacation->save();

        $lists = [  [ 'action' => $action,  'num' => $num ],
                    [ 'action' => $sub,     'num' => $num * -1 ],
                    ];
        $vacation->lists()->createMany( $lists );

        // 有給休暇残日数の更新
        //
        if( $type == "有給休暇" ) { $paidleave->paidleave_recalculate(); }

        return $vacation;
        
    }
    
    // 休暇申請の取り下げ（キャンセル）の処理
    //
    public static function create_cancellation( Application $app ) {
        
        $paidleave = $app->allocated_paidleave;
        $user_id   = $app->user_id;
        $status    = $app->status;
        $type      = $app->type;
        $num       = $app->num;

        $action = "残日数";
        $sub = "申請";

        $vacation = new Vacation;
        $vacation->user_id = $user_id;
        $vacation->application_id = $app->id;
        $vacation->action = "取り下げ";
        $vacation->type   = $type;
        $vacation->year   = optional( $paidleave )->year;
        $vacation->num    = $num;
        $vacation->save();

        $lists = [  [ 'action' => $action,  'num' => $num ],
                    [ 'action' => $sub,     'num' => $num * -1 ],
                    ];
        $vacation->lists()->createMany( $lists );

        // 有給休暇残日数の更新
        //
        if( $type == "有給休暇" ) { $paidleave->paidleave_recalculate(); }

        return $vacation;
    }
    
    // 休暇申請の取り下げ（キャンセル）の処理
    //
    public static function create_approved_application_cancellation( Application $app ) {
        
        $paidleave = $app->allocated_paidleave;
        $user_id   = $app->user_id;
        $type      = $app->type;
        $num       = $app->num;

        $action = "残日数";
        $sub = "承認";

        $vacation = new Vacation;
        $vacation->user_id = $user_id;
        $vacation->application_id = $app->id;
        $vacation->action = "取り下げ";
        $vacation->type   = $type;
        $vacation->year   = optional( $paidleave )->year;
        $vacation->num    = $app->num;
        $vacation->save();

        $lists = [  [ 'action' => $action,  'num' => $num ],
                    [ 'action' => $sub,     'num' => $num * -1 ],
                    ];
        $vacation->lists()->createMany( $lists );

        // 有給休暇残日数の更新
        //
        if( $type == "有給休暇" ) { $paidleave->paidleave_recalculate(); }

        return $vacation;

    }
    

    
    //　有給休暇期限切れ
    //
    public function expire_paidleave() {
        
        $num = $this->remains_num;
        $this->expired_num = $num;
        $this->remains_num = 0;
        $this->done_expired = true;
        $this->save();
        
        $lists = [  [ 'action' => '期限切れ',  'num' => $num ],
                    [ 'action' => '残日数',    'num' => $num * -1 ],
                    ];
        $this->lists()->createMany( $lists );
        return $this;
    }

    //　有給休暇期限切れ後に申請をキャンセルしたものに対する処理
    //
    public function check_expired_paidleave() {
        
        $num = $this->remains_num;
        $this->expired_num = $this->expired_num + $num;
        $this->remains_num = 0;
        $this->done_expired = true;
        $this->save();
        
        $lists = [  [ 'action' => '期限切れ',  'num' => $num ],
                    [ 'action' => '残日数',    'num' => $num * -1 ],
                    ];
        $this->lists()->createMany( $lists );
        return $this;
    }

    //　休暇リストの作成
    // 
    public function create_lists() {
    
        $action = $this->action;
        $num    = $this->num;
        
        if( $action == "申請" ) {
            $sub = "残日数";
        } elseif( $action == "承認" ) {
            $sub = "申請";
        } elseif( $action == "却下" ) {
            $sub = "申請";
        } elseif( $action == "休暇取得完了" ) {
            $sub = "承認";
        
        } elseif( $action == "割当" ) {
            $action = "残日数";
            $sub    = "割当";

        } elseif( $action == "管理者による削除" ) {
            $action = "残日数";
            $sub    = "休暇取得完了";
            
        } else {
            abort( 'Vacaion.create_list');
        }
        
        $lists = [  [ 'action' => $action,  'num' => $num ],
                    [ 'action' => $sub,     'num' => $num * -1 ],
                    ];
        
        $this->vacation_lists()->createMany( $lists );
        
        return $this;
        
    }
    
    //　表示用関数
    //
    public function print_remains_num() {
        // dump( $this->remains_num );
        return self::pnum( $this->remains_num );
    }
    public function print_digest_num() {
        // dump( $this->remains_num );
        $num = $this->application_num + $this->approval_num + $this->completed_num;
        return self::pnum( $num );
    }
    public function print_allocated_num() {
        return self::pnum( $this->allocated_num );
    }
    public function print_application_num() {
        return self::pnum( $this->application_num );
    }
    public function print_approval_num() {
        return self::pnum( $this->approval_num );
    }
    public function print_completed_num() {
        return self::pnum( $this->completed_num );
    }
    public function print_expired_num() {
        return self::pnum( $this->expired_num );
    }
    static public function pnum( $num ) {
        // dump( self::get_houry_paid_leave_unit(), self::HOURLY_PAID_LEAVE_UNIT );
        $day   = floor( $num );
        $h_num = $num - $day;
        $hour  = floor( $h_num / 0.125 );
        $m_num = $h_num - $hour * 0.125;
        $minites= $m_num / 0.03125 * 15;

        // $hour = $hour / self::get_houry_paid_leave_unit();
        // dump( "$num, $h_num, $m_num, $day, $hour, $minites" );
        if( (int)$day > 0     ) { $day .= "日";     } else { $day     = null; }
        if( (int)$hour > 0    ) { $hour .= "時間";  } else { $hour    = null; } 
        if( (int)$minites > 0 ) { $minites .= "分"; } else { $minites = null; }
        // dump( "$day,$hour,$minites" );
        $return = $day.$hour.$minites;
        
        return $return;
    }
    
    public static function get_houry_paid_leave_unit() {
        return self::HOURLY_PAID_LEAVE_UNIT;
    }
    
    
    
}
