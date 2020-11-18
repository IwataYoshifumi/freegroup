<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Paidleave;
use App\Http\Helpers\BackButton;

use App\Http\Controllers\Vacation\Events\ApplicationApproved;
use App\Http\Controllers\Vacation\Events\ApplicationRejected;

class ApprovalController extends Controller
{
    //　承認一覧画面
    //
    public function index( Request $request ) {
        
        $user = User::find( Auth::guard('user')->user()->id );
    
        if( !isset( $request['find'] )) { 
            $find = [ 'pagination' => 10, 'status' => '']; 
        } else {
            $find = $request['find'];
        }
        
        $query = Approval::where( 'approver_id', $user->id );
        #$query = Approval::where( 'approver_id', '>=', 1 );

        if( ! empty( $find['status']) ){
            $query = $query->where( 'status', $find['status'] );
        }
        if( ! empty( $find['date']) && ! empty( $find['date_operator'])) {
            $query = $query->where( 'date', $find['date_operator'], $find['date'] );
            
        }

        //　申請者で検索（今後対応）
        //
        if( ! empty( $find['applicant_name']) ) {
            $users = User::select( 'id' )->where( 'name', 'like', '%'.$find['applicant_name'].'%' )->get()->toArray();

        }

        $approvals = $query->paginate( $find['pagination'] );
        
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        
        return View( 'vacation.approval.index' )->with( 'approvals', $approvals )
                                                ->with( 'user', $user )
                                                ->with( 'find', $find );
    }
    
    //　承認選択画面
    //
    // public function select( User $user, Request $request ) {
    public function select( Request $request ) {
    
        $user = User::find( Auth::guard('user')->user()->id );
    
        if( !isset( $request['find'] )) { 
            $find = [ 'pagination' => 10, 'aluser_name' => '',  ]; 
        } else {
            $find = $request['find'];
        }
        
        $query =   DB::table( 'approvals as ar')
                    ->join( 'applications as al', 'al.id',     '=', 'ar.application_id')
                    ->join( 'users as al_user'  , 'al.user_id','=', 'al_user.id'  )
                    ->join( 'users as ar_user'  , 'ar.approver_id','=', 'ar_user.id' )
                    ->select( 'ar.id', 'al.id as al_id', 'ar.status as ar_status', 
                                'al.status as al_status', 'al_user.name as aluser_name', 'ar_user.name as aruser_name',
                                'al.date as al_date', 'al.start_date', 'al.end_date', 'al.num', 'al.type', 'al.reason'
                                
                                )
                    ->where( 'al.status', '=', '承認待ち' )
                    ->where( 'ar.status', '=', '承認待ち' )
                    ->where( 'ar.approver_id', '=', $user->id );
                  
        if( $find['aluser_name'] != "" ) {
            $query->where( 'al_user.name', 'like', '%'.$find['aluser_name'].'%' );
        } 
        
        #$approvals = $query->paginate( $find['pagination'] );
        $approvals = $query->get();

        //　戻るボタンの設定
        //
        BackButton::setHere( $request );

        return View( 'vacation.approval.select' )->with( 'approvals', $approvals )
                                        ->with( 'user', $user )
                                        ->with( 'find', $find );
    }
    
    // 申請確認画面
    //
    public function show( Approval $approval ) {
        
        return view( 'vacation.approval.show' )->with( 'approval', $approval );

    }
    

    //  休暇承認　確認画面
    //
    public function approve( Approval $approval ) {

        // リロード、バック対策処理
        //
        if( $approval->status != "承認待ち" ) {
            
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
            
        }

        $url = ['approve' => route( 'vacation.approval.approve', ['approval'=> $approval] ),
                'reject'  => "" ];
        
        Session::flash( 'flash_message', '承認をします。よろしければ「承認実行」ボタンを押してください。' );
        
        return view( 'vacation.approval.approval' )->with( 'approval', $approval )
                                          ->with( 'url', $url );
    }
    
    
    //  休暇承認　実行
    //
    public function approved( Approval $approval ) {
        
        // リロード、バック対策処理
        //
        if( $approval->status != "承認待ち" or $approval->application->status != "承認待ち" ) {
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
        }


        DB::transaction( function() use( $approval ) {
            
            $application = $approval->application;

            //　申請を承認する
            //
            $today = new Carbon('today', 'Asia/Tokyo');
            $approval->status = "承認";
            $approval->date = $today->toDateString();
            $approval->save();

            //　全て承認が下りれば、承認処理をする
            //
            if( $application->is_all_approvals_approved() ){ 
                //　申請の承認処理
                //
                $application->approve();

                //　申請者へ承認された旨を通知
                //
                event( new ApplicationApproved( $application ));
            }
        });

        Session::flash( 'flash_message', "承認しました。" );
        // 戻るボタン対策
        session()->regenerateToken();
        return redirect()->route( 'vacation.approval.show' ,['approval' => $approval ] );
    }
    
    //  却下　確認画面
    //
    public function reject( Approval $approval ) {
        
        //　DBチェック
        //
        if( $approval->status != "承認待ち" or $approval->application->status != "承認待ち" ) {           
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
        }
        
        $flash_message = "却下する場合は、却下完了ボタンを押してください。";

        Session::flash( 'flash_message', $flash_message ); 
        return view( 'vacation.approval.reject' )->with( 'approval', $approval );
                                           
    }
    
    //  却下　実行
    //
    public function rejected( Approval $approval ) {

        //　DBチェック
        //
        if( $approval->status != "承認待ち" or $approval->application->status != "承認待ち" ) {           
            Session::flash( 'previous_route', Route::currentRouteName() );
            return redirect()->route( 'home' );
        }
        
        DB::transaction( function() use( $approval ) {
            $application = $approval->application;
            $approvals   = $application->approvals;

            //　申請の却下処理
            //
            $application->reject( $approval );

            //　申請者へ却下された旨を通知
            //
            event( new ApplicationRejected( $application ));
            
        });

        $flash_message = "休暇申請を却下しました。";
        Session::flash( 'flash_message', $flash_message ); 
        // 戻るボタン対策
        session()->regenerateToken();
        return redirect()->route( 'vacation.approval.show', [ 'approval' => $approval ] );
                                    
    }
}
