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

    //  CSV出力ルート
    //
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
    //
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
    //
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
        } else {
            $query = $query->whereNotIn( 'a.status', [ '却下', '取り下げ'] );
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

    ////////////////////////////////////////////////////////////////////////////////////
    //
    //  有給休暇残日数の検索コントローラー　＆　検索関数
    //
    ////////////////////////////////////////////////////////////////////////////////////
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
    
    //　ＣＳＶ出力
    //
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
            // dump( $vacation );
        } else {
            $find = [];
            $vacation = [];
        }

        // CSV出力
        // 
        $lists[0] = [ '社員番号', '部署', '役職', '名前', '有給日数', ];
        $file_name = "有給休暇残日数.csv";

        foreach( $vacation as $v ) {
            array_push( $lists, [ $v->code, $v->dept_name, $v->grade, $v->user_name, $v->num ]);
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
