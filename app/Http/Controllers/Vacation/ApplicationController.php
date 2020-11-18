<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\VacationList;
use App\Models\Vacation\ApprovalMaster;
use App\Models\Vacation\ApprovalMasterAllocate;

use App\Http\Requests\Vacation\ApplicationRequest;

use App\Http\Controllers\Vacation\Notifications\NoticeApplication;
use App\Http\Controllers\Vacation\Notifications\RemindApplicationProcessed;
use App\Http\Controllers\Vacation\Notifications\RemindApproval;

use App\Http\Helpers\BackButton;



class ApplicationController extends Controller
{
 
    //  休暇申請画面
    //
    public function create( ) {
        
        $user  = User::find( Auth::id());
        //  dd( $user );
        $depts     = Dept::getArrayforSelect();

        //　承認マスターを検索
        //
        // $master_id = ApprovalMasterAllocate::where( 'user_id', '=', $user->id )->get( 'approval_master_id' )->first()->approval_master_id;
        $master = ApprovalMasterAllocate::where( 'user_id', '=', $user->id )->get( 'approval_master_id' )->first();
        // dd( $master_id );
        
        if( isset( $master )) {
            $master_id = $master->approval_master_id;   
        } else {
            $master_id = 0;
        }
        
        $default_approvers = DB::table( 'users as user')
                           ->join( 'approval_master_lists as list', 'list.user_id', '=', 'user.id' )
                            ->join( 'approval_masters as master',    'master.id',   '=', 'list.approval_master_id' )
                            ->select( 'user.id as user_id', 'user.dept_id as dept_id' )
                            ->where( 'master.id', '=', $master_id )
                            ->get();
    
        //  dd( $default_approvers );

        if( preg_match( '/application\.create$/', Route::currentRouteName() )) {    
            $view_name = 'vacation.application.create';
        } elseif( preg_match( '/application\.create_hourly$/', Route::currentRouteName() )) {
            $view_name = 'vacation.application.create_houly';
        } else { 
            abort( 403, 'ApplicationController.create:未定義ルート');
        }
        $today = Carbon::today()->format( 'Y-m-d' );
        // dump( $today);
        BackButton::stackHere( request() );
        
        return view( $view_name )->with( 'user',      $user )
                                 ->with( 'depts',     $depts )
                                 ->with( 'today',     $today )
                                 ->with( 'default_approvers', $default_approvers );
    }

    //　時間有給申請
    //
    public function create_hourly() {
        return $this->create();
    }

    //　休暇申請登録
    //
    public function store( ApplicationRequest $request ) {

        // 承認者
        //
        $user = User::find( auth('user')->id() );
        // dd( $user );
        $approvers = $request->approvers;
        
        $application = DB::transaction( function() use ( $request, $user, $approvers ) {

            //　休暇申請レコードの登録
            //
            $application = Application::create( $request, $user );            

            //　休暇DBの登録
            //
            $vacation = Vacation::create_application( $application );
            
            //　承認者の登録
            //
            $approvers = $request->approvers;
            Approval::create( $application, $approvers );

            return $application;
        });
        
        
        // 　承認者へ通知
        //        
        $approvers = $application->approvers;
        $notice = new NoticeApplication( $application );
        // dd( $approvers, $notice );
        foreach( $approvers as $user ) {
            if( $user->hasEmail() ) {
                $user->notify( $notice );
            }
            
        }

        //  完了画面へ　
        //
        #dd( $application );
        Session::flash( 'flash_message', "休暇申請の登録を完了しました。" );
        Session::flash( 'previous_route', Route::currentRouteName() );
        
        // 戻るボタン対策
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route( 'vacation.application.show' ,['application'=> $application ] );
                  
    }
    
    //　時間有給の登録
    //
    public function store_hourly( ApplicationRequest $request ) {

        // 承認者
        //
        $user = User::find( auth('user')->id() );
        // dd( $user );
        $approvers = $request->approvers;
        
        $application = DB::transaction( function() use ( $request, $user, $approvers ) {

            //　休暇申請レコードの登録
            //
            $application = Application::create( $request, $user );            

            //　休暇DBの登録
            //
            $vacation = Vacation::create_application( $application );
            
            //　承認者の登録
            //
            $approvers = $request->approvers;
            Approval::create( $application, $approvers );

            return $application;
        });
        
        $approvers = $application->approvers;
        
        // 　承認者へ通知
        //        
        // dd( $application );
        $users = User::whereIn( 'id', $approvers )->get();
        $notice = new NoticeApplication( $application );
        foreach( $users as $user ) {
            if( $user->hasEmail() ) {
                $user->notify( $notice );
            }
            
        }
        // dd( $notice );

        //  完了画面へ　
        //
        #dd( $application );
        Session::flash( 'flash_message', "休暇申請の登録を完了しました。" );
        Session::flash( 'previous_route', Route::currentRouteName() );
        // 戻るボタン対策
        session()->regenerateToken(); 
        BackButton::removePreviousSession();
        return redirect()->route( 'vacation.application.show' ,['application'=> $application ] );
        // return redirect()->route( 'vacation.a/pplication.create_hourly');
                  
    }
    
    // 詳細表示
    //
    public function show( Request $request, Application $application ) {
        BackButton::stackHere( request() );
        return View( 'vacation.application.show' )->with( 'application', $application );
    }
    public function show_m( Request $request ) {
        // dd( $request );
        if( ! isset( $request->application )) { abort( 'Error : ApplicationController@show_m : 1'); }
        return redirect()->route( 'vacation.application.show', [ 'application' => $request->application ] );
    }
    
    //  申請一覧表示（従業員画面）
    public function index( Request $request ) {
        
        if( !isset( $request['find'] )) {
            //
            // 検索の初期設定
            //
            $find = [ 'pagination' => 10,
                      'status' => ['承認待ち', '承認',  ],
                    ]; 
        } else {
            $find = $request['find'];
        }
        //　ログインユーザの申請休暇のみを検索
        //
        $query = Application::with( ['applicant', 'approvals'] )->where( 'user_id', Auth::guard('user')->user()->id );

        if( isset( $find['off_date'] )) {
            $query = $query->where( 'start_date', '<=', $find['off_date'] )
                           ->where( 'end_date'  , '>=', $find['off_date'] );
        }
        if( isset( $find['status'] )) {
            $query = $query->whereIn( 'status', $find['status'] );
            
        }
        $applications = $query->paginate( $find['pagination'] );
        $applications->appends( [ 'find' => $find ] );

        // 承認済みの申請があれば、完了処理を促すメッセージを表示
        //
        foreach( $applications as $app ) {
            if( $app->status == "承認" ) {
                Session::flash( "info_message", "承認済みの休暇申請があります。休暇を取得したら完了処理をしてください。<BR>
                                                 <font color='red'>完了処理をしないと総務で休暇取得状況を把握できず、欠勤扱いになります。</font>" );
                break;
            }
            
        }
        // dd( $applications);
        
        //　Rootルートの設定
        //
        // if( $request->has('root_route') or session('root_route_name') == Route::currentRouteName() ) {
        //     // session( [ 'root_route_name' => Route::currentRouteName(), 
        //     //           'root_url'        => route('vacation.application.index', $request->input('find') ) ]);
        //     BackButton::setHere( $request );
        // }
        BackButton::setHere( $request );
        // dump( $request );
        return View( 'vacation.application.index' )->with( 'applications', $applications )
                                          ->with( 'find', $find );

    }
    
    //  休暇取得完了（確認画面）
    public function process( Application $application ) {
        
        //  リロード対策
        //
        if( $application->status != "承認" ) {
            Session::flash( 'previous_route', Route::currentRouteName() );
            #return redirect()->route( 'user.show', [ 'user' => $Auth::user_id ]);
            return redirect()->route( 'home' );
        }
        BackButton::stackHere( request() );
        return View( 'vacation.application.process' )->with( 'application', $application );
        
    }

    //  休暇取得完了（完了）
    public function processed( Application $application ) {
        
        //  リロード対策
        //
        if( $application->status != "承認" ) {
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
        }
        
        $return = DB::transaction( function() use( $application ) {

            $application->status = "休暇取得完了";
            $application->save();

            Vacation::create_completion( $application );
        
            // if( $application->type == "有給休暇" ) {
            //     $paidleave = $application->paidleave;
            //     $paidleave->recalculate();
            // }
            return $application;
            
        });
        Session::flash( 'flash_message', "休暇取得処理を完了しました。" );
        Session::flash( 'previos_route', Route::currentRouteName() );
        
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route( 'vacation.application.show', ['application' => $return ]);
        
    }
    //  申請取り下げ（確認画面）
    public function drop( Application $application ) {
        
        //  リロード対策
        //
        #dd( $application );
        if( $application->status != '承認待ち' and $application->status != '承認' ) {
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
        }
        Session::flash( 'previous_route', Route::currentRouteName() );
        BackButton::stackHere( request() );
        return View( 'vacation.application.drop' )->with( 'application', $application );
    }
    
    //  申請取り下げ（完了処理）
    public function dropped( Application $application ) {

        //  リロード対策
        //
        if( $application->status != '承認待ち' and $application->status != '承認' ) {
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
        }

        DB::transaction( function() use( $application ) {

            //　承認DBの更新
            // 
            $approvals = $application->approvals;
            // dd( $approvals );
            foreach( $approvals as $app ) {
                if( $app->status == "承認待ち" ) {
                    $app->status = "取り下げ";
                    $app->save();
                }
            }

            //　休暇DBの更新
            //
            if( $application->status == "承認待ち") {
                $vacation = Vacation::create_cancellation( $application );
            } elseif( $application->status == "承認" ) {
                $vacation = Vacation::create_approved_application_cancellation( $application );
            } else {
                abort( 'ApplicationController.dropped ');
            }

            //　申請DBの更新
            //
            $application->status = "取り下げ";
            $application->save();
      
        });        
        
        Session::flash( 'flash_message', "取り下げをしました。" );
        Session::flash( 'previous_route', Route::currentRouteName() );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route( 'vacation.application.show', ['application' => $application ]);
    }

    //  承認済み休暇が、完了処理されいているか確認
    public function checkProcessed( Request $request ) {
        
        $find = optional( $request )->find;
        if( empty( $find['end_date'])) {
            $find['end_date'] = new Carbon( 'today' );
        }
        
        $applications = Application::getIncomplited( $find );
        // dd( $applications, $find, compact( [ 'applications' , 'find'] ) );
        BackButton::setHere( $request );
        return View( 'vacation.application.checkProcessed', compact( [ 'applications' , 'find'] ));
        
    } 
    
    //　休暇取得完了済み分の削除処理（管理者のみ）
    public function delete_complete( Application $application ) {
        
        // Session::flash( 'flash_message', '間違いなければ、取り下げ確認をチェックして、取り下げ実行ボタンを押してください。');
        BackButton::stackHere( request() );
        return view( 'vacation.application.delete_complete')->with( 'application', $application );
    }

    public function deleted_complete( Application $application, Request $request ) {
        // dd( $application);
        $validator = Validator::make( $request->all(), [ 'delete_comfirm' => 'required' ] );
        if( $validator->fails() ) {
            Session::flash( 'flash_message', '削除確認チェックが入っていません');
            return redirect()->route( 'vacation.application.delete_complete', [ 'application' => $application ])
                             ->withErrors( $validator )
                             ->withInput();
        }
        
        if( $application->status != "休暇取得完了") { abort( 500, 'ApplicationController:deleted_complete : Error'); }
        
        DB::transaction( function() use( $application ) {

            //　休暇DBの更新
            //
            $vacation = Vacation::create_delete_complete( $application );

            //　申請DBの更新
            //
            $application->reason = $application->reason."(管理者による休暇取り下げ）";
            $application->status = "取り下げ";
            $application->save();
        });        
        
        Session::flash( 'flash_message', "取得完了済み休暇を削除しました" );
        Session::flash( 'previous_route', Route::currentRouteName() );
        
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route( 'vacation.application.show', ['application' => $application ]);
        
        
    }

    //　未完申請に対して処理メールを送る
    public function notifyIncompleted( Request $request ) {
        
        $applications = Application::getIncomplited( $request->find );
        
        foreach( $applications as $app ) {
            $app->notifyIncompleted();
        }
        Session::flash( 'flash_message', "催促メールの送信処理を行いました。");
        // return BackButton::backToThere();
        return redirect()->route( 'vacation.application.checkProcessed' );

    } 
    
}
