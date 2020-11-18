<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;


use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;
use App\Http\Requests\Vacation\CommonRequest;
use App\Http\Helpers\BackButton;
use App\Http\Helpers\OutputCSV;

class CommonController extends Controller
{
    // 休暇取得状況　画面
    //
    public function vacation( CommonRequest $request ) {

        if( isset( $request->find )) {
            $find = $request->input( 'find' );
        } else {
            $find = array();
        }

        if( !isset( $find['start_date']) or !isset( $find['end_date'] ) ) {
            Session::flash( 'flash_message', '休暇年月日を入力してください' );
            $vacation = null;
            
        } else {
            
            // 検索クエリー
            //
            if( empty( $find['no_paid_leave'] )) {
                
                // 有給休暇消化日数・特別休暇取得日数の検索
                //
                $query = DB::table( 'applications as a' )
                            ->join( 'users as u', 'u.id', 'a.user_id' )
                            ->join( 'depts as d', 'd.id', 'u.dept_id' )
                            ->selectRaw( 'u.id, sum( a.num ) as num, u.name , a.type, d.name as dept, u.grade' )
                            ->groupBy( 'u.id', 'a.type' );
            
                //　従業員名で検索
                //
                if( !empty( $find['name'])) {
                    $query = $query->where( 'u.name', 'like', '%'.$find['name'].'%' );  
                }
                if( isset( $find['user_id'])) {
                    $query = $query->where( 'u.id', $find['user_id'] );  
                }
                
                //　有給休暇・特別休暇の検索
                //
                if( empty( $find['type'] )) {
                    $find['type'] = "有給休暇";
                }
                $query = $query->where( 'a.type', $find['type'] );

                //　ステータス
                //
                if( isset( $find['status']) ) {
                    $query = $query->whereIn( 'a.status', $find['status'] );
                }  else {
                    $query = $query->whereNotIn( 'a.status', ['却下', '取り下げ'] );
                    $find['status'] = array();
                }
                
                // 休暇日を検索
                //
                $query = $query->where( 'a.start_date', '>=', $find['start_date'] )
                               ->where( 'a.end_date'  , '<=', $find['end_date']   );
    
            } else {
                //
                //　有給休暇未取得者の検索
                //
                
                // 　サブクエリー（有給取得者を検索）
                //
                $result = DB::table( 'applications as a' )
                                ->join( 'users as u', 'u.id', 'a.user_id' )
                                ->select( 'a.user_id' )
                                ->where( 'type', '有給休暇' )
                                ->where( 'start_date', '>=', $find['start_date'] )
                                ->where( 'end_date',   '<=', $find['end_date'] )
                                ->groupBy( 'u.id' )
                                ->get();
        
                #dd( $subresult);
                $users = array();        
                foreach( $result as $r ) {
                    array_push( $users, $r->user_id );
                    
                }
           
                $query = DB::table( 'users as u' )
                            ->join( 'depts as d', 'd.id', 'u.dept_id' )
                            ->select( 'u.id', 'u.code as code', 'u.id as num', 'u.name' , 'u.id as type', 'd.name as dept', 'u.grade' )
                            ->whereNotIn( 'u.id', $users );
                if( isset( $find['no_officer'] )) {
             
                    $query = $query->whereNotIn( 'u.grade', config('constant.user.grade.officer') );
                }

            }

            // 部署名で検索
            //
            if( !empty( $find['dept_id']) ) {
                $query = $query->where( 'u.dept_id', $find['dept_id'] );
            }
            // 役職で検索
            //
            if( !empty( $find['grade']) ) {
                $query = $query->where( 'u.grade', $find['grade'] );
            }

            $vacation = $query->paginate( $find['pagination'] );

            dd( $vacation );
        }

        // 戻るボタンの設定
        //
        BackButton::setHere( $request );

            // dd( $find );
            // dd( $vacation );
        return View( 'vacation.common.vacation' )->with( 'find', $find )
                                                 ->with( 'vacation', $vacation );
    }
    
    //　休暇一覧
    //
    public function vindex( CommonRequest $request ) {
        //　検索初期値の設定
        //
        if( isset( $request->find )) {
            $find = $request->input( 'find' );
        } else {
            $find['show_item'] = [ '役職', '部署', '休暇種別', 'ステータス' ];
            $find['status']    =  [ '承認待ち', '承認', '休暇取得完了' ];
        }
        //　検索実行
        //
        if( !empty( $find['start_date'] ) and !empty( $find['end_date'] ) ) {
            $vacation = CommonController::getVacations( $find );
        } else {
            $vacation = null;
            $query = null;
        }
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        return View( 'vacation.common.vindex' )->with( 'find', $find )
                                               ->with( 'vacation', $vacation );
    }
    
    //　月表示
    //
    public function monthly( CommonRequest $request ) {
        //　検索初期値の設定
        //
        // dump( $request->all() );
        if( isset( $request->find )) {
            $find = $request->input( 'find' );
        } else {
            $find['show'] = [ '部署名', '休暇種別' ,'ステータス' ];
            
            $find['status']    =  [ '承認待ち', '承認', '休暇取得完了' ];
        }
        // dd( $find );
        $base_date = ( empty( $request->base_date )) ? Carbon::today() : new Carbon( $request->base_date );

        $dates = self::getMonthlyCalendarDates( $base_date );
        $find['start_date'] = Arr::first( $dates )->format( 'Y-m-d' );
        $find['end_date']   = Arr::last( $dates  )->format( 'Y-m-d' );
        
        //　検索実行
        //
        if( !isset( optional( $request )->root_route ) and  !empty( $find['start_date'] ) and !empty( $find['end_date'] ) ) {
            $vacations = CommonController::getVacations2( $find );
        } else {
            $vacations = [];
        }
        // dd( $vacations );
        $vacation_ids = self::get_array_dates_vacations_id( $vacations );
        
        // dd( $vacations, $vacation_ids );
        
        BackButton::setHere( $request );
        return View( 'vacation.common.monthly' )->with( 'find', $find )
                                                ->with( 'dates', $dates )
                                                ->with( 'vacation_ids', $vacation_ids )
                                                ->with( 'request', $request )
                                                ->with( 'base_date', $base_date )
                                                ->with( 'vacations', $vacations );
    }
    
    //　View内で使う関数(表示月の切替ボタン)
    //
    static public function get_argv_for_forms( Request $request, $base_date = null ) {
        
        $argvs = [ 'find' => $request->find ];

        if( is_null( $base_date )) {
            $argvs['base_date'] = Carbon::now()->format( 'Y-m-d' );
        } else {
            $argvs['base_date'] = $base_date;
        }
        // dump( $argvs );
        return $argvs;
    }
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //    
    //　有給休暇申請のコレクションから、キーが日付、値がID、の配列を作る（カレンダー表示で使うためのデータ）
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    static public function get_array_dates_vacations_id( $vacations ) {
        
        $dates = [];
        $i = 1;
        foreach( $vacations as $vacation ) {
            $start_date = Carbon::createFromFormat( 'Y-m-d', $vacation->start_date );
            $end_date   = Carbon::createFromFormat( 'Y-m-d', $vacation->end_date   );
            
            for( $date = $start_date->copy(); $date->lte( $end_date ); $date->addDay() ) {
                
                $d = $date->format( 'Y-m-d' );
                if( array_key_exists( $d, $dates )) {
                    array_push( $dates[$d], $vacation->id );
                } else {
                    $dates[$d] = [ $vacation->id ];
                }
                // dump( 'ID:'.$vacation->id."  date:".$date->format( 'Y-m-d')."   start:".$start_date->format( 'Y-m-d')."   end_date:".$end_date->format( 'Y-m-d') );
                if( $i >= 100 ) { break; }
                $i++;
            }
            if( $i >= 100 ) { break; }

        }
        // dump( $dates );
        return $dates;        
        
    }
    
    //　カレンダー表示用の日付データの生成
    static public function getMonthlyCalendarDates( Carbon $base_date ) {
   
        $date = new Carbon( "{$base_date->year}-{$base_date->month}-01" );
        
        // MEMO: 月末が日曜日の場合の挙動を修正
        $addDay = ( $date->copy()->endOfMonth()->isSunday()) ? 7 : 0;
        
        // カレンダーを四角形にするため、前月となる左上の隙間用のデータを入れるためずらす
        $date->subDay( $date->dayOfWeek );

        // 同上。右下の隙間のための計算。
        // MEMO: 変数に修正
        // $count = 31 + $date->dayOfWeek;
        $count = 31 + $addDay + $date->dayOfWeek;
        $count = ceil($count / 7) * 7;
        $dates = [];

        for ($i = 0; $i < $count; $i++, $date->addDay()) {
            // copyしないと全部同じオブジェクトを入れてしまうことになる
            $dates[] = $date->copy();
        }
        return $dates;
        
    }

    //  CSV出力ルート
    public function csv( CommonRequest $request ) {
        // 有給休暇消化日数・特別休暇取得日数の検索
        //
            
        if( isset( $request->find )) {
            $find = $request->input( 'find' );
            $find['pagination'] = null;
        } else {
            return redirect()->route( 'vacation.common.vindex' );
        }
        $vacation = CommonController::getVacations( $find );

        $lists[0] = [ '社員番号', '名前', '部署', '役職', '休暇種別', '休暇開始日', '休暇終了日', '休暇日数', '承認ステータス', '理由'];
        foreach( $vacation as $v ) {
            array_push( $lists, [ $v->code, $v->name, $v->dept, $v->grade, $v->type, $v->start_date, $v->end_date, $v->num, $v->status, $v->reason ]);
        }
        return CommonController::outputCSV( $lists, "休暇リスト.csv" );     
     }
 
    // CSV出力
    static public function outputCSV( $lists, $file_name = "csv_file.csv" ) {
        
        $file = fopen( 'php://memory', 'w+' );
        foreach( $lists as $row ) {
            fputcsv( $file, $row );
        }
        rewind( $file );
        $csv = str_replace( PHP_EOL, "\r\n", stream_get_contents( $file ));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $headers = array(
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$file_name.'"',
            );
        fclose( $file );
        return Response::make( $csv, 200, $headers );
    }
    
    //  休暇のDB配列を返す
    static public function getVacations( $find ) {
        
        if( empty( $find['start_date'] ) or empty( $find['end_date'] ) ) {
            return null;
        }

        // 有給休暇消化日数・特別休暇取得日数の検索
        //
        $query = DB::table( 'applications as a' )
                    ->join( 'users as u', 'u.id', 'a.user_id' )
                    ->join( 'depts as d', 'd.id', 'u.dept_id' )
                    ->selectRaw( 'a.id, u.id as user_id, u.code, a.id as application_id, u.name , d.name as dept, 
                                  u.grade, a.type, a.start_date, a.end_date, a.num, a.reason, a.status' );

        //　従業員名で検索  
        //
        if( !empty( $find['name'])) {
            $query = $query->where( 'u.name', 'like', '%'.$find['name'].'%' );  
        }
        if( isset( $find['user_id'])) {
            $query = $query->where( 'u.id', $find['user_id'] );
                
        }
        //　有給休暇・特別休暇の検索
        //
        if( ! empty( $find['type'] )) {
            $query = $query->where( 'a.type', $find['type'] );
        }

        //　ステータス
        //
        if( is_array( optional( $find )['status'])) {
            $query = $query->whereIn( 'a.status', $find['status'] );
        // } else {
        //     $query = $query->whereNotIn( 'a.status', [ '却下', '取り下げ'] );
        }

        // 休暇日を検索
        //
        $query = $query->whereRaw( "(( a.start_date >= '$find[start_date]' and a.start_date <= '$find[end_date]' ) or 
                                     ( a.end_date   >= '$find[start_date]' and a.end_date   <= '$find[end_date]' ) or
                                     ( a.start_date <  '$find[start_date]' and a.end_date   >  '$find[end_date]' ))
                                     ");
    

        // 部署名で検索
        //
        if( !empty( $find['dept_id']) ) {
                $query = $query->where( 'u.dept_id', $find['dept_id'] );
        }
        // 役職で検索
        //
        if( !empty( $find['grade']) ) {
            $query = $query->where( 'u.grade', $find['grade'] );
        }
        //　検索実行
        //
        if( isset( $find['pagination'] )) {     
            $vacation = $query->paginate( $find['pagination'] );
        } else {
            $vacation = $query->get();
        }

        // 検索期間外の休暇データの修正
        //
        $start_date = Carbon::parse( $find['start_date']);
        $end_date   = Carbon::parse( $find['end_date']);
        #dump( $start_date, $end_date );
            
        foreach( $vacation as $i => $v ) {
            $change = false;
            #dump( $v );
            $v_start_date = Carbon::parse( $v->start_date );
            $v_end_date   = Carbon::parse( $v->end_date );
            #dump( "aaa", $v_start_date->toDateString(), $v_end_date->toDateString() );
            if( $v_start_date < $start_date ) { $v_start_date = $start_date; $change = true; }
            if( $v_end_date   > $end_date   ) { $v_end_date = $end_date;     $change = true; }
            #dump( "bbb", $v_start_date->toDateString(), $v_end_date->toDateString(), $v_end_date->diffInDays( $v_start_date )+1);
            if( $change ) {
                // dd( $vacation[$i] );
                $vacation[$i]->start_date = $v_start_date->toDateString(); 
                $vacation[$i]->end_date   = $v_end_date->toDateString();
                $vacation[$i]->num        = $v_end_date->diffInDays( $v_start_date )+1;
            }

        }
        return $vacation;
    }
    
        
    //  休暇のEloquentsを返す
    static public function getVacations2( $find ) {
        
        if( empty( $find['start_date'] ) or empty( $find['end_date'] ) ) {
            return null;
        }

        // 休暇日を検索
        //
        $query = Application::where( function( $sub_query ) use ( $find ) {
                $sub_query->where( function( $query ) use ( $find ) {
                        $query->where( 'start_date', '>=', $find['start_date'] )
                              ->where( 'start_date', '<=', $find['end_date']   );
                });
                $sub_query->orWhere( function( $query) use ( $find ) {
                        $query->where( 'end_date', '>=', $find['start_date'] )
                              ->where( 'end_date', '<=', $find['end_date']   );
                });
                $sub_query->orWhere( function( $query) use ( $find ) {
                        $query->where( 'start_date', '<', $find['start_date'] )
                              ->where( 'end_date',   '>', $find['end_date']   );
                });
        });

        //　有給休暇・特別休暇の検索
        //
        if( ! empty( $find['type'] )) {
            $query = $query->where( 'type', $find['type'] );
        }
        //　ステータス
        //
        if( is_array( optional( $find )['status'])) {
            $query = $query->whereIn( 'status', $find['status'] );
        }
        //　従業員ＩＤ
        //
        if( isset( $find['user_id'])) {
            $query = $query->where( 'user_id', $find['user_id'] );
        }
        
        // 従業員名・部署・役職で検索
        //
        if( !empty( $find['name']) or !empty( $find['dept_id']) or !empty( $find['grade'] )) {
            $query = $query->whereHas( 'user', function( $sub_query ) use ( $find ) {
                if( !empty( $find['name']) ) {
                    $sub_query->where( 'name', 'like', '%'.$find['name'].'%' );
                }
                if( !empty( $find['dept_id']) ) {
                    $sub_query->where( 'dept_id', $find['dept_id']);
                }
                if( !empty( $find['grade']) ) {
                    $sub_query->where( 'grade', $find['grade']);
                }                
            });          
        }

        //　検索実行
        //
        $vacations = $query->with( 'user', 'user.department' )->get();
        // dump( $vacations );
        return $vacations;
    }
    
    
    ////////////////////////////////////////////////////////////////////////////////////
    //
    //  有給休暇　未取得者の検索コントローラー
    //
    ////////////////////////////////////////////////////////////////////////////////////
    public function noVacation( Request $request ) {
        
        //　検索初期値の設定
        //
        if( isset( $request->find )) {
            $find = $request->input( 'find' );
        } else {
            $find['no_paid_leave'] = true;
        }
        $find['type'] = "有給休暇";

        //　有給取得者の検索
        //
        if( !empty( $find['start_date'] ) and !empty( $find['end_date'] ) ) {
            
            $query = $find;
            $users = CommonController::getNoVacationUsers( $query );
            // dump( $users);
        } else {
            $users = null;
        }
    
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        return View( 'vacation.common.no_vacation' )->with( 'find', $find )
                                                    ->with( 'users', $users );
    }

    //  CSV出力
    //
    public function noVacationCSV( Request $request ) {

        if( isset( $request->find )) {
            $find = $request->input( 'find' );
        } else {
           abort( 'CommonController::noVacationCSV : Error 1');
        }
        if( empty( $find['start_date'] ) or empty( $find['end_date'] )) { abort( 'CommonController::noVacationCSV : Error 2'); }
        
        // dump( $find );
        $find['type'] = "有給休暇";
        $fnid['pagination'] = null;
        Arr::forget( $find, 'pagination');

        $users = CommonController::getNoVacationUsers( $find );
        // dump( $users );

        $lists[0] = [ '社員番号', '名前', '部署', '役職', '有給取得・未取得', '検索期間', '検索期間' ];
        if( optional( $find )['no_paid_leave'] ) {
            $no_paid_leave = "期間中　有給休暇未取得者";
            $file_name = "有給未取得者リスト.csv";
        } else {
            $no_paid_leave = "期間中　有給休暇取得者";
            $file_name = "有給取得者リスト.csv";

        }
        
        foreach( $users as $user ) {
            array_push( $lists, [ $user->code, $user->name, $user->department->name, $user->grade, $no_paid_leave, $find['start_date'], $find['end_date']  ]);
        }
        return CommonController::outputCSV( $lists, $file_name );  
        
        
    }

    //　有給休暇未取得・取得者を検索する関数
    //
    static public function getNoVacationUsers( $query ) {
        
        // dump( request()->all(), $query );
        if( empty( $query['start_date'] ) or empty( $query['end_date'] )) {
            return null;
        } 

        //　期間中の有給休暇を検索
        //
        $query2 = $query;
        $query2['pagination'] = null;
        $vacation = CommonController::getVacations( $query2 );
        
        //  有給取得者IDを抽出
        //
        $except_users = [];
        foreach( $vacation as $v ) { array_push( $except_users, $v->user_id ); }
        $users = array_unique( $except_users );
        // dump( $except_users );
        
        //　有給未取得者　or 有給取得者を検索
        //
        if( optional( $query )['no_paid_leave'] ) {
            $query['except_user_id'] = $except_users;
        } else {
            $query['include_user_id'] = $except_users;
        }
        $users = User::getUserList( $query );

        return $users;
    }

    //  有給休暇残日数の検索コントローラー　＆　検索関数
    //
    public function howManyDaysLeftForPaidleave( Request $request ) {

        //　検索初期値の設定
        //
        // dump( $request->all() );
        if( isset( $request->find )) {
            
            // 休暇を検索
            //
            $find = $request->input( 'find' );
            $vacation = CommonController::get_how_many_days_left_for_paidleave( $find );
            // dump( $vacation );

        } else {
            $find = [];
            $vacation = [];
        }
        
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        return View( 'vacation.common.how_many_left' )->with( 'find', $find )
                                                      ->with( 'vacation', $vacation );
    }
    
    //  有給休暇取得日数検索　
    //
    public function howManyDaysGetForPaidleave( Request $request ) {

        //　検索初期値の設定
        //
        // dump( $request->all() );
        if( isset( $request->find )) {
            // 休暇を検索
            //
            $find  = $request->input( 'find' );
            $users = CommonController::how_many_days_get_for_paidleave( $find );
            // dump( $vacation );
        } else {
            $find  = [];
            $users = [];
        }
        // dump( $users );
        
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        return View( 'vacation.common.how_many_days_get_for_paidleave' )->with( 'find', $find )
                                                                        ->with( 'users', $users );
    }
    
    public function howManyDaysGetForPaidleaveCSV( Request $request ) {

        //　検索初期値の設定
        //
        // dump( $request->all() );
        if( isset( $request->find )) {
            // 休暇を検索
            //
            $find  = $request->input( 'find' );
            $lists = CommonController::how_many_days_get_for_paidleave( $find );
            // dump( $vacation );
            foreach( $lists as $id => $user ) {
                $users[$id]['name'] = $user['name']; 
                $users[$id]['code'] = $user['code']; 
                $users[$id]['grade'] = $user['grade']; 
                $users[$id]['dept'] = $user['dept_name']; 
                $users[$id]['pnum'] = Vacation::pnum( $user['num'] ); 
                $users[$id]['num'] = $user['num']; 
            }
            
        } else {
            $users = [];
        }
        // dump( $users );
        
        //　CSV出力
        //
        $options['file_name'] = '期間中有給取得日数.csv';
        $options['column_name'] = [ '名前','社員番号', '役職', '部署名', '取得日数', '取得数'];
        $options['lists'] = $users;
        return OutputCSV::input_array( $options );
        
    }
    
    public static function how_many_days_get_for_paidleave( $find ) {
        if( isset( $find )) {
            
            // 社員ＩＤを検索
            //
            $query = $find;
            $query['pagination'] = null;
            $users = User::getUserList( $query );
            $user_ids = [];
            foreach( $users as $user ) {
                array_push( $user_ids, $user->id );    
            }
            // dump( $user_ids);
            
            //　検索した社員の有給残日数を検索
            //
            if( count( $user_ids )) {
                $action = [ '承認待ち', '承認', '休暇取得完了'];
                $vacations =DB::table( 'applications as a' )->join( 'users as u', 'a.user_id', '=', 'u.id' )
                                                            ->join( 'depts as d', 'u.dept_id', '=', 'd.id' )
                                                            ->selectRaw( 'a.type,
                                                                          a.start_date,
                                                                          a.end_date,
                                                                          u.id as id,
                                                                          u.name as name, 
                                                                          u.code as code, 
                                                                          u.grade as grade, 
                                                                          d.name as dept_name, 
                                                                          sum( a.num ) as num' 
                                                                         )
                                                         ->whereRaw( "(( a.start_date >= '$find[start_date]' and a.start_date <= '$find[end_date]' ) or 
                                                                       ( a.end_date   >= '$find[start_date]' and a.end_date   <= '$find[end_date]' ) or
                                                                       ( a.start_date <  '$find[start_date]' and a.end_date   >  '$find[end_date]' ))
                                                                       ")
                                                         ->whereIn( 'a.user_id', $user_ids )
                                                         ->where( 'a.type', '有給休暇' )
                                                         ->whereIn( 'a.status', $action )
                                                         ->groupBy( 'a.type', 'u.id', 'a.start_date', 'a.end_date' )
                                                         ->get();
                                                         
                
                $return = [];
                $f_start_date = Carbon::parse( $find['start_date']);
                $f_end_date   = Carbon::parse( $find['end_date']);
                foreach( $vacations as $i => $v ) {
                    $change = false;
                    
                    $return[$v->id]['id']       = $v->id;
                    $return[$v->id]['name']     = $v->name;
                    $return[$v->id]['code']     = $v->code;
                    $return[$v->id]['grade']    = $v->grade;
                    $return[$v->id]['dept_name']= $v->dept_name;

                    if( $v->num >= 1 ) {
                        $v_start_date = Carbon::parse( $v->start_date );
                        $v_end_date   = Carbon::parse( $v->end_date );
                        #dump( "aaa", $v_start_date->toDateString(), $v_end_date->toDateString() );
                        if( $v_start_date < $f_start_date ) { $v_start_date = $f_start_date; $change = true; }
                        if( $v_end_date   > $f_end_date   ) { $v_end_date   = $f_end_date;   $change = true; }
                    }
                    if( $change ) {
                        $vacations[$i]->start_date = $v_start_date->toDateString(); 
                        $vacations[$i]->end_date   = $v_end_date->toDateString();
                        $vacations[$i]->num       = $v_end_date->diffInDays( $v_start_date )+1;
                        $return[$v->id]['num'] = optional( $return[$v->id] )['num'] + $vacations[$i]->num;
                    } else {
                        $return[$v->id]['num'] = optional( $return[$v->id] )['num'] + $v->num;
                    }
                }
                
                //　有給取得日数以上の人を排除
                //
                if( $find['num'] >=1 ) {
                    $except = [];
                    foreach( $return as $i => $r ) {
                        if( $r['num'] > $find['num']) {
                            $except[$i] = $i;
                        }                        
                    }
                    // dump( $except );
                    if( count( $except)) { $return = Arr::except( $return, $except ); }
                }
                
            } else {
                $vacations = [];
                $return = [];
            }
        }
        // dump( $return );
        // return $vacations;
        return $return;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    //　ＣＳＶ出力
    //
    ////////////////////////////////////////////////////////////////////////////////////
    public function howManyDaysCSV( Request $request ) {

        //　検索初期値の設定
        //
        // dump( $request->all() );
        if( isset( $request->find )) {

            // 休暇を検索
            //
            $find = $request->input( 'find' );
            $find['pagination'] = 10000000;
            $vacation = CommonController::get_how_many_days_left_for_paidleave( $find );

        } else {
            $find = [];
            $vacation = [];
        }

        // CSV出力
        // 
        $lists[0] = [ '社員番号', '部署', '役職', '名前', '有給残日数','有給残数' ];
        $file_name = "有給休暇残日数.csv";

        foreach( $vacation as $v ) {
            array_push( $lists, [ $v->code, $v->dept_name, $v->grade, $v->user_name, Vacation::pnum( $v->num ), $v->num ]);
        }
        return CommonController::outputCSV( $lists, $file_name );  
    }
    
    //　有給残日数を検索して、配列で出力
    //
    static public function get_how_many_days_left_for_paidleave( $find ) {
        
        if( isset( $find )) {
            
            // 社員ＩＤを検索
            //
            $query = $find;
            $query['pagination'] = null;
            $users = User::getUserList( $query );
            $user_ids = [];
            foreach( $users as $user ) {
                array_push( $user_ids, $user->id );    
            }
            // dump( $user_ids);
            
            //　検索した社員の有給残日数を検索
            //
            if( count( $user_ids )) {
            $vacation = DB::table( 'vacations as v' )->join( 'vacation_lists as vl', 'vl.vacation_id', '=', 'v.id' )
                                                    ->join( 'users as u', 'v.user_id', '=', 'u.id' )
                                                    ->join( 'depts as d', 'u.dept_id', '=', 'd.id' )
                                                    ->selectRaw( 'v.type, 
                                                                  u.id as user_id,
                                                                  u.name as user_name, 
                                                                  u.code as code, 
                                                                  u.grade as grade, 
                                                                  d.name as dept_name, 
                                                                  vl.action, 
                                                                  sum( vl.num) as num' 
                                                                    )
                                                    ->whereIn( 'v.user_id', $user_ids )
                                                    ->where( 'v.type', '有給休暇' )
                                                    ->where( 'vl.action', '残日数')
                                                    ->groupBy( 'v.type', 'u.id', 'vl.action')
                                                    ->paginate( $find['pagination']);
                // dump( $vacation );
            } else {
                $vacation = [];
            }
        }
        return $vacation;
    }
}
